<?php

namespace App\Livewire;

use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class VerificarEmail extends Component
{
    public string $d1 = '';
    public string $d2 = '';
    public string $d3 = '';
    public string $d4 = '';
    public string $d5 = '';
    public string $d6 = '';

    public string $erro         = '';
    public bool   $reenvioOk    = false;
    public int    $reenvioAguardar = 0;

    private function usuarioId(): ?int
    {
        return session('verificacao_usuario_id');
    }

    public function mount(): void
    {
        if (!session('verificacao_usuario_id')) {
            $this->redirect(route('cadastro'));
        }
    }

    public function updatedD1(): void { $this->erro = ''; }
    public function updatedD2(): void { $this->erro = ''; }
    public function updatedD3(): void { $this->erro = ''; }
    public function updatedD4(): void { $this->erro = ''; }
    public function updatedD5(): void { $this->erro = ''; }
    public function updatedD6(): void { $this->erro = ''; }

    public function verificar(): void
    {
        $codigo = $this->d1 . $this->d2 . $this->d3 . $this->d4 . $this->d5 . $this->d6;

        if (strlen($codigo) < 6 || !ctype_digit($codigo)) {
            $this->erro = 'Digite os 6 dígitos do código.';
            return;
        }

        $usuarioId = $this->usuarioId();
        if (!$usuarioId) {
            $this->redirect(route('cadastro'));
            return;
        }

        // Limite de tentativas por sessão
        $tentativas = session('verificacao_tentativas', 0);
        if ($tentativas >= 5) {
            $this->invalidarCodigo($usuarioId);
            $this->erro = 'Número máximo de tentativas atingido. Solicite um novo código.';
            return;
        }

        $usuario = Usuario::find($usuarioId);

        if (!$usuario || !$usuario->email_token_verificacao) {
            $this->erro = 'Código inválido ou já utilizado.';
            return;
        }

        if ($usuario->email_token_expira_em && $usuario->email_token_expira_em->isPast()) {
            $this->erro = 'Código expirado. Clique em "Reenviar código" para receber um novo.';
            return;
        }

        if (!Hash::check($codigo, $usuario->email_token_verificacao)) {
            session(['verificacao_tentativas' => $tentativas + 1]);
            $restantes = 4 - $tentativas;
            $this->erro = "Código incorreto. " . ($restantes > 0 ? "Você tem {$restantes} tentativa(s) restante(s)." : 'Limite atingido.');
            return;
        }

        // Código válido — ativa o usuário
        $usuario->update([
            'ativo'                   => true,
            'email_verificado'        => true,
            'email_token_verificacao' => null,
            'email_token_expira_em'   => null,
        ]);

        session()->forget(['verificacao_usuario_id', 'verificacao_email', 'verificacao_tentativas']);

        Auth::guard('usuarios')->login($usuario, remember: true);

        $subdomain = $usuario->tenant?->slug;
        $destino   = $subdomain
            ? "https://{$subdomain}.kmd-ia.com.br/dashboard"
            : route('dashboard');

        $this->redirect($destino, navigate: false);
    }

    public function reenviar(): void
    {
        $ip          = request()->ip();
        $throttleKey = "reenvio_verificacao:{$ip}";

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->erro = 'Muitos reenvios. Aguarde alguns minutos.';
            return;
        }

        $usuarioId = $this->usuarioId();
        $usuario   = $usuarioId ? Usuario::find($usuarioId) : null;

        if (!$usuario) {
            $this->redirect(route('cadastro'));
            return;
        }

        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario->update([
            'email_token_verificacao' => Hash::make($codigo),
            'email_token_expira_em'   => now()->addMinutes(30),
        ]);

        session(['verificacao_tentativas' => 0]);

        RateLimiter::hit($throttleKey, 3600);

        try {
            Mail::send([], [], function ($msg) use ($usuario, $codigo) {
                $msg->to($usuario->email)
                    ->subject('Novo código de acesso — Software Jurídico')
                    ->html(
                        "<div style='font-family:sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;'>"
                        . "<h2 style='color:#0f2540;'>Software Jurídico</h2>"
                        . "<p style='color:#475569;'>Seu novo código de verificação é:</p>"
                        . "<div style='font-size:40px;font-weight:800;letter-spacing:12px;color:#0f2540;"
                        .      "background:#f1f5f9;border-radius:12px;padding:20px;text-align:center;"
                        .      "margin:24px 0;'>{$codigo}</div>"
                        . "<p style='color:#475569;'>Válido por <strong>30 minutos</strong>.</p>"
                        . "</div>"
                    );
            });
        } catch (\Throwable $e) {
            Log::info("REENVIO_VERIFICACAO | Para: {$usuario->email} | Código: {$codigo} | " . $e->getMessage());
        }

        $this->reenvioOk    = true;
        $this->reenvioAguardar = 60;
        $this->dispatch('iniciar-countdown');
    }

    private function invalidarCodigo(int $usuarioId): void
    {
        Usuario::where('id', $usuarioId)->update([
            'email_token_verificacao' => null,
            'email_token_expira_em'   => null,
        ]);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.verificar-email', [
            'emailDestino' => session('verificacao_email', ''),
        ])->layout('layouts.guest');
    }
}
