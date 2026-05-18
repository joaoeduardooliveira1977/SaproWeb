<?php

namespace App\Console\Commands;

use App\Models\FinanceiroLancamento;
use App\Models\Mensalidade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerificarVencimentosMensalidades extends Command
{
    protected $signature   = 'app:verificar-vencimentos-mensalidades';
    protected $description = 'Verifica vencimentos de mensalidades: avisa clientes e marca vencidas';

    public function handle(): int
    {
        $hoje      = now()->toDateString();
        $em5dias   = now()->addDays(5)->toDateString();
        $em3dias   = now()->addDays(3)->toDateString();
        $avisos    = 0;
        $vencidas  = 0;

        Mensalidade::whereIn('status', ['pendente', 'parcial'])
            ->whereNull('deleted_at')
            ->each(function (Mensalidade $m) use ($hoje, $em5dias, $em3dias, &$avisos, &$vencidas) {
                $vcto = $m->vencimento->toDateString();

                // Vencimento em 5 dias
                if ($vcto === $em5dias && !$m->notificado_5dias) {
                    $m->update(['notificado_5dias' => true]);
                    Log::info("Cobrança pendente: mensalidade #{$m->id} vence em 5 dias ({$vcto})");
                    $avisos++;
                }

                // Vencimento em 3 dias
                if ($vcto === $em3dias && !$m->notificado_3dias) {
                    $m->update(['notificado_3dias' => true]);
                    Log::info("Aviso ao cliente: mensalidade #{$m->id} vence em 3 dias ({$vcto})");
                    $avisos++;
                }

                // Vencimento hoje
                if ($vcto === $hoje && !$m->notificado_vencimento) {
                    $m->update(['notificado_vencimento' => true]);
                    Log::info("Vencimento hoje: mensalidade #{$m->id}");
                    $avisos++;
                }

                // Vencida (status=pendente, vencimento < hoje)
                if ($vcto < $hoje && $m->status === 'pendente') {
                    $m->update([
                        'status'            => 'vencido',
                        'notificado_atraso' => true,
                    ]);

                    if ($m->financeiro_lancamento_id) {
                        FinanceiroLancamento::withoutGlobalScopes()
                            ->where('id', $m->financeiro_lancamento_id)
                            ->update(['status' => 'atrasado', 'updated_at' => now()]);
                    }

                    Log::info("Vencida: mensalidade #{$m->id} (vcto {$vcto})");
                    $vencidas++;
                }
            });

        $this->info("✓ {$avisos} aviso(s) gerado(s), {$vencidas} mensalidade(s) marcada(s) como vencida.");

        return self::SUCCESS;
    }
}
