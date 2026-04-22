<div>

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
    <div>
        <h1 style="font-size:24px;font-weight:800;color:var(--primary);margin:0;">Perfis de Acesso</h1>
        <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">Configure quais módulos cada perfil pode acessar. Administradores têm acesso total sempre.</p>
    </div>
</div>

@php
    $corPerfil = [
        'advogado'      => ['bg'=>'#eff6ff','color'=>'#1d4ed8','label'=>'Advogado'],
        'estagiario'    => ['bg'=>'#f5f3ff','color'=>'#6d28d9','label'=>'Estagiário'],
        'financeiro'    => ['bg'=>'#f0fdf4','color'=>'#15803d','label'=>'Financeiro'],
        'recepcionista' => ['bg'=>'#fff7ed','color'=>'#c2410c','label'=>'Recepcionista'],
    ];
    $iconModulo = [
        'processos'   => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
        'pessoas'     => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>',
        'documentos'  => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>',
        'financeiro'  => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>',
        'honorarios'  => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'relatorios'  => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 3v18h18"/><path d="M7 14l4-4 3 3 5-6"/></svg>',
        'ferramentas' => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>',
        'usuarios'    => '<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
    ];
@endphp

{{-- ── Legenda dos perfis ──────────────────────────────────────── --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
    <div style="background:#f1f5f9;border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;font-size:12px;color:#475569;">
        <svg width="14" height="14" fill="none" stroke="#ef4444" stroke-width="2.5" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
        Admin — acesso total, não configurável
    </div>
    @foreach($corPerfil as $perfil => $c)
    <div style="background:{{ $c['bg'] }};border-radius:8px;padding:10px 14px;display:flex;align-items:center;gap:8px;">
        <span style="width:10px;height:10px;border-radius:50%;background:{{ $c['color'] }};display:inline-block;"></span>
        <span style="font-size:12px;font-weight:600;color:{{ $c['color'] }};">{{ $c['label'] }}</span>
    </div>
    @endforeach
</div>

{{-- ── Matriz ──────────────────────────────────────────────────── --}}
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:600px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid var(--border);">
                    <th style="padding:14px 20px;text-align:left;font-size:12px;font-weight:700;color:var(--primary);width:240px;">
                        Módulo
                    </th>
                    @foreach(\App\Livewire\PerfilPermissoes::$PERFIS as $perfil => $label)
                    <th style="padding:14px 16px;text-align:center;font-size:12px;font-weight:700;color:{{ $corPerfil[$perfil]['color'] }};">
                        {{ $label }}
                        <div style="margin-top:4px;">
                            <button wire:click="restaurarPadrao('{{ $perfil }}')"
                                    wire:confirm="Restaurar permissões padrão para {{ $label }}?"
                                    style="background:none;border:none;cursor:pointer;font-size:10px;color:var(--muted);text-decoration:underline;padding:0;">
                                restaurar padrão
                            </button>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Livewire\PerfilPermissoes::$MODULOS as $modulo => $nomeModulo)
                <tr style="border-bottom:1px solid var(--border);" wire:key="row-{{ $modulo }}">
                    <td style="padding:14px 20px;">
                        <div style="display:flex;align-items:center;gap:8px;color:#1e293b;">
                            <span style="color:var(--muted);">{!! $iconModulo[$modulo] !!}</span>
                            <span style="font-size:13px;font-weight:500;">{{ $nomeModulo }}</span>
                        </div>
                    </td>
                    @foreach(\App\Livewire\PerfilPermissoes::$PERFIS as $perfil => $label)
                    <td style="padding:14px 16px;text-align:center;" wire:key="cell-{{ $perfil }}-{{ $modulo }}">
                        @php $permitido = $matriz[$perfil][$modulo] ?? false; @endphp
                        <button wire:click="toggle('{{ $perfil }}', '{{ $modulo }}')"
                                wire:loading.class="opacity-50"
                                wire:target="toggle('{{ $perfil }}', '{{ $modulo }}')"
                                style="background:none;border:none;cursor:pointer;padding:4px;border-radius:6px;transition:all .15s;"
                                title="{{ $permitido ? 'Clique para remover acesso' : 'Clique para conceder acesso' }}">
                            @if($permitido)
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="11" fill="#dcfce7" stroke="#16a34a" stroke-width="1.5"/>
                                <path d="M7 12l3.5 3.5L17 8.5" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @else
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="11" fill="#fee2e2" stroke="#dc2626" stroke-width="1.5"/>
                                <path d="M15 9l-6 6M9 9l6 6" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                            @endif
                        </button>
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top:14px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;font-size:12px;color:#92400e;display:flex;align-items:flex-start;gap:8px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px;"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
    As alterações têm efeito imediato — o usuário precisará recarregar a página para sentir a mudança. Permissões customizadas têm prioridade sobre o padrão do sistema.
</div>

</div>
