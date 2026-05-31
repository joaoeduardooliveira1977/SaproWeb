<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\{MasterAdminLog, Usuario};
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class Master2FAController extends Controller
{
    private function google2fa(): Google2FA
    {
        return new Google2FA();
    }

    // ── Configuração do 2FA ───────────────────────────────────────

    public function mostrarConfigurar(Request $request)
    {
        $user = Auth::guard('usuarios')->user();

        // Gera secret se ainda não tem
        if (!$user->master_2fa_secret) {
            $secret = $this->google2fa()->generateSecretKey();
            $user->update(['master_2fa_secret' => encrypt($secret)]);
            $user->refresh();
        }

        $secret     = decrypt($user->master_2fa_secret);
        $otpauthUrl = $this->google2fa()->getQRCodeUrl(
            config('app.name', 'Sistema Jurídico'),
            $user->email ?? $user->login,
            $secret
        );

        $qrCodeSvg = $this->gerarQrCode($otpauthUrl);

        return view('master.2fa.configurar', compact('secret', 'qrCodeSvg', 'user'));
    }

    public function confirmarSetup(Request $request)
    {
        $request->validate([
            'codigo' => 'required|digits:6',
        ], [
            'codigo.required' => 'Digite o código de 6 dígitos.',
            'codigo.digits'   => 'O código deve ter 6 dígitos.',
        ]);

        $user   = Auth::guard('usuarios')->user();
        $secret = decrypt($user->master_2fa_secret);

        if (!$this->google2fa()->verifyKey($secret, $request->codigo)) {
            return back()->withErrors(['codigo' => 'Código inválido. Verifique seu authenticator.']);
        }

        // Gera 8 códigos de recuperação
        $codigosRecuperacao = collect(range(1, 8))->map(fn() => strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)));
        $hashesRecuperacao  = $codigosRecuperacao->map(fn($c) => bcrypt($c))->toArray();

        $user->update([
            'master_2fa_ativo'          => true,
            'master_2fa_confirmado_em'  => now(),
            'master_2fa_recovery_codes' => $hashesRecuperacao,
        ]);

        MasterAdminLog::registrar('2fa_ativado', null, null, '2FA configurado e ativado.');
        session(['master_2fa_verificado' => true]);

        return view('master.2fa.codigos-recuperacao', [
            'codigos' => $codigosRecuperacao->toArray(),
        ]);
    }

    public function desativar(Request $request)
    {
        $request->validate(['codigo' => 'required|digits:6']);

        $user   = Auth::guard('usuarios')->user();
        $secret = decrypt($user->master_2fa_secret);

        if (!$this->google2fa()->verifyKey($secret, $request->codigo)) {
            return back()->withErrors(['codigo' => 'Código inválido.']);
        }

        $user->update([
            'master_2fa_ativo'          => false,
            'master_2fa_secret'         => null,
            'master_2fa_confirmado_em'  => null,
            'master_2fa_recovery_codes' => null,
        ]);

        MasterAdminLog::registrar('2fa_desativado', null, null, '2FA desativado.');
        session()->forget('master_2fa_verificado');

        return redirect()->route('master.2fa.configurar')
            ->with('sucesso', '2FA desativado com sucesso.');
    }

    // ── Verificação no login ──────────────────────────────────────

    public function mostrarVerificar()
    {
        if (!Auth::guard('usuarios')->check()) {
            return redirect()->route('master.login');
        }
        if (session('master_2fa_verificado')) {
            return redirect()->route('master.dashboard');
        }
        return view('master.2fa.verificar');
    }

    public function verificar(Request $request)
    {
        $request->validate(['codigo' => 'required|string'], ['codigo.required' => 'Digite o código.']);

        $user   = Auth::guard('usuarios')->user();
        $codigo = trim($request->codigo);

        // Tenta código de recuperação (formato XXXX-XXXX)
        if (strlen($codigo) === 9 && str_contains($codigo, '-')) {
            if ($this->verificarCodigoRecuperacao($user, $codigo)) {
                session(['master_2fa_verificado' => true]);
                MasterAdminLog::registrar('2fa_verificado_recuperacao', null, null, 'Código de recuperação usado.');
                return redirect()->route('master.dashboard');
            }
            return back()->withErrors(['codigo' => 'Código de recuperação inválido ou já utilizado.']);
        }

        // Tenta código TOTP
        if (!$user->master_2fa_secret) {
            return back()->withErrors(['codigo' => '2FA não configurado.']);
        }

        if (!$this->google2fa()->verifyKey(decrypt($user->master_2fa_secret), $codigo)) {
            return back()->withErrors(['codigo' => 'Código inválido. Tente novamente.']);
        }

        session(['master_2fa_verificado' => true]);
        MasterAdminLog::registrar('2fa_verificado', null, null, 'Login com 2FA verificado.');

        return redirect()->route('master.dashboard');
    }

    // ── Helpers ───────────────────────────────────────────────────

    private function verificarCodigoRecuperacao(Usuario $user, string $codigo): bool
    {
        $hashes = $user->master_2fa_recovery_codes ?? [];
        foreach ($hashes as $i => $hash) {
            if (password_verify(strtoupper($codigo), $hash)) {
                unset($hashes[$i]);
                $user->update(['master_2fa_recovery_codes' => array_values($hashes)]);
                return true;
            }
        }
        return false;
    }

    private function gerarQrCode(string $url): string
    {
        try {
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            return (new Writer($renderer))->writeString($url);
        } catch (\Exception) {
            return '';
        }
    }
}
