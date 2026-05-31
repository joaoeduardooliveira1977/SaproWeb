<?php

namespace App\Jobs;

use App\Models\{Comunicado, Tenant};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\{Log, Mail};

class EnviarEmailComunicado implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly Comunicado $comunicado) {}

    public function handle(): void
    {
        $c = $this->comunicado;

        // Determina quais tenants recebem o e-mail
        $tenants = match($c->destino) {
            'tenant_especifico' => Tenant::where('ativo', true)->where('id', $c->tenant_id)->get(),
            'plano_especifico'  => Tenant::where('ativo', true)->where('plano', $c->plano)->get(),
            default             => Tenant::where('ativo', true)->get(),
        };

        $ti     = $c->tipoInfo();
        $assunto = "[Software Jurídico] {$c->titulo}";
        $erros  = 0;

        foreach ($tenants as $tenant) {
            if (!$tenant->email) continue;

            $corpo = "
            <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;'>
                <div style='background:#0f2540;padding:24px 32px;border-radius:8px 8px 0 0;'>
                    <div style='color:#c9a84c;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:6px;'>Software Jurídico</div>
                    <div style='color:#fff;font-size:20px;font-weight:800;'>{$ti['icon']} {$c->titulo}</div>
                </div>
                <div style='background:#fff;border:1px solid #e2e8f0;border-top:none;padding:28px 32px;border-radius:0 0 8px 8px;'>
                    <p style='font-size:14px;color:#475569;line-height:1.7;white-space:pre-line;'>{$c->mensagem}</p>
                    <div style='margin-top:24px;padding-top:16px;border-top:1px solid #e2e8f0;'>
                        <a href='" . config('app.url') . "/status'
                           style='display:inline-block;background:#1a3a5c;color:#fff;padding:10px 22px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:700;'>
                            Ver página de status
                        </a>
                    </div>
                    <p style='font-size:11px;color:#94a3b8;margin-top:16px;'>
                        Este e-mail foi enviado automaticamente para {$tenant->nome}.
                    </p>
                </div>
            </div>";

            try {
                Mail::html($corpo, fn($msg) => $msg->to($tenant->email, $tenant->nome)->subject($assunto));
            } catch (\Exception $e) {
                $erros++;
                Log::warning("EnviarEmailComunicado: falha para {$tenant->email} — {$e->getMessage()}");
            }
        }

        Log::info("EnviarEmailComunicado #{$c->id}: {$tenants->count()} tenants, {$erros} erros.");
    }
}
