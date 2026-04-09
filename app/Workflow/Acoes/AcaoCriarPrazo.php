<?php

namespace App\Workflow\Acoes;

use App\Models\Prazo;
use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Cria um Prazo vinculado ao processo.
 *
 * Config JSON esperada:
 * {
 *   "titulo":          "Resposta à Intimação",         // obrigatório
 *   "descricao":       "Gerado automaticamente",        // opcional
 *   "tipo":            "Prazo processual",              // opcional
 *   "dias":            15,                              // obrigatório
 *   "tipo_contagem":   "uteis",                        // "uteis" | "corridos" (default: uteis)
 *   "prazo_fatal":     true,                           // default: false
 *   "responsavel":     "advogado_processo"             // "advogado_processo" | user_id numérico
 * }
 */
class AcaoCriarPrazo implements AcaoInterface
{
    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg = $acao->config ?? [];

        $titulo        = $cfg['titulo']        ?? 'Prazo gerado automaticamente';
        $descricao     = $cfg['descricao']     ?? null;
        $tipo          = $cfg['tipo']          ?? 'Prazo processual';
        $dias          = (int) ($cfg['dias']   ?? 15);
        $tipoContagem  = $cfg['tipo_contagem'] ?? 'uteis';
        $prazoFatal    = (bool) ($cfg['prazo_fatal'] ?? false);

        // Resolve responsável
        $responsavelId = $this->resolverResponsavel($cfg['responsavel'] ?? 'advogado_processo', $processo);

        // Data base: hoje
        $dataInicio = now()->toDateString();
        $dataPrazo  = Prazo::calcularData($dataInicio, $dias, $tipoContagem);

        // Substitui variáveis no título (ex: {numero})
        $titulo = $this->interpolar($titulo, $processo, $payload);

        $prazo = Prazo::create([
            'tenant_id'     => $processo->tenant_id,
            'processo_id'   => $processo->id,
            'responsavel_id'=> $responsavelId,
            'criado_por'    => $responsavelId,
            'titulo'        => $titulo,
            'descricao'     => $descricao ? $this->interpolar($descricao, $processo, $payload) : null,
            'tipo'          => $tipo,
            'data_inicio'   => $dataInicio,
            'tipo_contagem' => $tipoContagem,
            'dias'          => $dias,
            'data_prazo'    => $dataPrazo->toDateString(),
            'prazo_fatal'   => $prazoFatal,
            'status'        => 'aberto',
        ]);

        return ['prazo_id' => $prazo->id, 'data_prazo' => $prazo->data_prazo];
    }

    private function resolverResponsavel(string|int $responsavel, Processo $processo): ?int
    {
        if (is_numeric($responsavel)) {
            return (int) $responsavel;
        }

        if ($responsavel === 'advogado_processo') {
            return $processo->advogado_id;
        }

        return null;
    }

    private function interpolar(string $texto, Processo $processo, array $payload): string
    {
        $andamentoDescricao = $payload['andamento_descricao'] ?? '';

        return str_replace(
            ['{numero}', '{cliente}', '{andamento_descricao}'],
            [$processo->numero ?? '', $processo->cliente?->nome ?? '', $andamentoDescricao],
            $texto
        );
    }
}
