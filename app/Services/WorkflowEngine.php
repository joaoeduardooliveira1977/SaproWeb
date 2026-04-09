<?php

namespace App\Services;

use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Models\WorkflowExecucao;
use App\Models\WorkflowRegra;
use App\Workflow\Acoes\AcaoChamarIA;
use App\Workflow\Acoes\AcaoCriarAgenda;
use App\Workflow\Acoes\AcaoCriarNotificacao;
use App\Workflow\Acoes\AcaoCriarPrazo;
use App\Workflow\Acoes\AcaoAtualizarScore;
use App\Workflow\Acoes\AcaoEnviarWhatsapp;
use App\Workflow\Contrato\AcaoInterface;
use Illuminate\Support\Facades\Log;

class WorkflowEngine
{
    // ── Mapa tipo → classe de ação ────────────────────────────────
    private array $acaoMap = [
        WorkflowRegra::ACAO_CRIAR_PRAZO       => AcaoCriarPrazo::class,
        WorkflowRegra::ACAO_CRIAR_NOTIFICACAO => AcaoCriarNotificacao::class,
        WorkflowRegra::ACAO_CRIAR_AGENDA      => AcaoCriarAgenda::class,
        WorkflowRegra::ACAO_ENVIAR_WHATSAPP   => AcaoEnviarWhatsapp::class,
        WorkflowRegra::ACAO_ATUALIZAR_SCORE   => AcaoAtualizarScore::class,
        WorkflowRegra::ACAO_CHAMAR_IA         => AcaoChamarIA::class,
    ];

    // ── Ponto de entrada principal ────────────────────────────────

    /**
     * Processa todas as regras ativas para um dado gatilho e processo.
     *
     * @param  string   $gatilho  Ex: WorkflowRegra::GATILHO_ANDAMENTO_CRIADO
     * @param  Processo $processo Processo que originou o evento
     * @param  array    $payload  Dados do evento (andamento_id, andamento_descricao, etc.)
     */
    public function processar(string $gatilho, Processo $processo, array $payload = []): void
    {
        $regras = WorkflowRegra::ativas()
            ->porGatilho($gatilho)
            ->with('acoes')
            ->get();

        foreach ($regras as $regra) {
            $this->processarRegra($regra, $processo, $payload);
        }
    }

    // ── Processamento de uma regra ────────────────────────────────

    private function processarRegra(WorkflowRegra $regra, Processo $processo, array $payload): void
    {
        // 1. Avalia condições
        if (!$this->avaliarCondicoes($regra->condicoes ?? [], $processo, $payload)) {
            $this->registrarExecucao($regra, $processo, $payload, WorkflowExecucao::STATUS_IGNORADO, []);
            return;
        }

        // 2. Executa ações em ordem
        $resultados = [];

        foreach ($regra->acoes as $acao) {
            try {
                $handler    = $this->resolverAcao($acao->tipo);
                $resultado  = $handler->executar($acao, $processo, $payload);
                $resultados[] = ['acao_id' => $acao->id, 'tipo' => $acao->tipo, 'resultado' => $resultado];
            } catch (\Throwable $e) {
                Log::error('WorkflowEngine: erro ao executar ação', [
                    'regra_id'  => $regra->id,
                    'acao_id'   => $acao->id,
                    'tipo'      => $acao->tipo,
                    'processo'  => $processo->id,
                    'erro'      => $e->getMessage(),
                ]);

                $this->registrarExecucao(
                    $regra, $processo, $payload,
                    WorkflowExecucao::STATUS_ERRO,
                    $resultados,
                    "[Ação {$acao->tipo}] {$e->getMessage()}"
                );
                return; // Interrompe a sequência de ações se uma falhar
            }
        }

        // 3. Registra execução bem-sucedida
        $this->registrarExecucao($regra, $processo, $payload, WorkflowExecucao::STATUS_EXECUTADO, $resultados);
        $regra->incrementarExecucoes();
    }

    // ── Avaliação de condições ────────────────────────────────────

    /**
     * Avalia todas as condições (AND lógico).
     *
     * Cada condição: { "campo": "andamento.descricao", "op": "contem", "valor": "intimação" }
     *
     * Campos suportados:
     *   andamento.descricao, processo.status, processo.score,
     *   processo.tipo_acao_id, processo.fase_id, processo.advogado_id
     */
    private function avaliarCondicoes(array $condicoes, Processo $processo, array $payload): bool
    {
        foreach ($condicoes as $condicao) {
            if (!$this->avaliarCondicao($condicao, $processo, $payload)) {
                return false;
            }
        }
        return true;
    }

    private function avaliarCondicao(array $condicao, Processo $processo, array $payload): bool
    {
        $campo    = $condicao['campo']    ?? '';
        $operador = $condicao['op']       ?? 'igual';
        $valor    = $condicao['valor']    ?? '';

        $valorReal = $this->resolverCampo($campo, $processo, $payload);

        return match ($operador) {
            'igual'       => mb_strtolower((string) $valorReal) === mb_strtolower((string) $valor),
            'diferente'   => mb_strtolower((string) $valorReal) !== mb_strtolower((string) $valor),
            'contem'      => str_contains(mb_strtolower((string) $valorReal), mb_strtolower((string) $valor)),
            'nao_contem'  => !str_contains(mb_strtolower((string) $valorReal), mb_strtolower((string) $valor)),
            'maior_que'   => (float) $valorReal > (float) $valor,
            'menor_que'   => (float) $valorReal < (float) $valor,
            'vazio'       => empty($valorReal),
            'nao_vazio'   => !empty($valorReal),
            default       => false,
        };
    }

    /**
     * Resolve o valor de um campo composto (modelo.campo).
     */
    private function resolverCampo(string $campo, Processo $processo, array $payload): mixed
    {
        return match ($campo) {
            'andamento.descricao'   => $payload['andamento_descricao'] ?? '',
            'processo.status'       => $processo->status ?? '',
            'processo.score'        => $processo->score ?? '',
            'processo.tipo_acao_id' => $processo->tipo_acao_id ?? '',
            'processo.fase_id'      => $processo->fase_id ?? '',
            'processo.advogado_id'  => $processo->advogado_id ?? '',
            'processo.numero'       => $processo->numero ?? '',
            default                 => '',
        };
    }

    // ── Resolução de ação ─────────────────────────────────────────

    private function resolverAcao(string $tipo): AcaoInterface
    {
        $classe = $this->acaoMap[$tipo] ?? null;

        if (!$classe) {
            throw new \InvalidArgumentException("Tipo de ação desconhecido: {$tipo}");
        }

        return app($classe);
    }

    // ── Log de execução ───────────────────────────────────────────

    private function registrarExecucao(
        WorkflowRegra $regra,
        Processo      $processo,
        array         $payload,
        string        $status,
        array         $resultados,
        ?string       $erroMensagem = null
    ): void {
        WorkflowExecucao::create([
            'tenant_id'       => $processo->tenant_id,
            'regra_id'        => $regra->id,
            'processo_id'     => $processo->id,
            'gatilho_payload' => $payload,
            'status'          => $status,
            'resultado'       => $resultados ?: null,
            'erro_mensagem'   => $erroMensagem,
        ]);
    }
}
