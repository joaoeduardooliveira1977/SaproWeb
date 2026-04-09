<?php

namespace App\Workflow\Acoes;

use App\Models\Andamento;
use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Services\AIService;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Chama a IA (Claude/Gemini) e salva o resultado no processo ou andamento.
 *
 * Config JSON esperada:
 * {
 *   "tipo":      "resumo_andamento",     // "resumo_andamento" | "analise_processo" | "classificar_score"
 *   "salvar_em": "andamento.resumo_ia"   // "andamento.resumo_ia" | "processo.resumo_ia" | "processo.analise_ia"
 * }
 *
 * Tipos disponíveis:
 *  - resumo_andamento  → Humaniza a descrição bruta do andamento em 1 frase clara
 *  - analise_processo  → Análise completa RESUMO/RISCO/PRÓXIMOS PASSOS/ALERTAS
 *  - classificar_score → Retorna apenas "critico" | "atencao" | "normal" e atualiza o score
 */
class AcaoChamarIA implements AcaoInterface
{
    public function __construct(private readonly AIService $ai) {}

    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg      = $acao->config ?? [];
        $tipo     = $cfg['tipo']      ?? 'resumo_andamento';
        $salvarEm = $cfg['salvar_em'] ?? 'andamento.resumo_ia';

        $prompt   = $this->montarPrompt($tipo, $processo, $payload);
        $maxTokens = $tipo === 'analise_processo' ? 1500 : 300;

        $resultado = $this->ai->gerar($prompt, $maxTokens);

        if (!$resultado) {
            return ['status' => 'erro', 'motivo' => 'IA não retornou resposta'];
        }

        $resultado = trim($resultado);

        $this->salvarResultado($salvarEm, $resultado, $processo, $payload);

        return ['status' => 'ok', 'tipo' => $tipo, 'salvo_em' => $salvarEm, 'chars' => strlen($resultado)];
    }

    // ── Prompts por tipo ──────────────────────────────────────────

    private function montarPrompt(string $tipo, Processo $processo, array $payload): string
    {
        $descricao = $payload['andamento_descricao'] ?? '';
        $numero    = $processo->numero ?? '';
        $cliente   = $processo->cliente?->nome ?? '';

        return match ($tipo) {
            'resumo_andamento' => <<<PROMPT
                Reescreva o andamento processual abaixo em linguagem clara e objetiva para um advogado,
                em no máximo 1 frase (até 100 caracteres). Sem introdução, sem aspas.

                Andamento original: {$descricao}
                PROMPT,

            'classificar_score' => <<<PROMPT
                Classifique a urgência do andamento abaixo como exatamente uma das opções:
                critico | atencao | normal

                Responda SOMENTE com a palavra, sem pontuação.

                Processo: {$numero} — Cliente: {$cliente}
                Andamento: {$descricao}
                PROMPT,

            'analise_processo' => $this->promptAnaliseCompleta($processo),

            default => "Analise o andamento processual: {$descricao}",
        };
    }

    private function promptAnaliseCompleta(Processo $processo): string
    {
        $processo->loadMissing(['tipoAcao', 'fase', 'risco',
            'andamentos' => fn($q) => $q->latest('data')->limit(5)]);

        $andamentos = $processo->andamentos
            ->map(fn($a) => '- [' . ($a->data?->format('d/m/Y') ?? '?') . '] ' . $a->descricao)
            ->join("\n");

        return <<<PROMPT
            Você é um assistente jurídico. Analise o processo abaixo e responda com exatamente estas 4 seções:

            RESUMO: [2-3 linhas]
            RISCO: [Baixo | Médio | Alto — justificativa em 1-2 linhas]
            PRÓXIMOS PASSOS:
            - [ação 1]
            - [ação 2]
            ALERTAS: [alertas importantes ou "Nenhum"]

            Processo: {$processo->numero}
            Cliente: {$processo->cliente?->nome}
            Tipo: {$processo->tipoAcao?->descricao}
            Fase: {$processo->fase?->descricao}
            Risco: {$processo->risco?->descricao}
            Últimos andamentos:
            {$andamentos}
            PROMPT;
    }

    // ── Persistência ──────────────────────────────────────────────

    private function salvarResultado(
        string   $salvarEm,
        string   $resultado,
        Processo $processo,
        array    $payload
    ): void {
        match ($salvarEm) {
            'andamento.resumo_ia' => $this->salvarEmAndamento($resultado, $payload),

            'processo.resumo_ia'  => $processo->update(['resumo_ia'  => $resultado]),

            'processo.analise_ia' => $processo->update([
                'analise_ia'    => $resultado,
                'analise_ia_em' => now(),
            ]),

            'processo.score' => $this->salvarScore($resultado, $processo),

            default => null,
        };
    }

    private function salvarEmAndamento(string $resultado, array $payload): void
    {
        $andamentoId = $payload['andamento_id'] ?? null;
        if (!$andamentoId) return;

        $andamento = Andamento::find($andamentoId);
        $andamento?->update(['resumo_ia' => $resultado]);
    }

    private function salvarScore(string $resultado, Processo $processo): void
    {
        $score = mb_strtolower(trim($resultado));
        if (in_array($score, ['critico', 'atencao', 'normal'], true)) {
            $processo->update(['score' => $score]);
        }
    }
}
