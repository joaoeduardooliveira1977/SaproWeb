<?php

namespace App\Http\Controllers;

use App\Models\{Tenant, Usuario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash};
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    public function index()
    {
        $tenants = Tenant::withCount(['processos', 'usuarios'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_tenants'   => $tenants->count(),
            'tenants_ativos'  => $tenants->where('ativo', true)->count(),
            'plano_demo'      => $tenants->where('plano', 'demo')->count(),
            'plano_starter'   => $tenants->where('plano', 'starter')->count(),
            'plano_pro'       => $tenants->where('plano', 'pro')->count(),
        ];

        return view('super-admin.index', compact('tenants', 'stats'));
    }

    public function show(int $id)
    {
        $tenant   = Tenant::withCount(['processos', 'usuarios'])->findOrFail($id);
        $usuarios = Usuario::where('tenant_id', $id)->get();
        return view('super-admin.show', compact('tenant', 'usuarios'));
    }

    public function atualizarPlano(Request $request, int $id)
    {
        $request->validate([
            'plano'          => 'required|in:demo,starter,pro,enterprise',
            'gemini_api_key' => 'nullable|string',
        ]);

        $tenant  = Tenant::findOrFail($id);
        $limites = Tenant::limitesPlano($request->plano);

        $dados = [
            'plano'               => $request->plano,
            'limite_processos'    => $limites['processos'],
            'limite_usuarios'     => $limites['usuarios'],
            'ia_habilitada'       => $limites['ia'],
            'datajud_habilitado'  => $limites['datajud'],
            'whatsapp_habilitado' => $limites['whatsapp'],
            'trial_expira_em'     => $request->plano === 'demo' ? now()->addDays(30) : null,
        ];

        if ($request->filled('gemini_api_key')) {
            $dados['gemini_api_key'] = $request->gemini_api_key;
        }

        $tenant->update($dados);

        return back()->with('sucesso', 'Configurações atualizadas com sucesso!');
    }

    public function toggleAtivo(int $id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->update(['ativo' => !$tenant->ativo]);
        return back()->with('sucesso', $tenant->ativo ? 'Tenant ativado!' : 'Tenant suspenso!');
    }

    public function loginComoTenant(int $id)
    {
        $tenant  = Tenant::findOrFail($id);
        $usuario = Usuario::where('tenant_id', $id)
                    ->where('perfil', 'administrador')
                    ->first();

        if (!$usuario) {
            return back()->withErrors(['erro' => 'Nenhum administrador encontrado.']);
        }

        // Guardar super admin na sessão para poder voltar
        session(['super_admin_id' => auth()->id()]);

        Auth::guard('usuarios')->login($usuario);
        return redirect()->route('dashboard');
    }

    public function voltarSuperAdmin()
    {
        $superAdminId = session('super_admin_id');
        if ($superAdminId) {
            $superAdmin = Usuario::findOrFail($superAdminId);
            Auth::guard('usuarios')->login($superAdmin);
            session()->forget('super_admin_id');
        }
        return redirect()->route('super-admin.index');
    }

    public function criar()
    {
        $planos = ['starter', 'pro', 'enterprise'];
        return view('super-admin.criar', compact('planos'));
    }

    public function salvar(Request $request)
    {
        $request->validate([
            'nome'        => 'required|string|min:3|max:150',
            'email'       => 'required|email|unique:tenants,email',
            'plano'       => 'required|in:starter,pro,enterprise',
            'cnpj'        => 'nullable|string|max:18',
            'telefone'    => 'nullable|string|max:20',
            'oab'         => 'nullable|string|max:30',
            'cidade'      => 'nullable|string|max:100',
            'admin_nome'  => 'required|string|min:3|max:150',
            'admin_email' => 'required|email|unique:usuarios,email',
            'admin_senha' => 'required|string|min:8|confirmed',
        ], [
            'nome.required'        => 'O nome do escritório é obrigatório.',
            'email.required'       => 'O e-mail é obrigatório.',
            'email.unique'         => 'Este e-mail já está cadastrado.',
            'plano.required'       => 'Selecione o plano.',
            'admin_nome.required'  => 'O nome do administrador é obrigatório.',
            'admin_email.required' => 'O e-mail de login é obrigatório.',
            'admin_email.unique'   => 'Este e-mail de usuário já está em uso.',
            'admin_senha.required' => 'A senha é obrigatória.',
            'admin_senha.min'      => 'A senha deve ter no mínimo 8 caracteres.',
            'admin_senha.confirmed'=> 'As senhas não conferem.',
        ]);

        $nomeRef = $request->nome;

        DB::transaction(function () use ($request) {
            $slug     = Str::slug($request->nome);
            $slugBase = $slug;
            $i        = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $i++;
            }

            $limites = Tenant::limitesPlano($request->plano);

            $tenant = Tenant::create([
                'nome'                => $request->nome,
                'email'               => $request->email,
                'slug'                => $slug,
                'cnpj'                => $request->cnpj     ?: null,
                'telefone'            => $request->telefone ?: null,
                'oab'                 => $request->oab      ?: null,
                'cidade'              => $request->cidade   ?: null,
                'plano'               => $request->plano,
                'ativo'               => true,
                'trial_expira_em'     => null,
                'limite_processos'    => $limites['processos'],
                'limite_usuarios'     => $limites['usuarios'],
                'ia_habilitada'       => $limites['ia'],
                'datajud_habilitado'  => $limites['datajud'],
                'whatsapp_habilitado' => $limites['whatsapp'],
                'timezone'            => 'America/Sao_Paulo',
                'onboarding_concluido'=> false,
            ]);

            Usuario::create([
                'tenant_id' => $tenant->id,
                'nome'      => $request->admin_nome,
                'email'     => $request->admin_email,
                'login'     => $request->admin_email,
                'password'  => Hash::make($request->admin_senha),
                'perfil'    => 'administrador',
                'ativo'     => true,
            ]);
        });

        return redirect()
            ->route('super-admin.index')
            ->with('sucesso', "Tenant \"{$nomeRef}\" criado com sucesso!");
    }

    public function excluir(int $id)
    {
        $tenant = Tenant::findOrFail($id);

        // Não permitir excluir o próprio tenant do super admin
        if (auth()->user()->tenant_id === $id) {
            return back()->with('erro', 'Não é possível excluir o tenant atual.');
        }

        // Excluir usuários do tenant
        Usuario::where('tenant_id', $id)->delete();

        // Excluir o tenant
        $tenant->delete();

        return back()->with('sucesso', "Tenant \"{$tenant->nome}\" excluído com sucesso!");
    }
}
