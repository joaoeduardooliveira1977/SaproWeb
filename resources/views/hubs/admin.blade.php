@extends('layouts.app')
@section('page-title', 'Central de Administração')

@section('content')
@php
    $totalUsuarios  = \App\Models\Usuario::where('ativo', true)->count();
    $totalClientes  = \App\Models\Pessoa::ativos()->doTipo('Cliente')->count();
    $portalAtivos   = \App\Models\Pessoa::where('portal_ativo', true)->count();
    $whatsappConfigurado = filled(config('services.twilio.sid')) && filled(config('services.twilio.token'));
    $canalPadrao = strtoupper(config('services.twilio.canal_padrao', 'whatsapp'));

    try {
        $totalAuditoria = \Illuminate\Support\Facades\DB::table('auditorias')
            ->whereDate('created_at', today())
            ->count();
    } catch (\Throwable $e) {
        $totalAuditoria = 0;
    }

    try {
        $whatsappHoje = \Illuminate\Support\Facades\DB::table('notificacoes_whatsapp')
            ->whereDate('created_at', today())
            ->count();

        $templatesWhatsapp = \App\Models\WhatsappTemplate::where('ativo', true)->count();
        $regrasWhatsapp = \App\Models\NotificacaoConfig::where('ativo', true)->count();
    } catch (\Throwable $e) {
        $whatsappHoje = 0;
        $templatesWhatsapp = 0;
        $regrasWhatsapp = 0;
    }

    $atalhos = [
        'usuarios' => ['titulo' => 'Usuários', 'desc' => 'Permissões, perfis e acessos internos.', 'valor' => $totalUsuarios, 'rota' => route('usuarios'), 'cor' => '#2563a8', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/></svg>'],
        'tabelas' => ['titulo' => 'Cadastros auxiliares', 'desc' => 'Fases, riscos, tipos e listas do sistema.', 'valor' => 'Base', 'rota' => route('tabelas'), 'cor' => '#7c3aed', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>'],
        'administradoras' => ['titulo' => 'Administradoras', 'desc' => 'Cadastros usados em clientes e condomínios.', 'valor' => \App\Models\Administradora::count(), 'rota' => route('administradoras'), 'cor' => '#059669', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>'],
        'indices' => ['titulo' => 'Índices de cálculo', 'desc' => 'INPC, IPCA, SELIC e índices financeiros.', 'valor' => 'Cálculo', 'rota' => route('indices'), 'cor' => '#0891b2', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>'],
        'auditoria' => ['titulo' => 'Auditoria', 'desc' => 'Histórico de ações realizadas no sistema.', 'valor' => $totalAuditoria, 'rota' => route('auditoria'), 'cor' => '#475569', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>'],
        'portal' => ['titulo' => 'Portal do cliente', 'desc' => 'Acessos externos liberados para clientes.', 'valor' => $portalAtivos, 'rota' => route('admin.portal-acesso'), 'cor' => '#d97706', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 0 1 0 20"/></svg>'],
        'mensagens' => ['titulo' => 'Mensagens do portal', 'desc' => 'Conversas e retornos enviados pelo cliente.', 'valor' => 'Portal', 'rota' => route('admin.portal-mensagens'), 'cor' => '#dc2626', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'],
        'whatsapp' => ['titulo' => 'WhatsApp / SMS', 'desc' => 'Notificações automáticas, modelos e testes.', 'valor' => $whatsappHoje, 'rota' => route('admin.notificacoes-whatsapp'), 'cor' => '#16a34a', 'icone' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'],
    ];

    $grupos = [
        ['titulo' => 'Acessos', 'desc' => 'Quem entra no sistema e o que o cliente pode consultar.', 'itens' => ['usuarios', 'portal']],
        ['titulo' => 'Cadastros e base', 'desc' => 'Listas estruturais usadas nas rotinas do escritório.', 'itens' => ['tabelas', 'administradoras', 'indices']],
        ['titulo' => 'Comunicação', 'desc' => 'Canais de contato e mensagens automáticas.', 'itens' => ['whatsapp', 'mensagens']],
        ['titulo' => 'Controle', 'desc' => 'Rastreabilidade das alterações feitas no sistema.', 'itens' => ['auditoria']],
    ];
@endphp

<div>
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap;margin-bottom:18px;">
        <div>
            <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Central de Administração</h1>
            <p style="font-size:13px;color:var(--muted);margin:4px 0 0;line-height:1.5;">Configure acessos, cadastros auxiliares, portal do cliente e rotinas automáticas do sistema.</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('usuarios') }}" class="btn btn-primary btn-sm" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;">{!! $atalhos['usuarios']['icone'] !!} Usuários</a>
            <a href="{{ route('auditoria') }}" class="btn btn-sm" style="display:inline-flex;align-items:center;gap:6px;text-decoration:none;background:#eef2f7;color:var(--primary);border:1px solid var(--border);">{!! $atalhos['auditoria']['icone'] !!} Auditoria</a>
        </div>
    </div>

    <div class="admin-guide" style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px;display:grid;grid-template-columns:minmax(240px,1fr) repeat(3,minmax(150px,1fr));gap:12px;align-items:center;">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <div style="width:38px;height:38px;border-radius:8px;background:#f1f5f9;color:var(--primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></div>
            <div><div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:3px;">Por onde começar?</div><div style="font-size:12px;color:var(--muted);line-height:1.5;">A administração mexe na base do sistema. A ordem abaixo ajuda a revisar sem se perder.</div></div>
        </div>
        <a href="{{ route('usuarios') }}" style="text-decoration:none;border-left:3px solid #2563a8;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">1. Acessos</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Revise usuários e portal do cliente.</span></a>
        <a href="{{ route('tabelas') }}" style="text-decoration:none;border-left:3px solid #7c3aed;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">2. Cadastros base</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Ajuste listas e índices de cálculo.</span></a>
        <a href="{{ route('admin.notificacoes-whatsapp') }}" style="text-decoration:none;border-left:3px solid #16a34a;padding-left:10px;"><strong style="display:block;font-size:12px;color:var(--text);margin-bottom:3px;">3. Comunicação</strong><span style="font-size:12px;color:var(--muted);line-height:1.4;">Configure WhatsApp, SMS e mensagens.</span></a>
    </div>

    <div class="admin-kpis" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px;">
        @foreach([
            ['label' => 'Usuários ativos', 'valor' => $totalUsuarios, 'cor' => '#2563a8'],
            ['label' => 'Clientes cadastrados', 'valor' => $totalClientes, 'cor' => '#7c3aed'],
            ['label' => 'Portal ativo', 'valor' => $portalAtivos, 'cor' => '#059669'],
            ['label' => 'Ações hoje', 'valor' => $totalAuditoria, 'cor' => '#475569'],
        ] as $kpi)
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:16px;">
                <div style="font-size:22px;font-weight:800;color:{{ $kpi['cor'] }};line-height:1;">{{ $kpi['valor'] }}</div>
                <div style="font-size:12px;color:var(--muted);margin-top:6px;">{{ $kpi['label'] }}</div>
            </div>
        @endforeach
    </div>

    <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-left:4px solid #16a34a;border-radius:10px;padding:16px;margin-bottom:16px;display:grid;grid-template-columns:minmax(280px,1fr) auto;gap:14px;align-items:center;">
        <div style="display:flex;gap:12px;align-items:flex-start;">
            <div style="width:42px;height:42px;border-radius:8px;background:#dcfce7;color:#166534;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{!! $atalhos['whatsapp']['icone'] !!}</div>
            <div>
                <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;margin-bottom:4px;">
                    <div style="font-size:15px;font-weight:800;color:#166534;">WhatsApp / SMS em destaque</div>
                    <span style="font-size:10px;font-weight:800;border-radius:99px;padding:3px 8px;background:{{ $whatsappConfigurado ? '#dcfce7' : '#fef3c7' }};color:{{ $whatsappConfigurado ? '#166534' : '#92400e' }};">{{ $whatsappConfigurado ? 'Integração ativa' : 'Configuração pendente' }}</span>
                </div>
                <div style="font-size:12px;color:#475569;line-height:1.5;">Envio automático para prazos, audiências, cobranças e testes manuais. Recurso forte para mostrar automação e acompanhamento do escritório.</div>
                <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:10px;font-size:11px;color:#166534;font-weight:700;">
                    <span>{{ $whatsappHoje }} envio(s) hoje</span>
                    <span>{{ $templatesWhatsapp }} modelo(s) ativo(s)</span>
                    <span>{{ $regrasWhatsapp }} regra(s) ativa(s)</span>
                    <span>Canal padrão: {{ $canalPadrao }}</span>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
            <a href="{{ route('admin.notificacoes-whatsapp') }}" class="btn btn-success btn-sm" style="text-decoration:none;display:inline-flex;align-items:center;gap:6px;">Abrir painel</a>
            <a href="{{ route('admin.notificacoes-whatsapp', ['aba' => 'config']) }}" class="btn btn-sm" style="text-decoration:none;background:#fff;color:#166534;border:1px solid #bbf7d0;">Configurar regras</a>
        </div>
    </div>

    <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:18px;">
        <div style="font-size:15px;font-weight:800;color:var(--text);margin-bottom:4px;">Módulos de administração</div>
        <div style="font-size:12px;color:var(--muted);margin-bottom:14px;">Organizei por finalidade para ficar mais fácil encontrar o ajuste certo.</div>

        <div class="admin-sections" style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
            @foreach($grupos as $grupo)
                <section style="border:1.5px solid var(--border);border-radius:10px;padding:14px;background:#fff;">
                    <div style="font-size:13px;font-weight:800;color:var(--text);margin-bottom:3px;">{{ $grupo['titulo'] }}</div>
                    <div style="font-size:11px;color:var(--muted);line-height:1.4;margin-bottom:10px;">{{ $grupo['desc'] }}</div>

                    <div style="display:grid;gap:8px;">
                        @foreach($grupo['itens'] as $chave)
                            @php($item = $atalhos[$chave])
                            <a href="{{ $item['rota'] }}" style="text-decoration:none;">
                                <div style="height:100%;display:flex;gap:12px;align-items:flex-start;padding:12px;border:1.5px solid var(--border);border-radius:8px;background:#f8fafc;transition:border-color .15s,box-shadow .15s,transform .15s;" onmouseover="this.style.borderColor='{{ $item['cor'] }}';this.style.boxShadow='0 8px 22px rgba(15,37,64,.08)';this.style.transform='translateY(-1px)'" onmouseout="this.style.borderColor='var(--border)';this.style.boxShadow='';this.style.transform=''">
                                    <div style="width:34px;height:34px;border-radius:8px;background:{{ $item['cor'] }}14;color:{{ $item['cor'] }};display:flex;align-items:center;justify-content:center;flex-shrink:0;">{!! $item['icone'] !!}</div>
                                    <div style="min-width:0;flex:1;">
                                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:3px;"><span style="font-size:13px;font-weight:800;color:var(--text);">{{ $item['titulo'] }}</span><span style="font-size:10px;font-weight:800;color:{{ $item['cor'] }};background:{{ $item['cor'] }}12;border-radius:99px;padding:2px 7px;">{{ $item['valor'] }}</span></div>
                                        <div style="font-size:11px;color:var(--muted);line-height:1.4;">{{ $item['desc'] }}</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>

<style>
@media (max-width: 1100px) {
    .admin-guide { grid-template-columns: 1fr 1fr !important; }
    .admin-kpis { grid-template-columns: repeat(2, 1fr) !important; }
    .admin-sections { grid-template-columns: 1fr !important; }
}
@media (max-width: 760px) {
    .admin-guide,
    .admin-kpis { grid-template-columns: 1fr !important; }
}
</style>
@endsection