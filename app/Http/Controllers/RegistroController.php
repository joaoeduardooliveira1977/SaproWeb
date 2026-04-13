<?php

namespace App\Http\Controllers;

use App\Models\{Tenant, Usuario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Auth};
use Illuminate\Support\Str;

class RegistroController extends Controller
{
    public function index()
    {
        return view('registro');
    }

    public function store(Request $request)
    {
        $request->validate([
            'escritorio' => 'required|string|max:150',
            'nome'       => 'required|string|max:150',
            'email'      => 'required|email|unique:tenants,email|unique:usuarios,email',
            'telefone'   => 'nullable|string|max:20',
            'senha'      => 'required|string|min:8|confirmed',
        ], [
            'escritorio.required' => 'Nome do escritório é obrigatório.',
            'nome.required'       => 'Seu nome é obrigatório.',
            'email.required'      => 'E-mail é obrigatório.',
            'email.unique'        => 'Este e-mail já está cadastrado.',
            'senha.required'      => 'Senha é obrigatória.',
            'senha.min'           => 'A senha deve ter no mínimo 8 caracteres.',
            'senha.confirmed'     => 'As senhas não conferem.',
        ]);

        // Criar tenant
        $slug    = Str::slug($request->escritorio) . '-' . Str::random(4);
        $limites = Tenant::limitesPlano('demo');

        $tenant = Tenant::create([
            'nome'                => $request->escritorio,
            'slug'                => $slug,
            'email'               => $request->email,
            'telefone'            => $request->telefone,
            'plano'               => 'demo',
            'trial_expira_em'     => now()->addDays(30),
            'ativo'               => true,
            'limite_processos'    => $limites['processos'],
            'limite_usuarios'     => $limites['usuarios'],
            'ia_habilitada'       => $limites['ia'],
            'datajud_habilitado'  => $limites['datajud'],
            'whatsapp_habilitado' => $limites['whatsapp'],
        ]);

        // Criar usuário administrador do tenant
	$usuario = Usuario::create([
    	'tenant_id' => $tenant->id,
    	'nome'      => $request->nome,
    	'email'     => $request->email,
    	'login'     => $request->email,
    	'password'  => Hash::make($request->password),
    	'perfil'    => 'administrador',
    	'ativo'     => true,
	]);

        Auth::guard('usuarios')->login($usuario);

        // Limpar cache para garantir que o tenant seja carregado
        \Illuminate\Support\Facades\Cache::forget("tenant_{$tenant->id}");

        // Enviar e-mail de boas-vindas
        $this->enviarEmailBoasVindas($tenant, $usuario, $request->senha);

        return redirect()->route('dashboard')
            ->with('sucesso', "Bem-vindo ao Sistema! Você tem 30 dias de teste gratuito.");
    }

    private function enviarEmailBoasVindas(\App\Models\Tenant $tenant, \App\Models\Usuario $usuario, string $senha): void
    {
        $expiracao = $tenant->trial_expira_em->format('d/m/Y');
        $sistNome  = config('app.name', 'Software Jur�dico');
        $url       = config('app.url', 'http://localhost:8000');
        $plano     = ucfirst($tenant->plano);
        $limite    = $tenant->limite_processos;

        $corpo = "
        <div style='font-family:Arial,Helvetica,sans-serif;max-width:600px;margin:0 auto;background:#f1f5f9;border-radius:12px;overflow:hidden;'>

            <div style='background:linear-gradient(135deg,#0f2540,#1a3a5c);padding:32px;text-align:center;'>
                <div style='color:#93c5fd;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:2px;margin-bottom:8px;'>Sistema Jurídico</div>
                <div style='color:#fff;font-size:26px;font-weight:800;'>⚖️ {$sistNome}</div>
                <div style='color:#93c5fd;font-size:14px;margin-top:8px;'>Bem-vindo! Sua conta está pronta.</div>
            </div>

            <div style='background:#fff;padding:32px;border:1px solid #e2e8f0;border-top:none;'>
                <p style='font-size:16px;color:#1e293b;font-weight:700;margin:0 0 8px;'>Olá, {$usuario->nome}! 🎉</p>
                <p style='font-size:14px;color:#475569;margin:0 0 24px;line-height:1.6;'>
                    Seu escritório <strong>{$tenant->nome}</strong> foi cadastrado com sucesso no {$sistNome}.
                    Você tem <strong style='color:#16a34a;'>{$tenant->trial_expira_em->diffInDays(now())} dias</strong> para explorar todas as funcionalidades gratuitamente!
                </p>

                <div style='background:#f0f9ff;border:1.5px solid #bae6fd;border-radius:10px;padding:20px;margin-bottom:24px;'>
                    <div style='font-size:12px;font-weight:700;color:#0369a1;text-transform:uppercase;letter-spacing:.5px;margin-bottom:14px;'>🔑 Seus dados de acesso</div>
                    <table style='width:100%;border-collapse:collapse;font-size:14px;'>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;width:120px;'>URL de acesso:</td>
                            <td style='padding:6px 0;'><a href='{$url}' style='color:#2563a8;font-weight:600;'>{$url}</a></td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;'>Login:</td>
                            <td style='padding:6px 0;font-weight:700;color:#1e293b;'>{$usuario->email}</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;'>Senha:</td>
                            <td style='padding:6px 0;font-weight:700;color:#1e293b;'>{$senha}</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;'>Plano:</td>
                            <td style='padding:6px 0;'><span style='background:#eff6ff;color:#2563a8;padding:2px 10px;border-radius:99px;font-size:12px;font-weight:700;'>{$plano}</span></td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;'>Trial expira:</td>
                            <td style='padding:6px 0;font-weight:700;color:#dc2626;'>{$expiracao}</td>
                        </tr>
                        <tr>
                            <td style='padding:6px 0;color:#64748b;'>Limite:</td>
                            <td style='padding:6px 0;color:#475569;'>{$limite} processos</td>
                        </tr>
                    </table>
                </div>

                <div style='text-align:center;margin-bottom:24px;'>
                    <a href='{$url}/login'
                        style='display:inline-block;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;padding:14px 32px;border-radius:10px;text-decoration:none;font-size:15px;font-weight:700;'>
                        → Acessar o Sistema
                    </a>
                </div>

                <div style='background:#f8fafc;border-radius:8px;padding:16px;margin-bottom:16px;'>
                    <div style='font-size:13px;font-weight:700;color:#334155;margin-bottom:10px;'>🚀 Por onde começar:</div>
                    <div style='font-size:13px;color:#475569;line-height:1.8;'>
                        1. Cadastre seus primeiros clientes em <strong>Processos → Pessoas</strong><br>
                        2. Crie seu primeiro processo em <strong>Processos → Novo Processo</strong><br>
                        3. Explore o <strong>Assistente IA</strong> para redação e análise jurídica<br>
                        4. Configure alertas de prazos em <strong>Prazos</strong>
                    </div>
                </div>

                <p style='font-size:13px;color:#94a3b8;margin:0;'>
                    ⚠️ Guarde suas credenciais em local seguro. Recomendamos alterar a senha após o primeiro acesso em <strong>Minha Conta</strong>.
                </p>
            </div>

            <div style='background:#e2e8f0;padding:16px 32px;text-align:center;'>
                <div style='font-size:12px;color:#64748b;'>
                    {$sistNome} — Este é um e-mail automático, não responda.<br>
                    Dúvidas? Entre em contato: <a href='mailto:suporte@softwarejuridico.com.br' style='color:#2563a8;'>suporte@softwarejuridico.com.br</a>
                </div>
            </div>
        </div>";

        try {
            \Illuminate\Support\Facades\Mail::html($corpo, function ($msg) use ($usuario, $tenant, $sistNome) {
                $msg->to($usuario->email, $usuario->nome)
                    ->subject("✅ Bem-vindo ao {$sistNome} — {$tenant->nome}");
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Falha ao enviar e-mail de boas-vindas: ' . $e->getMessage());
        }
    }
}
