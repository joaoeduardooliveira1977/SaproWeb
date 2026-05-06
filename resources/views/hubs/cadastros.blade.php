@extends('layouts.app')
@section('page-title', 'Cadastros')

@section('content')
@php
    $perfil  = auth('usuarios')->user()?->perfil ?? 'estagiario';
    $isAdmin = in_array($perfil, ['admin', 'administrador', 'super_admin']);

    $cardsApoio = [
        ['titulo' => 'Tabelas Auxiliares',   'desc' => 'Varas, fóruns, fases, tipos de ação e demais tabelas de apoio.',       'valor' => \Illuminate\Support\Facades\DB::table('reparticoes')->count() + \Illuminate\Support\Facades\DB::table('varas')->count(), 'rota' => route('tabelas'),          'cor' => '#0891b2', 'icone' => 'table',    'mostrar' => $isAdmin],
        ['titulo' => 'Administradoras',       'desc' => 'Administradoras vinculadas a clientes e condomínios.',                 'valor' => \App\Models\Administradora::count(),                                                                                           'rota' => route('administradoras'),  'cor' => '#475569', 'icone' => 'building', 'mostrar' => $isAdmin],
        ['titulo' => 'Correspondentes',       'desc' => 'Apoio jurídico externo por região e contato.',                        'valor' => \App\Models\Correspondente::count(),                                                                                           'rota' => route('correspondentes'),  'cor' => '#0e7490', 'icone' => 'users',    'mostrar' => true],
        ['titulo' => 'Procurações',           'desc' => 'Instrumentos vinculados aos clientes e processos.',                   'valor' => \App\Models\Procuracao::count(),                                                                                               'rota' => route('procuracoes'),      'cor' => '#d97706', 'icone' => 'file-check','mostrar' => true],
        ['titulo' => 'Indicadores / Síndicos','desc' => 'Cadastro de indicadores com percentual de comissão sobre receitas.',  'valor' => \App\Models\Indicador::ativos()->count(),                                                                                      'rota' => route('indicadores'),      'cor' => '#7c3aed', 'icone' => 'percent',  'mostrar' => $isAdmin],
    ];

    $icon = function (string $name, string $color) {
        $c = 'width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="'.$color.'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"';
        return match ($name) {
            'building'   => '<svg '.$c.'><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 7h1"/><path d="M14 7h1"/><path d="M9 12h1"/><path d="M14 12h1"/><path d="M9 17h6"/></svg>',
            'table'      => '<svg '.$c.'><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/><path d="M9 3v18"/><path d="M15 3v18"/></svg>',
            'file-check' => '<svg '.$c.'><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M9 15l2 2 4-5"/></svg>',
            'percent'    => '<svg '.$c.'><line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/></svg>',
            default      => '<svg '.$c.'><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        };
    };
@endphp

<style>
    .cadastros-apoio { display:grid;grid-template-columns:repeat(3,1fr);gap:12px; }
    @media (max-width: 1100px) { .cadastros-apoio { grid-template-columns:repeat(2,1fr) !important; } }
    @media (max-width: 640px)  { .cadastros-apoio { grid-template-columns:1fr !important; } }
</style>

<div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:14px;flex-wrap:wrap;margin-bottom:18px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0 0 4px;">Cadastros</h1>
            <p style="font-size:13px;color:var(--muted);line-height:1.5;margin:0;max-width:720px;">
                Acesse os cadastros de apoio do sistema — tabelas auxiliares, correspondentes, procurações e indicadores.
            </p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('pessoas') }}" class="btn btn-outline btn-sm">Pessoas</a>
            <a href="{{ route('processos.hub') }}" class="btn btn-primary btn-sm">Processos</a>
        </div>
    </div>

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
