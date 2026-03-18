<?php

namespace App\Livewire\Portal;

use App\Services\WhatsAppSmsService;
use Livewire\Component;
use App\Models\Pessoa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class PortalLogin extends Component
{
    // Etapa: 'credenciais' | 'codigo'
    public string $etapa = 'credenciais';

    // Etapa 1
    public string $cpf_cnpj = '';
    public string $senha    = '';

    // Etapa 2
    public string $codigo   = '';

    // Feedback
    public ?string $erro    = null;
    public ?string $info    = null;

    // Dados da pessoa autenticada (antes do 2FA)
    public ?int    $pessoaId     = null;
    public string  $telefoneExib = ''; // mascarado para exibição

    public function autenticar(): void
    {
        $this->erro = null;

        $cpf_limpo = preg_replace('/\D/', '', $this->cpf_cnpj);

        $pessoa = Pessoa::where(function($q) use ($cpf_limpo) {
                $q->where('cpf_cnpj', $this->cpf_cnpj)
                  ->orWhereRaw("regexp_replace(cpf_cnpj, '[^0-9]', '', 'g') = ?", [$cpf_limpo]);
            })
            ->where('portal_ativo', true)
            ->first();

        if (! $pessoa || ! Hash::check($this->senha, $pessoa->portal_senha)) {
            $this->erro = 'CPF/CNPJ ou senha inválidos.';
            return;
        }

        // Verifica se há telefone para 2FA
        $telefone = $pessoa->celular ?: $pessoa->telefone;
        $svc      = new WhatsAppSmsService();

        if (! $telefone || ! $svc->configurado()) {
            // Sem 2FA — acesso direto
            $this->finalizarLogin($pessoa);
            return;
        }

        // Gera OTP de 6 dígitos
        $otp     = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expira  = now()->addMinutes(5)->timestamp;

        Session::put('portal_2fa', [
            'pessoa_id' => $pessoa->id,
            'otp'       => Hash::make($otp),
            'expira'    => $expira,
        ]);

        // Envia via WhatsApp/SMS
        $svc->enviar(
            telefone:         $telefone,
            mensagem:         "🔐 *JURÍDICO — Código de acesso*\n\nSeu código de verificação é: *{$otp}*\n\nVálido por 5 minutos. Não compartilhe.",
            destinatarioNome: $pessoa->nome,
            tipo:             'teste',
            canal:            config('services.twilio.canal_padrao', 'whatsapp'),
        );

        $this->pessoaId     = $pessoa->id;
        $this->telefoneExib = $this->mascarar($telefone);
        $this->etapa        = 'codigo';
        $this->info         = "Código enviado para {$this->telefoneExib}";
    }

    public function verificarCodigo(): void
    {
        $this->erro = null;
        $dados2fa   = Session::get('portal_2fa');

        if (! $dados2fa || $dados2fa['pessoa_id'] !== $this->pessoaId) {
            $this->erro = 'Sessão inválida. Faça login novamente.';
            $this->reiniciar();
            return;
        }

        if (now()->timestamp > $dados2fa['expira']) {
            $this->erro = 'Código expirado. Solicite um novo.';
            Session::forget('portal_2fa');
            $this->reiniciar();
            return;
        }

        if (! Hash::check($this->codigo, $dados2fa['otp'])) {
            $this->erro = 'Código incorreto. Tente novamente.';
            $this->codigo = '';
            return;
        }

        Session::forget('portal_2fa');

        $pessoa = Pessoa::findOrFail($this->pessoaId);
        $this->finalizarLogin($pessoa);
    }

    public function reenviarCodigo(): void
    {
        // Volta para credenciais para reautenticar
        $this->reiniciar();
        $this->info = 'Digite suas credenciais novamente para reenviar o código.';
    }

    private function finalizarLogin(Pessoa $pessoa): void
    {
        $pessoa->update(['portal_ultimo_acesso' => now()]);

        Session::put('portal_pessoa_id', $pessoa->id);
        Session::put('portal_pessoa_nome', $pessoa->nome);

        $this->redirect(route('portal.dashboard'));
    }

    private function reiniciar(): void
    {
        $this->etapa        = 'credenciais';
        $this->codigo       = '';
        $this->pessoaId     = null;
        $this->telefoneExib = '';
        Session::forget('portal_2fa');
    }

    private function mascarar(string $tel): string
    {
        $digits = preg_replace('/\D/', '', $tel);
        $len    = strlen($digits);
        if ($len <= 4) return $tel;
        // Exibe apenas os últimos 4 dígitos: ***** 9999
        return str_repeat('*', $len - 4) . substr($digits, -4);
    }

    public function render()
    {
        return view('livewire.portal.login')
            ->layout('portal.layout');
    }
}
