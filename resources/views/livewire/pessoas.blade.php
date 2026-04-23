<div>
<style>
@media (max-width: 768px) {
    .pessoas-grid  { grid-template-columns: 1fr !important; }
    .metricas-grid { grid-template-columns: 1fr 1fr !important; }
    .clientes-help { grid-template-columns: 1fr !important; }
    .filtros-panel { position: static !important; }
}
@media (max-width: 480px) {
    .metricas-grid { grid-template-columns: 1fr !important; }
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>

{{-- Cabecalho --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;flex-wrap:wrap;gap:14px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--text);margin:0 0 4px;">Clientes</h1>
        <p style="font-size:13px;color:var(--muted);margin:0;max-width:620px;line-height:1.5;">
            Consulte contatos, identifique o papel de cada pessoa e acesse rapidamente a pasta do cliente.
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button wire:click="exportarCsv" wire:loading.attr="disabled"
            class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
            <span wire:loading.remove wire:target="exportarCsv" style="display:flex;align-items:center;gap:5px;">
                <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                CSV
            </span>
            <span wire:loading wire:target="exportarCsv">Gerando...</span>
        </button>
        <button wire:click="abrirModal()" class="btn btn-primary btn-sm" style="display:flex;align-items:center;gap:6px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Cliente
        </button>
    </div>
</div>

<div class="clientes-help" style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px 18px;margin-bottom:14px;display:grid;grid-template-columns:1.15fr .85fr;gap:16px;align-items:center;">
    <div>
        <div style="font-size:14px;font-weight:800;color:var(--text);margin-bottom:4px;">Como usar esta tela</div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6;">
            Use a busca para localizar o cadastro, filtre por tipo quando precisar separar clientes de advogados ou partes contrárias e use a pasta do cliente para ver os vínculos do processo.
        </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:10px 12px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Total filtrado</div>
            <div style="font-size:20px;font-weight:800;color:var(--text);margin-top:2px;">{{ number_format($pessoas->total()) }}</div>
        </div>
        <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:10px 12px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;">Clientes ativos</div>
            <div style="font-size:20px;font-weight:800;color:var(--text);margin-top:2px;">{{ number_format($totalClientes) }}</div>
        </div>
    </div>
</div>

{{-- Analista IA --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:12px 14px;margin-bottom:14px;display:flex;align-items:center;gap:12px;">
    <div style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            <path d="M18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/>
        </svg>
    </div>
    <input
        wire:model="perguntaIA"
        wire:keydown.enter="perguntarIA"
        type="text"
        placeholder="Pergunte à IA: clientes sem e-mail, advogados cadastrados, partes contrárias..."
        style="flex:1;background:#f8fafc;border:1.5px solid var(--border);border-radius:8px;padding:10px 14px;color:var(--text);font-size:13px;outline:none;">
    <button wire:click="perguntarIA" wire:loading.attr="disabled" wire:target="perguntarIA"
        class="btn btn-primary btn-sm"
        style="white-space:nowrap;display:flex;align-items:center;gap:6px;">
        <span wire:loading.remove wire:target="perguntarIA" style="display:flex;align-items:center;gap:6px;">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
            </svg>
            Analisar
        </span>
        <span wire:loading wire:target="perguntarIA">Analisando...</span>
    </button>
</div>

@if($respostaIA)
<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#1e40af;display:flex;gap:10px;align-items:flex-start;">
    <div style="flex-shrink:0;width:28px;height:28px;background:#dbeafe;border-radius:6px;display:flex;align-items:center;justify-content:center;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
        </svg>
    </div>
    <div style="flex:1;">
        <div style="font-weight:700;margin-bottom:4px;font-size:12px;text-transform:uppercase;letter-spacing:.4px;color:#1d4ed8;">Analista IA</div>
        <div style="line-height:1.6;">{{ $respostaIA }}</div>
    </div>
    <button wire:click="limparIA"
        style="background:none;border:none;color:#93c5fd;cursor:pointer;font-size:18px;line-height:1;padding:0 4px;flex-shrink:0;"
        title="Fechar">&times;</button>
</div>
@endif

{{-- Grid principal --}}
<div class="pessoas-grid" style="display:grid;grid-template-columns:280px 1fr;gap:18px;align-items:start;">

    {{-- COLUNA ESQUERDA: Filtros --}}
    <div class="filtros-panel" style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;padding:16px;position:sticky;top:20px;">

        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="font-size:14px;font-weight:800;color:var(--text);">Filtros</div>
            @if($busca || $tipo)
            <button wire:click="$set('busca',''); $set('tipo','')"
                style="border:0;background:transparent;color:var(--primary);font-size:12px;font-weight:700;cursor:pointer;padding:0;">
                Limpar
            </button>
            @endif
        </div>

        {{-- Busca --}}
        <div style="margin-bottom:16px;">
            <div style="position:relative;">
                <svg aria-hidden="true" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"
                    style="position:absolute;left:10px;top:50%;transform:translateY(-50%);pointer-events:none;">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="busca"
                    placeholder="Nome, CPF/CNPJ ou e-mail"
                    style="width:100%;box-sizing:border-box;padding:9px 10px 9px 34px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
            </div>
        </div>

        {{-- Filtro por Tipo --}}
        <div style="margin-bottom:4px;">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;">Tipo no cadastro</div>
            <div style="display:flex;flex-direction:column;gap:4px;">

                {{-- Todos --}}
                @php $sel = $tipo === ''; @endphp
                <button wire:click="$set('tipo', '')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;border:1.5px solid {{ $sel ? 'var(--primary)' : 'transparent' }};background:{{ $sel ? '#eff6ff' : 'transparent' }};color:{{ $sel ? 'var(--primary)' : 'var(--text)' }};text-align:left;width:100%;transition:all .15s;">
                    <span style="font-weight:{{ $sel ? '600' : '400' }};">Todos</span>
                    <span style="font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;background:{{ $sel ? '#dbeafe' : '#f1f5f9' }};color:{{ $sel ? 'var(--primary)' : 'var(--muted)' }};">{{ array_sum($tipoCounts) }}</span>
                </button>

                @foreach([
                    'Cliente'         => ['#1d4ed8','#dbeafe','#bfdbfe'],
                    'Advogado'        => ['#15803d','#dcfce7','#bbf7d0'],
                    'Juiz'            => ['#b45309','#fef3c7','#fde68a'],
                    'Parte Contrária' => ['#b91c1c','#fee2e2','#fecaca'],
                    'Usuário'         => ['#6d28d9','#ede9fe','#ddd6fe'],
                ] as $t => $cor)
                @php $sel = $tipo === $t; $cnt = $tipoCounts[$t] ?? 0; @endphp
                <button wire:click="$set('tipo', '{{ $sel ? '' : $t }}')"
                    style="display:flex;justify-content:space-between;align-items:center;padding:7px 10px;border-radius:8px;font-size:13px;cursor:pointer;border:1.5px solid {{ $sel ? $cor[2] : 'transparent' }};background:{{ $sel ? $cor[1] : 'transparent' }};color:{{ $sel ? $cor[0] : 'var(--text)' }};text-align:left;width:100%;transition:all .15s;">
                    <span style="display:flex;align-items:center;gap:7px;font-weight:{{ $sel ? '600' : '400' }};">
                        <span style="width:8px;height:8px;border-radius:50%;background:{{ $cor[0] }};flex-shrink:0;display:inline-block;"></span>
                        {{ $t }}
                    </span>
                    @if($cnt > 0)
                    <span style="font-size:11px;font-weight:700;padding:1px 7px;border-radius:10px;background:{{ $sel ? $cor[2] : '#f1f5f9' }};color:{{ $sel ? $cor[0] : 'var(--muted)' }};">{{ $cnt }}</span>
                    @endif
                </button>
                @endforeach
            </div>
        </div>

    </div>

    {{-- COLUNA DIREITA: Metricas + Tabela --}}
    <div>

        {{-- Metricas --}}
        <div class="metricas-grid" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px;margin-bottom:16px;">

            {{-- Total cadastrados --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:var(--text);line-height:1.1;">{{ number_format($totalPessoas) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">cadastros ativos</div>
                </div>
            </div>

            {{-- Clientes --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#eff6ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#1d4ed8;line-height:1.1;">{{ number_format($totalClientes) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">clientes ativos</div>
                </div>
            </div>

            {{-- Advogados --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#15803d" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                        <line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#15803d;line-height:1.1;">{{ number_format($totalAdvogados) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">advogados</div>
                </div>
            </div>

            {{-- Partes Contrarias --}}
            <div style="background:var(--white);border:1.5px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:9px;background:#fff1f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#b91c1c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        <line x1="17" y1="11" x2="23" y2="11"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:800;color:#b91c1c;line-height:1.1;">{{ number_format($totalPartes) }}</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;line-height:1.3;">partes contrárias</div>
                </div>
            </div>

        </div>

        {{-- Tabela --}}
        <div class="card" style="padding:0;overflow:hidden;">
            <div class="table-wrap">
                <table style="border-collapse:collapse;width:100%;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--border);">
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Nome</th>
                            <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">CPF / CNPJ</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Tipo</th>
                            <th class="hide-sm" style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:left;">Contato</th>
                            <th style="padding:12px 16px;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;text-align:center;width:100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pessoas as $p)
                        @php
                            $iniciais = collect(explode(' ', trim($p->nome)))->filter()->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
                            $avatarCores = ['#2563a8','#16a34a','#d97706','#dc2626','#7c3aed','#0891b2','#be185d'];
                            $avatarCor   = $avatarCores[ord($p->nome[0] ?? 'A') % count($avatarCores)];
                        @endphp
                        <tr style="border-bottom:1px solid var(--border);transition:background .15s;" onmouseover="this.style.background='var(--bg)'" onmouseout="this.style.background=''">

                            <td style="padding:14px 16px;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div style="width:36px;height:36px;border-radius:50%;background:{{ $avatarCor }}22;color:{{ $avatarCor }};font-size:12px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1.5px solid {{ $avatarCor }}44;">
                                        {{ $iniciais }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;font-size:13px;color:var(--text);">{{ $p->nome }}</div>
                                        @if($p->oab)
                                        <div style="font-size:11px;color:var(--muted);margin-top:1px;">OAB {{ $p->oab }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <td class="hide-sm" style="padding:14px 16px;font-size:13px;color:var(--muted);font-family:monospace;">
                                {{ $p->cpf_cnpj ?? '—' }}
                            </td>

                            <td style="padding:14px 16px;">
                                <div style="display:flex;flex-wrap:wrap;gap:4px;">
                                    @foreach($tiposPorPessoa->get($p->id, []) as $tipoLabel)
                                    @php
                                    $corTipo = match($tipoLabel) {
                                        'Cliente'         => ['#1d4ed8','#dbeafe'],
                                        'Advogado'        => ['#15803d','#dcfce7'],
                                        'Juiz'            => ['#b45309','#fef3c7'],
                                        'Parte Contrária' => ['#b91c1c','#fee2e2'],
                                        default           => ['#6d28d9','#ede9fe'],
                                    };
                                    @endphp
                                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $corTipo[1] }};color:{{ $corTipo[0] }};">
                                        {{ $tipoLabel }}
                                    </span>
                                    @endforeach
                                </div>
                            </td>

                            <td class="hide-sm" style="padding:14px 16px;">
                                @if($p->email)
                                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--text);margin-bottom:3px;">
                                    <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    {{ $p->email }}
                                </div>
                                @endif
                                @if($p->celular || $p->telefone)
                                <div style="display:flex;align-items:center;gap:5px;font-size:12px;color:var(--muted);">
                                    <svg aria-hidden="true" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                    {{ $p->celular ?? $p->telefone }}
                                </div>
                                @endif
                                @if(!$p->email && !$p->celular && !$p->telefone)
                                <span style="font-size:12px;color:var(--muted);">—</span>
                                @endif
                            </td>

                            <td style="padding:14px 16px;text-align:center;">
                                <div style="display:flex;justify-content:center;gap:4px;">
                                    <button wire:click="abrirModal({{ $p->id }})" title="Editar"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f0fdf4;color:#16a34a;border:none;cursor:pointer;transition:background .15s;"
                                        onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </button>
                                    <button wire:click="desativar({{ $p->id }})" title="Desativar"
                                        wire:confirm="Desativar {{ $p->nome }}?"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f8fafc;color:#94a3b8;border:none;cursor:pointer;transition:background .15s;"
                                        onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f8fafc';this.style.color='#94a3b8'">
                                        <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                    @if(in_array('Cliente', $tiposPorPessoa->get($p->id, [])))
                                    <a href="{{ route('pessoas.pasta', $p->id) }}" title="Pasta do Cliente"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#eff6ff;color:#2563eb;border:none;cursor:pointer;transition:background .15s;text-decoration:none;"
                                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                                    </a>
                                    @endif
                                    @if(in_array('Cliente', $tiposPorPessoa->get($p->id, [])))
                                    <button wire:click="gerarPerfilIA({{ $p->id }})" title="Perfil IA"
                                        style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:6px;background:#f5f3ff;color:#7c3aed;border:none;cursor:pointer;transition:background .15s;"
                                        onmouseover="this.style.background='#ede9fe'" onmouseout="this.style.background='#f5f3ff'">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:48px;color:var(--muted);">
                                <svg aria-hidden="true" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.3;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                <div style="font-size:14px;font-weight:500;">Nenhum cadastro encontrado</div>
                                <div style="font-size:12px;margin-top:4px;">Tente ajustar os filtros ou cadastre um novo cliente.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginacao --}}
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border-top:1px solid var(--border);flex-wrap:wrap;gap:8px;">
                <span style="font-size:13px;color:var(--muted);">
                    @if($pessoas->total() > 0)
                        Mostrando {{ $pessoas->firstItem() }}–{{ $pessoas->lastItem() }} de {{ $pessoas->total() }}
                    @else
                        Nenhum resultado
                    @endif
                </span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <button wire:click="previousPage" @disabled($pessoas->onFirstPage())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $pessoas->onFirstPage() ? '.4' : '1' }};">
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        Anterior
                    </button>
                    <span style="padding:6px 12px;font-size:13px;font-weight:600;color:var(--text);">
                        {{ $pessoas->currentPage() }} / {{ $pessoas->lastPage() }}
                    </span>
                    <button wire:click="nextPage" @disabled(!$pessoas->hasMorePages())
                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:12px;font-weight:600;background:var(--white);color:var(--text);cursor:pointer;opacity:{{ $pessoas->hasMorePages() ? '1' : '.4' }};">
                        Próxima
                        <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Nota informativa --}}
        <div style="margin-top:12px;padding:10px 14px;background:#f0f9ff;border-radius:8px;font-size:12px;color:#64748b;border:1px solid #bae6fd;display:flex;align-items:center;gap:8px;">
            <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0891b2" stroke-width="2" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Um cadastro pode ter múltiplos tipos, como advogado e cliente, sem duplicar informações.
        </div>

    </div>{{-- /coluna direita --}}
</div>{{-- /grid --}}

{{-- Modal Perfil IA --}}
@if($modalPerfilIA)
<div class="modal-backdrop" wire:click.self="fecharPerfilIA">
    <div class="modal" style="max-width:640px;max-height:90vh;overflow-y:auto;">
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#6d28d9);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09Z"/>
                    </svg>
                </div>
                <div>
                    <div class="modal-title">Perfil Inteligente — IA</div>
                    <div style="font-size:12px;color:var(--muted);">{{ $perfilPessoaNome }}</div>
                </div>
            </div>
            <button wire:click="fecharPerfilIA" class="modal-close">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:4px 0;">
            @if($gerandoPerfil)
            <div style="text-align:center;padding:40px;color:var(--muted);">
                <svg style="animation:spin .7s linear infinite;margin:0 auto 12px;display:block;" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <div style="font-size:13px;">Gerando perfil inteligente...</div>
            </div>
            @elseif($perfilIA)
            <div style="background:linear-gradient(135deg,#0f2540,#1a3a5c);border-radius:10px;padding:18px;margin-bottom:16px;">
                <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#93c5fd;margin-bottom:10px;">
                    ✨ Análise gerada por IA
                </div>
                <div style="font-size:13px;color:#e2e8f0;line-height:1.8;white-space:pre-line;">{{ $perfilIA }}</div>
            </div>
            @endif
        </div>

        <div class="modal-footer">
            @if(!$gerandoPerfil && $perfilIA)
            <button wire:click="gerarPerfilIA({{ $perfilPessoaId }})"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 14px;background:#f5f3ff;border:1.5px solid #ddd6fe;border-radius:8px;color:#7c3aed;font-size:12px;font-weight:600;cursor:pointer;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M1 20l4.64-4.36A9 9 0 0020.49 15"/></svg>
                Atualizar
            </button>
            @endif
            <button wire:click="fecharPerfilIA" class="btn btn-outline">Fechar</button>
        </div>
    </div>
</div>
@endif

{{-- Modal Cadastro --}}
@if($modalAberto)
<div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="max-width:720px;max-height:90vh;overflow-y:auto;">

        {{-- Header --}}
        <div class="modal-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;border-radius:8px;background:#eff6ff;display:flex;align-items:center;justify-content:center;">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563a8" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="modal-title">{{ $pessoaId ? 'Editar Cadastro' : 'Novo Cliente' }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;">Preencha o essencial primeiro e complete os dados complementares quando necessário.</div>
                </div>
            </div>
            <button wire:click="fecharModal" class="modal-close" aria-label="Fechar">
                <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        @php
        $inp = "width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);box-sizing:border-box;";
        $sec = "font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px;display:flex;align-items:center;gap:6px;";
        $box = "border:1.5px solid var(--border);border-radius:10px;background:#f8fafc;padding:14px 14px 2px;margin-bottom:12px;";
        @endphp

        <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:10px 12px;margin-bottom:14px;font-size:12px;color:#1e40af;line-height:1.5;">
            Dica: marque todos os tipos que se aplicam. O mesmo cadastro pode ser cliente, advogado ou parte contrária sem duplicar a ficha.
        </div>

        {{-- Seção: Dados Cadastrais --}}
        <div style="{{ $box }}">
        <div style="{{ $sec }}margin-top:0;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            Dados Cadastrais
        </div>

        {{-- Tipo de Pessoa --}}
        <div style="display:flex;gap:10px;margin-bottom:14px;">
            <label style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;border:1.5px solid {{ $tipoPessoa==='fisica' ? '#bfdbfe' : 'var(--border)' }};background:{{ $tipoPessoa==='fisica' ? '#dbeafe' : 'var(--white)' }};color:{{ $tipoPessoa==='fisica' ? '#1d4ed8' : 'var(--text)' }};transition:all .15s;">
                <input type="radio" name="tipoPessoa" value="fisica" wire:click="setTipoPessoa('fisica')" {{ $tipoPessoa==='fisica' ? 'checked' : '' }} style="accent-color:#1d4ed8;margin:0;">
                Pessoa Física
            </label>
            <label style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;border:1.5px solid {{ $tipoPessoa==='juridica' ? '#d1fae5' : 'var(--border)' }};background:{{ $tipoPessoa==='juridica' ? '#dcfce7' : 'var(--white)' }};color:{{ $tipoPessoa==='juridica' ? '#15803d' : 'var(--text)' }};transition:all .15s;">
                <input type="radio" name="tipoPessoa" value="juridica" wire:click="setTipoPessoa('juridica')" {{ $tipoPessoa==='juridica' ? 'checked' : '' }} style="accent-color:#15803d;margin:0;">
                Pessoa Jurídica
            </label>
        </div>

        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">{{ $tipoPessoa === 'juridica' ? 'Razão Social' : 'Nome Completo' }} *</label>
            <input type="text" wire:model="nome" placeholder="{{ $tipoPessoa === 'juridica' ? 'Razão Social' : 'Nome completo' }}" style="{{ $inp }}">
            @error('nome')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">{{ $tipoPessoa === 'juridica' ? 'CNPJ' : 'CPF' }}</label>
                <input type="text" wire:model="cpf_cnpj"
                    placeholder="{{ $tipoPessoa === 'juridica' ? '00.000.000/0000-00' : '000.000.000-00' }}"
                    style="{{ $inp }}">
                @error('cpf_cnpj')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
            </div>
            {{-- RG (Física) ou IE (Jurídica) --}}
            <div class="form-field">
                @if($tipoPessoa === 'fisica')
                    <label class="lbl">RG</label>
                    <input type="text" wire:model="rg" placeholder="Número do RG" style="{{ $inp }}">
                @else
                    <label class="lbl">Inscrição Estadual <span style="font-weight:400;color:var(--muted);">(IE)</span></label>
                    <input type="text" wire:model="inscricaoEstadual" placeholder="Número da IE" style="{{ $inp }}">
                @endif
            </div>
            @if($tipoPessoa === 'fisica')
            <div class="form-field">
                <label class="lbl">Data de Nascimento</label>
                <input type="date" wire:model="data_nascimento" style="{{ $inp }}">
            </div>
            @endif
            <div class="form-field">
                <label class="lbl">OAB <span style="font-weight:400;color:var(--muted);">(se Advogado)</span></label>
                <input type="text" wire:model="oab" placeholder="Número da OAB" style="{{ $inp }}">
            </div>
        </div>

        </div>

        {{-- Seção: Contato --}}
        <div style="{{ $box }}">
        <div style="{{ $sec }}margin-top:0;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            Contato
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">Telefone</label>
                <input type="text" wire:model="telefone" placeholder="(00) 0000-0000" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">Celular</label>
                <input type="text" wire:model="celular" placeholder="(00) 00000-0000" style="{{ $inp }}">
            </div>
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">E-mail</label>
            <input type="email" wire:model="email" placeholder="email@exemplo.com" style="{{ $inp }}">
            @error('email')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
        </div>

        </div>

        {{-- Seção: Endereço --}}
        <div style="{{ $box }}">
        <div style="{{ $sec }}margin-top:0;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Endereço
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label class="lbl">Logradouro</label>
            <input type="text" wire:model="logradouro" placeholder="Rua, número, complemento" style="{{ $inp }}">
        </div>
        <div style="display:grid;grid-template-columns:1fr 80px 110px;gap:10px;margin-bottom:12px;">
            <div class="form-field">
                <label class="lbl">Cidade</label>
                <input type="text" wire:model="cidade" placeholder="Cidade" style="{{ $inp }}">
            </div>
            <div class="form-field">
                <label class="lbl">UF</label>
                <input type="text" wire:model="estado" placeholder="SP" maxlength="2" style="{{ $inp }}text-transform:uppercase;">
            </div>
            <div class="form-field">
                <label class="lbl">CEP</label>
                <input type="text" wire:model="cep" placeholder="00000-000" style="{{ $inp }}">
            </div>
        </div>

        </div>

        {{-- Seção: Tipos --}}
        <div style="{{ $box }}">
        <div style="{{ $sec }}margin-top:0;">
            <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Tipo no cadastro *
        </div>
        <div style="font-size:12px;color:var(--muted);line-height:1.5;margin:-4px 0 10px;">
            Escolha o papel deste cadastro no sistema. Use "Cliente" quando ele tiver processos vinculados.
        </div>
        @error('tipos_selecionados')<span style="color:var(--danger);font-size:11px;display:block;margin-bottom:8px;">{{ $message }}</span>@enderror
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;">
            @foreach($tiposDisponiveis as $t)
            @php
            $ativo = in_array($t, $tipos_selecionados);
            $corTipoM = match($t) {
                'Cliente'         => ['#1d4ed8','#dbeafe','#bfdbfe'],
                'Advogado'        => ['#15803d','#dcfce7','#bbf7d0'],
                'Juiz'            => ['#b45309','#fef3c7','#fde68a'],
                'Parte Contrária' => ['#b91c1c','#fee2e2','#fecaca'],
                default           => ['#6d28d9','#ede9fe','#ddd6fe'],
            };
            @endphp
            <label style="display:flex;align-items:center;gap:6px;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;border:1.5px solid {{ $ativo ? $corTipoM[2] : 'var(--border)' }};background:{{ $ativo ? $corTipoM[1] : 'var(--white)' }};color:{{ $ativo ? $corTipoM[0] : 'var(--text)' }};transition:all .15s;">
                <input type="checkbox" wire:model.live="tipos_selecionados" value="{{ $t }}" style="width:14px;height:14px;accent-color:{{ $corTipoM[0] }};margin:0;flex-shrink:0;cursor:pointer;">
                {{ $t }}
            </label>
            @endforeach
        </div>

        {{-- Advogados responsáveis (obrigatório quando Cliente) --}}
        @if(in_array('Cliente', $tipos_selecionados))
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:16px;">
            <label class="lbl" style="display:block;margin-bottom:4px;">
                Advogado(s) Responsável(is)
                <span style="font-size:11px;font-weight:400;color:var(--muted);">(opcional)</span>
            </label>
            <div style="font-size:12px;color:var(--muted);margin-bottom:8px;">Vincule um ou mais advogados responsáveis por este cliente.</div>
            @error('advogados_ids')<span style="color:var(--danger);font-size:11px;display:block;margin-bottom:8px;">{{ $message }}</span>@enderror
            @if($advogadosDisponiveis->isEmpty())
                <div style="font-size:13px;color:var(--muted);padding:8px 12px;background:#f8fafc;border:1.5px dashed var(--border);border-radius:8px;">
                    Nenhum advogado cadastrado. <a href="{{ route('pessoas') }}?novo=cliente" style="color:var(--primary);">Cadastre primeiro um advogado.</a>
                </div>
            @else
                <div style="display:flex;flex-direction:column;gap:6px;max-height:180px;overflow-y:auto;padding:4px 0;">
                    @foreach($advogadosDisponiveis as $adv)
                    @php $marcado = in_array((string)$adv->id, $advogados_ids); @endphp
                    <label style="display:flex;align-items:center;gap:8px;padding:7px 12px;border-radius:8px;cursor:pointer;font-size:13px;border:1.5px solid {{ $marcado ? '#bbf7d0' : 'var(--border)' }};background:{{ $marcado ? '#f0fdf4' : 'var(--white)' }};transition:all .15s;">
                        <input type="checkbox"
                            wire:model.live="advogados_ids"
                            value="{{ $adv->id }}"
                            style="width:15px;height:15px;accent-color:#16a34a;margin:0;flex-shrink:0;cursor:pointer;">
                        <span style="font-weight:{{ $marcado ? '600' : '400' }};color:{{ $marcado ? '#15803d' : 'var(--text)' }};">{{ $adv->nome }}</span>
                    </label>
                    @endforeach
                </div>
            @endif
        </div>
        @endif

        {{-- Administradora --}}
        @if($administradoras->count() > 0)
        <div style="border-top:1px solid var(--border);padding-top:16px;">
            <label class="lbl" style="display:block;margin-bottom:6px;">Administradora <span style="font-weight:400;color:var(--muted);">(se condomínio)</span></label>
            <select wire:model="administradoraId"
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);">
                <option value="">— Nenhuma —</option>
                @foreach($administradoras as $adm)
                <option value="{{ $adm->id }}">{{ $adm->nome }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Honorário (obrigatório para novo Cliente) --}}
        @if(in_array('Cliente', $tipos_selecionados) && !$pessoaId)
        <div style="border-top:1px solid var(--border);padding-top:16px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:var(--text);margin-bottom:10px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Honorário <span style="color:var(--danger);">*</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:10px;">
                <div>
                    <label class="lbl" style="display:block;margin-bottom:4px;">Tipo *</label>
                    <select wire:model="honorarioTipo" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);">
                        <option value="fixo_mensal">Fixo Mensal</option>
                        <option value="exito">Êxito</option>
                        <option value="hora">Por Hora</option>
                        <option value="ato_diligencia">Ato / Diligência</option>
                    </select>
                    @error('honorarioTipo')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="lbl" style="display:block;margin-bottom:4px;">Valor (R$) *</label>
                    <input type="text" wire:model="honorarioValor" placeholder="0,00" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('honorarioValor')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="lbl" style="display:block;margin-bottom:4px;">Descrição *</label>
                    <input type="text" wire:model="honorarioDescricao" placeholder="Ex: Honorário advocatício" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('honorarioDescricao')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="lbl" style="display:block;margin-bottom:4px;">Data Início *</label>
                    <input type="date" wire:model="honorarioDataInicio" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                    @error('honorarioDataInicio')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="lbl" style="display:block;margin-bottom:4px;">Nº de Parcelas</label>
                    <input type="number" wire:model="honorarioParcelas" min="1" max="120" style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;box-sizing:border-box;">
                </div>
            </div>
        </div>
        @endif

        {{-- Documento de Validação (só admin/financeiro) --}}
        @if($podeVerContrato && in_array('Cliente', $tipos_selecionados))
        <div style="border-top:1px solid var(--border);padding-top:16px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:13px;font-weight:700;color:var(--text);margin-bottom:6px;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><polyline points="9 15 12 18 15 15"/></svg>
                Contrato / Validação
                <span style="font-size:10px;font-weight:600;padding:2px 7px;border-radius:99px;background:#f5f3ff;color:#7c3aed;border:1px solid #ddd6fe;">Restrito</span>
            </div>
            @if($contratoAtual)
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;margin-bottom:8px;font-size:12px;color:#15803d;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Contrato anexado: <strong>{{ $contratoAtualNome }}</strong>
                <div style="display:flex;align-items:center;gap:8px;margin-left:auto;">
                    <a href="/storage/{{ $contratoAtual }}" target="_blank" style="color:#15803d;font-weight:600;">Visualizar</a>
                    <button type="button" wire:click="removerContrato"
                        wire:confirm="Remover o contrato anexado?"
                        style="background:none;border:none;cursor:pointer;color:#dc2626;font-size:11px;font-weight:600;padding:0;">
                        Remover
                    </button>
                </div>
            </div>
            @endif
            <div>
                <label class="lbl" style="display:block;margin-bottom:4px;">{{ $contratoAtual ? 'Substituir contrato' : 'Anexar contrato assinado' }} <span style="font-weight:400;color:var(--muted);">(opcional)</span></label>
                <input type="file" wire:model="contratoArquivo"
                    style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);box-sizing:border-box;cursor:pointer;">
                @error('contratoArquivo')<span style="color:var(--danger);font-size:11px;">{{ $message }}</span>@enderror
                <div wire:loading wire:target="contratoArquivo" style="font-size:11px;color:var(--muted);margin-top:4px;">Carregando...</div>
            </div>
        </div>
        @endif

        {{-- Observações --}}
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-bottom:4px;">
            <label class="lbl" style="display:block;margin-bottom:6px;">Observações</label>
            <textarea wire:model="observacoes" rows="2" placeholder="Observações internas..."
                style="width:100%;padding:8px 12px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;background:var(--white);color:var(--text);resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
        </div>

        </div>

        {{--- Footer --}}
        <div class="modal-footer" style="margin-top:16px;">
            <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
            <button wire:click="salvar" class="btn btn-primary" style="display:flex;align-items:center;gap:6px;">
                <svg aria-hidden="true" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Salvar
            </button>
        </div>
    </div>
</div>
@endif

</div>

</div>
