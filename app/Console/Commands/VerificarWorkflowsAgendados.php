<?php

namespace App\Console\Commands;

use App\Jobs\ExecutarWorkflow;
use App\Models\Prazo;
use App\Models\Processo;
use App\Models\WorkflowRegra;
use Illuminate\Console\Command;

/**
 * Dispara gatilhos de workflow baseados em tempo:
 *
 *   prazo.vencendo          → processos com prazo abrindo nos próximos N dias
 *   prazo.vencido           → processos com prazo vencido ainda aberto
 *   processo.sem_andamento  → processos sem andamento há X dias (por regra)
 *
 * Deve rodar diariamente via schedule (ver routes/console.php).
 */
class VerificarWorkflowsAgendados extends Command
{
    protected $signature   = 'workflow:verificar-agendados';
    protected $description = 'Dispara gatilhos de workflow baseados em tempo (prazos, inatividade)';

    public function handle(): int
    {
        $this->info('[Workflow] Iniciando verificação agendada — ' . now()->toDateTimeString());

        $this->verificarPrazos();
        $this->verificarSemAndamento();

        $this->info('[Workflow] Concluído.');
        return self::SUCCESS;
    }

    // ── Gatilhos de prazo ─────────────────────────────────────────

    private function verificarPrazos(): void
    {
        // prazo.vencendo — prazos abertos que vencem nos próximos 7 dias
        $vencendo = Prazo::where('status', 'aberto')
            ->whereDate('data_prazo', '>=', now()->toDateString())
            ->whereDate('data_prazo', '<=', now()->addDays(7)->toDateString())
            ->get();

        $this->line("  prazo.vencendo: {$vencendo->count()} prazo(s)");

        foreach ($vencendo as $prazo) {
            ExecutarWorkflow::paraPrazo(
                WorkflowRegra::GATILHO_PRAZO_VENCENDO,
                $prazo->processo_id,
                [
                    'prazo_id'       => $prazo->id,
                    'prazo_titulo'   => $prazo->titulo,
                    'prazo_data'     => $prazo->data_prazo->toDateString(),
                    'dias_restantes' => $prazo->diasRestantes(),
                    'prazo_fatal'    => $prazo->prazo_fatal,
                ]
            );
        }

        // prazo.vencido — prazos abertos que já venceram
        $vencidos = Prazo::where('status', 'aberto')
            ->whereDate('data_prazo', '<', now()->toDateString())
            ->get();

        $this->line("  prazo.vencido: {$vencidos->count()} prazo(s)");

        foreach ($vencidos as $prazo) {
            ExecutarWorkflow::paraPrazo(
                WorkflowRegra::GATILHO_PRAZO_VENCIDO,
                $prazo->processo_id,
                [
                    'prazo_id'     => $prazo->id,
                    'prazo_titulo' => $prazo->titulo,
                    'prazo_data'   => $prazo->data_prazo->toDateString(),
                    'dias_atraso'  => abs($prazo->diasRestantes()),
                    'prazo_fatal'  => $prazo->prazo_fatal,
                ]
            );
        }
    }

    // ── Gatilho de inatividade ────────────────────────────────────

    private function verificarSemAndamento(): void
    {
        // Busca todas as regras ativas com gatilho sem_andamento_dias
        $regras = WorkflowRegra::ativas()
            ->porGatilho(WorkflowRegra::GATILHO_SEM_ANDAMENTO_DIAS)
            ->get();

        foreach ($regras as $regra) {
            $dias = (int) ($regra->gatilho_config['dias'] ?? 30);

            // Processos ativos cujo último andamento foi há mais de $dias dias
            $processos = Processo::where('status', 'Ativo')
                ->whereHas('andamentos', function ($q) use ($dias) {
                    $q->where('created_at', '<=', now()->subDays($dias));
                })
                ->whereDoesntHave('andamentos', function ($q) use ($dias) {
                    $q->where('created_at', '>', now()->subDays($dias));
                })
                ->get();

            $this->line("  processo.sem_andamento_dias ({$dias}d) — regra #{$regra->id}: {$processos->count()} processo(s)");

            foreach ($processos as $processo) {
                ExecutarWorkflow::paraPrazo(
                    WorkflowRegra::GATILHO_SEM_ANDAMENTO_DIAS,
                    $processo->id,
                    ['dias_sem_andamento' => $dias]
                );
            }
        }
    }
}
