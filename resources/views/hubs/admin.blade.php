@extends('layouts.app')
@section('page-title', 'Central de Administração')

@section('content')
@php
    $totalUsuarios  = \App\Models\Usuario::where('ativo', true)->count();
    $totalClientes  = \App\Models\Pessoa::ativos()->doTipo('Cliente')->count();
    $portalAtivos   = \App\Models\Pessoa::where('portal_ativo', true)->count();
    try {
        $totalAuditoria = \Illuminate\Support\Facades\DB::table('auditorias')
                            ->whereDate('created_at', today())->count();
    } catch (\Throwable $e) {
        $totalAuditoria = 0;
    }
@endphp

<div>

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Central de Administração</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Gerenciamento de usuários, tabelas auxiliares e configurações do sistema.</p>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('usuarios') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#1d4ed8,#2563a8);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Usuários
        </a>
        <a href="{{ route('auditoria') }}"
            style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#475569,#334155);color:#fff;border-radius:10px;text-decoration:none;font-size:13px;font-weight:700;transition:opacity .15s;"
            onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Auditoria
        </a>
    </div>
</div>

{{-- KPIs --}}
<div class="hub-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px;">
    @php
    $kpis = [
        [
            'label' => 'Usuários Ativos',
            'val'   => $totalUsuarios,
            'bg'    => 'linear-gradient(135deg,#1d4ed8,#2563a8)',
            'route' => route('usuarios'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        ],
        [
            'label' => 'Clientes Cadastrados',
            'val'   => $totalClientes,
            'bg'    => 'linear-gradient(135deg,#7c3aed,#6d28d9)',
            'route' => route('pessoas'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        ],
        [
            'label' => 'Portal Ativos',
            'val'   => $portalAtivos,
            'bg'    => 'linear-gradient(135deg,#059669,#16a34a)',
            'route' => route('admin.portal-acesso'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>',
        ],
        [
            'label' => 'Ações Hoje',
            'val'   => $totalAuditoria,
            'bg'    => 'linear-gradient(135deg,#475569,#334155)',
            'route' => route('auditoria'),
            'svg'   => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>',
        ],
    ];
    @endphp
    @foreach($kpis as $k)
    <a href="{{ $k['route'] }}" style="text-decoration:none;">
        <div style="background:{{ $k['bg'] }};border-radius:14px;padding:22px 20px;color:#fff;cursor:pointer;transition:transform .15s,box-shadow .15s;box-shadow:0 4px 15px rgba(0,0,0,.15);"
            onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 8px 25px rgba(0,0,0,.2)'"
            onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(0,0,0,.15)'">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                {!! $k['svg'] !!}
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.5)" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
            <div style="font-size:28px;font-weight:800;margin-bottom:4px;letter-spacing:-1px;">{{ $k['val'] }}</div>
            <div style="font-size:13px;color:rgba(255,255,255,.8);font-weight:500;">{{ $k['label'] }}</div>
        </div>
    </a>
    @endforeach
</div>

{{-- Status do Sistema --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:14px;padding:20px 24px;margin-bottom:20px;">
    <div style="display:flex;align-items:flex-start;gap:16px;">
        <div style="width:52px;height:52px;border-radius:14px;background:linear-gradient(135deg,#475569,#334155);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:6px;">Status do Sistema</div>
            <div style="font-size:13px;color:var(--muted);line-height:1.7;">
                Sistema operando normalmente com <strong style="color:var(--primary);">{{ $totalUsuarios }} usuário(s) ativo(s)</strong>
                e <strong style="color:#7c3aed;">{{ $totalClientes }} cliente(s)</strong> cadastrado(s).
                @if($portalAtivos > 0)
                    <strong style="color:#059669;">{{ $portalAtivos }} cliente(s)</strong> com acesso ao portal ativo.
                @endif
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:12px;font-size:12px;color:var(--muted);">
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#2563a8" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#2563a8;">{{ $totalUsuarios }}</strong>&nbsp;usuários ativos
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#7c3aed" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#7c3aed;">{{ $totalClientes }}</strong>&nbsp;clientes
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#059669" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#059669;">{{ $portalAtivos }}</strong>&nbsp;no portal
                </span>
                <span style="display:flex;align-items:center;gap:5px;">
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="#475569" stroke="none"><circle cx="12" cy="12" r="10"/></svg>
                    <strong style="color:#475569;">{{ $totalAuditoria }}</strong>&nbsp;ações hoje
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Módulos de Administração --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:20px;">
    <div style="font-size:15px;font-weight:700;color:var(--text);margin-bottom:16px;">Módulos de Administração</div>
    <div class="hub-modulos" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @php
        $modulos = [
            ['label'=>'Usuários',           'desc'=>'Permissões e acessos',        'cor'=>'#1d4ed8','bg'=>'#eff6ff','route'=>route('usuarios'),                   'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
            ['label'=>'Tabelas Auxiliares', 'desc'=>'Fases, riscos e tipos',       'cor'=>'#7c3aed','bg'=>'#f5f3ff','route'=>route('tabelas'),                    'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg>'],
            ['label'=>'Administradoras',    'desc'=>'Cadastro de administradoras', 'cor'=>'#059669','bg'=>'#f0fdf4','route'=>route('administradoras'),             'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
            ['label'=>'Índices',            'desc'=>'INPC, IPCA, SELIC...',        'cor'=>'#0891b2','bg'=>'#f0f9ff','route'=>route('indices'),                    'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>'],
            ['label'=>'Auditoria',          'desc'=>'Log de ações do sistema',     'cor'=>'#475569','bg'=>'#f8fafc','route'=>route('auditoria'),                  'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/></svg>'],
            ['label'=>'Portal — Acessos',   'desc'=>'Acessos do portal do cliente','cor'=>'#d97706','bg'=>'#fffbeb','route'=>route('admin.portal-acesso'),         'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>'],
            ['label'=>'Portal — Mensagens', 'desc'=>'Mensagens do portal',         'cor'=>'#dc2626','bg'=>'#fef2f2','route'=>route('admin.portal-mensagens'),      'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'],
            ['label'=>'WhatsApp / SMS',     'desc'=>'Notificações automáticas',    'cor'=>'#16a34a','bg'=>'#f0fdf4','route'=>route('admin.notificacoes-whatsapp'), 'svg'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>'],
        ];
        @endphp
        @foreach($modulos as $m)
        <a href="{{ $m['route'] }}" style="text-decoration:none;">
            <div style="display:flex;align-items:center;gap:12px;padding:16px;border-radius:12px;background:{{ $m['bg'] }};border:1.5px solid {{ $m['cor'] }}22;transition:all .15s;cursor:pointer;"
                onmouseover="this.style.borderColor='{{ $m['cor'] }}';this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.08)'"
                onmouseout="this.style.borderColor='{{ $m['cor'] }}22';this.style.transform='';this.style.boxShadow=''">
                <div style="width:40px;height:40px;border-radius:10px;background:#fff;color:{{ $m['cor'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 2px 8px rgba(0,0,0,.08);">
                    {!! $m['svg'] !!}
                </div>
                <div>
                    <div style="font-size:13px;font-weight:700;color:{{ $m['cor'] }};">{{ $m['label'] }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">{{ $m['desc'] }}</div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</div>

</div>

<style>
@media (max-width: 1024px) {
    .hub-kpis    { grid-template-columns: repeat(2, 1fr) !important; }
    .hub-modulos { grid-template-columns: repeat(2, 1fr) !important; }
}
@media (max-width: 640px) {
    .hub-kpis    { grid-template-columns: 1fr 1fr !important; }
    .hub-modulos { grid-template-columns: 1fr 1fr !important; }
}
</style>
@endsection
