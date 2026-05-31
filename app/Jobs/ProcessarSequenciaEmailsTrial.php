<?php

namespace App\Jobs;

use App\Mail\TrialAviso12Dias;
use App\Mail\TrialBoasVindas;
use App\Mail\TrialDica3Dias;
use App\Mail\TrialExpiracao15Dias;
use App\Mail\TrialLembrete7Dias;
use App\Models\Tenant;
use App\Models\TrialEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessarSequenciaEmailsTrial implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // [dias_desde_criacao => tipo]
    private const SEQUENCIA = [
        0  => 'boas_vindas',
        3  => 'dica_3dias',
        7  => 'lembrete_7dias',
        12 => 'aviso_12dias',
        15 => 'expiracao_15dias',
    ];

    public function handle(): void
    {
        $tenants = Tenant::where('plano', 'demo')
            ->whereNotNull('trial_iniciado_em')
            ->where('ativo', true)
            ->get();

        foreach ($tenants as $tenant) {
            $diasDesde = (int) $tenant->trial_iniciado_em->diffInDays(now());

            foreach (self::SEQUENCIA as $dia => $tipo) {
                if ($diasDesde < $dia) continue;

                $jaEnviado = TrialEmail::where('tenant_id', $tenant->id)
                    ->where('tipo', $tipo)
                    ->exists();

                if ($jaEnviado) continue;

                $this->enviarEmail($tenant, $tipo);
            }
        }
    }

    private function enviarEmail(Tenant $tenant, string $tipo): void
    {
        $email = $tenant->email;
        if (!$email) return;

        try {
            $mailable = match($tipo) {
                'boas_vindas'     => new TrialBoasVindas($tenant),
                'dica_3dias'      => new TrialDica3Dias($tenant),
                'lembrete_7dias'  => new TrialLembrete7Dias($tenant),
                'aviso_12dias'    => new TrialAviso12Dias($tenant),
                'expiracao_15dias'=> new TrialExpiracao15Dias($tenant),
                default           => null,
            };

            if (!$mailable) return;

            Mail::to($email)->send($mailable);

            TrialEmail::create([
                'tenant_id'  => $tenant->id,
                'tipo'       => $tipo,
                'enviado_em' => now(),
                'sucesso'    => true,
            ]);
        } catch (\Throwable $e) {
            TrialEmail::create([
                'tenant_id'  => $tenant->id,
                'tipo'       => $tipo,
                'enviado_em' => now(),
                'sucesso'    => false,
            ]);

            \Illuminate\Support\Facades\Log::error("Falha ao enviar email trial {$tipo} para tenant {$tenant->id}: " . $e->getMessage());
        }
    }
}
