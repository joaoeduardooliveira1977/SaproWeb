<?php

namespace App\Http\Controllers;

use App\Jobs\PopularDadosFicticios;
use App\Mail\TrialBoasVindas;
use App\Models\Tenant;
use App\Models\TrialEmail;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class CadastroLandingController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'responsavel_nome'  => 'required|string|min:3|max:150',
            'nome_escritorio'   => 'required|string|min:3|max:150',
            'email'             => 'required|email|max:150|unique:usuarios,email',
            'telefone'          => 'nullable|string|max:30',
            'cidade'            => 'nullable|string|max:80',
            'estado'            => 'nullable|string|size:2',
            'senha'             => 'required|string|min:8',
            'senha_confirmacao' => 'required|same:senha',
            'aceitar_termos'    => 'accepted',
        ], [
            'responsavel_nome.required'  => 'Informe seu nome completo.',
            'responsavel_nome.min'       => 'O nome deve ter ao menos 3 caracteres.',
            'nome_escritorio.required'   => 'Informe o nome do escritório.',
            'email.required'             => 'Informe seu email.',
            'email.email'                => 'Email inválido.',
            'email.unique'               => 'Este email já está cadastrado. Faça login ou use outro email.',
            'senha.required'             => 'Defina uma senha.',
            'senha.min'                  => 'A senha deve ter ao menos 8 caracteres.',
            'senha_confirmacao.required' => 'Confirme a senha.',
            'senha_confirmacao.same'     => 'As senhas não coincidem.',
            'aceitar_termos.accepted'    => 'Você precisa aceitar os Termos de Uso para continuar.',
        ]);

        $slug   = $this->gerarSlugUnico($data['nome_escritorio']);
        $limites = Tenant::limitesPlanoCompleto('demo');

        $tenant = Tenant::create([
            'nome'                 => $data['nome_escritorio'],
            'slug'                 => $slug,
            'dominio'              => "{$slug}.kmd-ia.com.br",
            'email'                => $data['email'],
            'telefone'             => $data['telefone'] ?? null,
            'cidade'               => $data['cidade'] ?? null,
            'plano'                => 'demo',
            'trial_iniciado_em'    => now(),
            'trial_expira_em'      => now()->addDays(15),
            'ativo'                => true,
            'origem'               => 'landing',
            'responsavel_nome'     => $data['responsavel_nome'],
            'responsavel_telefone' => $data['telefone'] ?? null,
            'onboarding_concluido' => false,
            ...$limites,
        ]);

        // Gera código de 6 dígitos
        $codigo = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $usuario = Usuario::create([
            'tenant_id'               => $tenant->id,
            'nome'                    => $data['responsavel_nome'],
            'email'                   => $data['email'],
            'login'                   => $data['email'],
            'password'                => Hash::make($data['senha']),
            'perfil'                  => 'admin',
            'ativo'                   => false, // inativo até verificar email
            'email_verificado'        => false,
            'email_token_verificacao' => Hash::make($codigo),
            'email_token_expira_em'   => now()->addMinutes(30),
        ]);

        PopularDadosFicticios::dispatch($tenant->id, $usuario->id);

        $this->enviarCodigoVerificacao($data['email'], $data['responsavel_nome'], $codigo);

        // Email de boas-vindas
        try {
            Mail::to($data['email'])->send(new TrialBoasVindas($tenant));
            TrialEmail::create([
                'tenant_id'  => $tenant->id,
                'tipo'       => 'boas_vindas',
                'enviado_em' => now(),
                'sucesso'    => true,
            ]);
        } catch (\Throwable) {}

        // Salva o ID do usuário na sessão para a página de verificação
        session([
            'verificacao_usuario_id' => $usuario->id,
            'verificacao_email'      => $data['email'],
            'verificacao_tentativas' => 0,
        ]);

        return redirect()->route('verificar-email')
            ->with('info', "Enviamos um código de 6 dígitos para {$data['email']}. Digite abaixo para ativar sua conta.");
    }

    private function enviarCodigoVerificacao(string $email, string $nome, string $codigo): void
    {
        try {
            Mail::send([], [], function ($msg) use ($email, $nome, $codigo) {
                $msg->to($email)
                    ->subject('Seu código de acesso — Software Jurídico')
                    ->html(
                        "<div style='font-family:sans-serif;max-width:480px;margin:0 auto;padding:32px 24px;'>"
                        . "<h2 style='color:#0f2540;margin-bottom:8px;'>Software Jurídico</h2>"
                        . "<p style='color:#475569;'>Olá, {$nome}!</p>"
                        . "<p style='color:#475569;'>Seu código de verificação é:</p>"
                        . "<div style='font-size:40px;font-weight:800;letter-spacing:12px;color:#0f2540;"
                        .      "background:#f1f5f9;border-radius:12px;padding:20px;text-align:center;"
                        .      "margin:24px 0;'>{$codigo}</div>"
                        . "<p style='color:#475569;'>Este código é válido por <strong>30 minutos</strong> e é de uso único.</p>"
                        . "<p style='color:#94a3b8;font-size:12px;margin-top:32px;'>Se você não solicitou este cadastro, ignore este email.</p>"
                        . "</div>"
                    );
            });
        } catch (\Throwable $e) {
            // Em desenvolvimento sem SMTP: loga o código
            Log::info("VERIFICACAO_EMAIL | Para: {$email} | Código: {$codigo} | Motivo: " . $e->getMessage());
        }
    }

    private function gerarSlugUnico(string $nome): string
    {
        $base = Str::slug($nome);
        if (!Tenant::where('slug', $base)->exists()) {
            return $base;
        }
        $i = 2;
        while (Tenant::where('slug', "{$base}-{$i}")->exists()) {
            $i++;
        }
        return "{$base}-{$i}";
    }
}
