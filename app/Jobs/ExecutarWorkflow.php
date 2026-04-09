<?php

namespace App\Jobs;

use App\Models\Andamento;
use App\Models\Processo;
use App\Models\WorkflowRegra;
use App\Services\WorkflowEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecutarWorkflow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas antes de desistir.
     */
    public int $tries = 3;

    /**
     * Tempo (segundos) entre tentativas após falha.
     */
    public int $backoff = 10;

    public function __construct(
        private readonly string $gatilho,
        private readonly int    $processoId,
        private readonly array  $payload = [],
    ) {}

    public function handle(WorkflowEngine $engine): void
    {
        $processo = Processo::with(['cliente', 'advogado'])->find($this->processoId);

        if (!$processo) {
            Log::warning('ExecutarWorkflow: processo não encontrado', [
                'processo_id' => $this->processoId,
                'gatilho'     => $this->gatilho,
            ]);
            return;
        }

        $engine->processar($this->gatilho, $processo, $this->payload);
    }

    // ── Factories semânticas ──────────────────────────────────────

    /**
     * Dispara o job para o gatilho "andamento.criado".
     */
    public static function paraAndamento(Andamento $andamento): void
    {
        static::dispatch(
            WorkflowRegra::GATILHO_ANDAMENTO_CRIADO,
            $andamento->processo_id,
            [
                'andamento_id'          => $andamento->id,
                'andamento_descricao'   => $andamento->descricao ?? '',
                'andamento_data'        => $andamento->data?->toDateString() ?? now()->toDateString(),
            ]
        )->onQueue('workflows');
    }

    /**
     * Dispara o job para o gatilho "processo.fase_mudou".
     */
    public static function paraFaseMudou(Processo $processo, ?int $faseAnteriorId): void
    {
        static::dispatch(
            WorkflowRegra::GATILHO_FASE_MUDOU,
            $processo->id,
            [
                'fase_anterior_id' => $faseAnteriorId,
                'fase_atual_id'    => $processo->fase_id,
            ]
        )->onQueue('workflows');
    }

    /**
     * Dispara o job para gatilhos baseados em prazo (vencendo / vencido).
     */
    public static function paraPrazo(string $gatilho, int $processoId, array $extra = []): void
    {
        static::dispatch($gatilho, $processoId, $extra)->onQueue('workflows');
    }
}
