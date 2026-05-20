<?php

namespace App\Console\Commands;

use App\Mail\CobrancaVencida;
use App\Mail\CobrancaVencimentoHoje;
use App\Mail\CobrancaVencimentoProximo;
use App\Models\FinanceiroLancamento;
use App\Models\Mensalidade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificarVencimentosMensalidades extends Command
{
    protected $signature   = 'app:verificar-vencimentos-mensalidades';
    protected $description = 'Verifica vencimentos de mensalidades: avisa clientes e marca vencidas';

    public function handle(): int
    {
        $hoje    = now()->toDateString();
        $em5dias = now()->addDays(5)->toDateString();
        $em3dias = now()->addDays(3)->toDateString();
        $avisos  = 0;
        $vencidas = 0;

        Mensalidade::whereIn('status', ['pendente', 'parcial'])
            ->whereNull('deleted_at')
            ->with(['cliente', 'contratoMensal'])
            ->each(function (Mensalidade $m) use ($hoje, $em5dias, $em3dias, &$avisos, &$vencidas) {
                $vcto          = $m->vencimento->toDateString();
                $temEmail      = !empty($m->cliente?->email);
                $envioAtivo    = (bool) ($m->contratoMensal?->envio_automatico ?? false);
                $deveEnviar    = $temEmail && $envioAtivo;

                // Vencimento em 5 dias
                if ($vcto === $em5dias && !$m->notificado_5dias) {
                    $m->update(['notificado_5dias' => true]);
                    Log::info("Cobrança pendente: mensalidade #{$m->id} vence em 5 dias ({$vcto})");
                    $avisos++;

                    if ($deveEnviar) {
                        try {
                            Mail::to($m->cliente->email)
                                ->send(new CobrancaVencimentoProximo($m, 5));
                            Log::info("E-mail 5 dias enviado: mensalidade #{$m->id}");
                        } catch (\Exception $e) {
                            Log::error("Falha e-mail mensalidade #{$m->id}: {$e->getMessage()}");
                        }
                    }
                }

                // Vencimento em 3 dias
                if ($vcto === $em3dias && !$m->notificado_3dias) {
                    $m->update(['notificado_3dias' => true]);
                    Log::info("Aviso ao cliente: mensalidade #{$m->id} vence em 3 dias ({$vcto})");
                    $avisos++;

                    if ($deveEnviar) {
                        try {
                            Mail::to($m->cliente->email)
                                ->send(new CobrancaVencimentoProximo($m, 3));
                            Log::info("E-mail 3 dias enviado: mensalidade #{$m->id}");
                        } catch (\Exception $e) {
                            Log::error("Falha e-mail mensalidade #{$m->id}: {$e->getMessage()}");
                        }
                    }
                }

                // Vencimento hoje
                if ($vcto === $hoje && !$m->notificado_vencimento) {
                    $m->update(['notificado_vencimento' => true]);
                    Log::info("Vencimento hoje: mensalidade #{$m->id}");
                    $avisos++;

                    if ($deveEnviar) {
                        try {
                            Mail::to($m->cliente->email)
                                ->send(new CobrancaVencimentoHoje($m));
                            Log::info("E-mail vencimento hoje enviado: mensalidade #{$m->id}");
                        } catch (\Exception $e) {
                            Log::error("Falha e-mail mensalidade #{$m->id}: {$e->getMessage()}");
                        }
                    }
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
                            ->where('status', 'previsto')
                            ->where('vencimento', '<', $hoje)
                            ->update(['status' => 'atrasado', 'updated_at' => now()]);
                    }

                    Log::info("Vencida: mensalidade #{$m->id} (vcto {$vcto})");
                    $vencidas++;

                    if ($deveEnviar) {
                        try {
                            Mail::to($m->cliente->email)
                                ->send(new CobrancaVencida($m));
                            Log::info("E-mail vencida enviado: mensalidade #{$m->id}");
                        } catch (\Exception $e) {
                            Log::error("Falha e-mail mensalidade #{$m->id}: {$e->getMessage()}");
                        }
                    }
                }
            });

        $this->info("✓ {$avisos} aviso(s) gerado(s), {$vencidas} mensalidade(s) marcada(s) como vencida.");

        return self::SUCCESS;
    }
}
