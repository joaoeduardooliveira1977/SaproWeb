@extends('layouts.app')
@section('page-title', 'Cadastros')

@section('content')
@php
    $perfil  = auth('usuarios')->user()?->perfil ?? 'estagiario';
    $isAdmin = in_array($perfil, ['admin', 'administrador', 'super_admin']);

    $totalClientes = \App\Models\Pessoa::ativos()->doTipo('Cliente')->count();
    $totalAdv      = \App\Models\Pessoa::ativos()->doTipo('Advogado')->count();
    $totalJuizes   = \App\Models\Pessoa::ativos()->doTipo('Juiz')->count();
    $totalPartes   = \App\Models\Pessoa::ativos()->doTipo('Parte Contrária')->count();
    $totalProc     = \App\Models\Processo::count();
    $totalRepart   = \Illuminate\Support\Facades\DB::table('reparticoes')->count();
    $totalFases    = \Illuminate\Support\Facades\DB::table('fases')->count();
    $totalTipos    = \Illuminate\Support\Facades\DB::table('tipos_acao')->count();

    $cardsPrincipais = [
        ['titulo' => 'Novo Cliente', 'desc' => 'Abrir o formulário para cadastrar um cliente.', 'valor' => $totalClientes, 'rota' => route('pessoas', ['novo' => 'cliente']), 'cor' => '#2563a8', 'icone' => 'users'],
        ['titulo' => 'Novo Processo', 'desc' => 'Cadastro inicial do processo, partes e dados básicos.', 'valor' => $totalProc, 'rota' => route('processos.novo'), 'cor' => '#0f766e', 'icone' => 'file-plus'],
        ['titulo' => 'Advogados', 'desc' => 'Profissionais vinculados aos processos e cadastros.', 'valor' => $totalAdv, 'rota' => route('pessoas', ['tipo' => 'Advogado']), 'cor' => '#15803d', 'icone' => 'briefcase'],
        ['titulo' => 'Partes Contrárias', 'desc' => 'Partes adversas cadastradas para uso nos processos.', 'valor' => $totalPartes, 'rota' => route('pessoas', ['tipo' => 'Parte Contrária']), 'cor' => '#b91c1c', 'icone' => 'user-x'],
    ];

    $cardsApoio = [
        ['titulo' => 'Juízes', 'desc' => 'Cadastros classificados como juiz.', 'valor' => $totalJuizes, 'rota' => route('pessoas', ['tipo' => 'Juiz']), 'cor' => '#a16207', 'icone' => 'scale', 'mostrar' => true],
        ['titulo' => 'Fóruns e Varas', 'desc' => 'Repartições usadas na ficha do processo.', 'valor' => $totalRepart, 'rota' => route('tabelas'), 'cor' => '#0891b2', 'icone' => 'building', 'mostrar' => $isAdmin],
        ['titulo' => 'Fases e Tipos', 'desc' => 'Fases, tipos de ação e tabelas auxiliares.', 'valor' => $totalFases + $totalTipos, 'rota' => route('tabelas'), 'cor' => '#7c3aed', 'icone' => 'table', 'mostrar' => $isAdmin],
        ['titulo' => 'Administradoras', 'desc' => 'Administradoras vinculadas a clientes e condomínios.', 'valor' => \App\Models\Administradora::count(), 'rota' => route('administradoras'), 'cor' => '#475569', 'icone' => 'building', 'mostrar' => $isAdmin],
        ['titulo' => 'Correspondentes', 'desc' => 'Apoio jurídico externo por região e contato.', 'valor' => \App\Models\Correspondente::count(), 'rota' => route('correspondentes'), 'cor' => '#0e7490', 'icone' => 'users', 'mostrar' => true],
        ['titulo' => 'Procurações', 'desc' => 'Instrumentos vinculados aos clientes e processos.', 'valor' => \App\Models\Procuracao::count(), 'rota' => route('procuracoes'), 'cor' => '#d97706', 'icone' => 'file-check', 'mostrar' => true],
    ];

    $icon = function (string $name, string $color) {
        $common = 'width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
        return match ($name) {
            'file-plus' => '<svg '.$common.'><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15h6"/></svg>',
            'briefcase' => '<svg '.$common.'><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M12 12v3"/><path d="M9 15h6"/></svg>',
            'user-x' => '<svg '.$common.'><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M17 8l5 5"/><path d="M22 8l-5 5"/></svg>',
            'scale' => '<svg '.$common.'><path d="M12 3v18"/><path d="M5 7h14"/><path d="M6 7l-3 6h6L6 7z"/><path d="M18 7l-3 6h6l-3-6z"/><path d="M8 21h8"/></svg>',
            'building' => '<svg '.$common.'><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 7h1"/><path d="M14 7h1"/><path d="M9 12h1"/><path d="M14 12h1"/><path d="M9 17h6"/></svg>',
            'table' => '<svg '.$common.'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>',
            'file-check' => '<svg '.$common.'><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15l2 2 4-5"/></svg>',
            default => '<svg '.$common.'><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        };
    };
@endphp

<style>
    .cadastros-grid { display:grid;grid-template-columns:repeat(4,1fr);gap:12px; }
    .cadastros-apoio { display:grid;grid-template-columns:repeat(3,1fr);gap:12px; }
    @media (max-width: 1100px) {
        .cadastros-grid { grid-template-columns:repeat(2,1fr) !important; }
        .cadastros-apoio { grid-template-columns:repeat(2,1fr) !important; }
    }
    @media (max-width: 640px) {
        .cadastros-grid, .cadastros-apoio { grid-template-columns:1fr !important; }
    }
</style>

<div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0 0 4px;">Cadastros</h1>
            <p style="font-size:13px;color:var(--muted);line-height:1.5;margin:0;max-width:720px;">
                Organize os cadastros-base do sistema sem tirar Processos do acesso principal.
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('pessoas') }}" class="btn btn-outline btn-sm">Ver clientes</a>
            <a href="{{ route('processos.novo') }}" class="btn btn-primary btn-sm">Novo processo</a>
        </div>
    </div>

    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:16px;">
        <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:5px;">Onde cadastrar cada coisa</div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6;">
            Use Clientes para partes e contatos, Novo Processo para criar uma demanda, e Tabelas auxiliares para dados de apoio como fóruns, varas, fases e tipos de ação.
        </div>
    </div>

    <div style="font-size:15px;font-weight:800;color:var(--text);margin:0 0 12px;">Cadastros principais</div>
    <div class="cadastros-grid" style="margin-bottom:18px;">
        @foreach($cardsPrincipais as $card)
        <a href="{{ $card['rota'] }}" style="text-decoration:none;">
            <div style="height:100%;background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px;display:flex;flex-direction:column;gap:12px;transition:all .15s;"
                onmouseover="this.style.borderColor='{{ $card['cor'] }}';this.style.transform='translateY(-2px)';this.style.boxShadow='0 6px 18px rgba(15,37,64,.10)'"
                onmouseout="this.style.borderColor='var(--border)';this.style.transform='';this.style.boxShadow=''">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                    <div style="width:42px;height:42px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;">
                        {!! $icon($card['icone'], $card['cor']) !!}
                    </div>
                    <div style="font-size:22px;font-weight:800;color:{{ $card['cor'] }};">{{ number_format($card['valor']) }}</div>
                </div>
                <div>
                    <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:4px;">{{ $card['titulo'] }}</div>
                    <div style="font-size:12px;color:var(--muted);line-height:1.5;">{{ $card['desc'] }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    <div style="font-size:15px;font-weight:800;color:var(--text);margin:0 0 12px;">Cadastros de apoio</div>
    <div class="cadastros-apoio">
        @foreach($cardsApoio as $card)
        @continue(!$card['mostrar'])
        <a href="{{ $card['rota'] }}" style="text-decoration:none;">
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:14px;display:flex;align-items:center;gap:12px;min-height:92px;transition:all .15s;"
                onmouseover="this.style.borderColor='{{ $card['cor'] }}';this.style.background='#fff'"
                onmouseout="this.style.borderColor='var(--border)';this.style.background='var(--white)'">
                <div style="width:40px;height:40px;border-radius:8px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    {!! $icon($card['icone'], $card['cor']) !!}
                </div>
                <div style="min-width:0;flex:1;">
                    <div style="display:flex;justify-content:space-between;gap:8px;align-items:center;">
                        <div style="font-size:13px;font-weight:800;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $card['titulo'] }}</div>
                        <div style="font-size:13px;font-weight:800;color:{{ $card['cor'] }};">{{ number_format($card['valor']) }}</div>
                    </div>
                    <div style="font-size:12px;color:var(--muted);line-height:1.45;margin-top:3px;">{{ $card['desc'] }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
