<div>

{{-- Flash messages --}}
@if(session('sucesso'))
<div style="margin-bottom:16px;padding:12px 16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#065f46;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('sucesso') }}
</div>
@endif
@if(session('erro'))
<div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px;font-weight:600;display:flex;align-items:center;gap:8px;">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    {{ session('erro') }}
</div>
@endif

{{-- Cabeçalho --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:#1e3a5f;margin:0;">Custas a Reembolsar</h1>
        <p style="font-size:13px;color:#64748b;margin:4px 0 0;">Despesas pagas pelo escritório marcadas para reembolso pelo cliente</p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
        @if($processoId && $totalPendente > 0)
        <button wire:click="gerarTodos" wire:confirm="Gerar reembolso de todas as custas pendentes deste processo?"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 16px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="17 1 21 5 17 9"/><path d="M3 11V9a4 4 0 014-4h14"/><polyline points="7 23 3 19 7 15"/><path d="M21 13v2a4 4 0 01-4 4H3"/></svg>
            Gerar todos (R$ {{ number_format($totalPendente, 2, ',', '.') }})
        </button>
        @endif
    </div>
</div>

{{-- Card resumo (quando filtrado por processo) --}}
@if($processoId && $totalPendente > 0)
<div style="background:#faf5ff;border:1px solid #ddd6fe;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;">
    <div style="width:40px;height:40px;border-radius:10px;background:#ede9fe;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
    </div>
    <div>
        <div style="font-size:13px;color:#6d28d9;font-weight:600;">Total pendente de reembolso neste processo</div>
        <div style="font-size:24px;font-weight:800;color:#5b21b6;">R$ {{ number_format($totalPendente, 2, ',', '.') }}</div>
    </div>
</div>
@endif

{{-- Filtros --}}
<div style="display:flex;gap:10px;align-items:center;margin-bottom:18px;flex-wrap:wrap;">
    <span style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;">Situação:</span>
    @foreach([
        'todos'                => ['label' => 'Todas',              'bg' => '#f1f5f9', 'cor' => '#475569'],
        'pendente_cobranca'    => ['label' => 'Pendente de Cobrar', 'bg' => '#fef9c3', 'cor' => '#854d0e'],
        'cobrado'              => ['label' => 'Cobrado',            'bg' => '#dcfce7', 'cor' => '#15803d'],
        'aguardando_pagamento' => ['label' => 'Ag. Pagamento',      'bg' => '#f1f5f9', 'cor' => '#64748b'],
    ] as $val => $cfg)
    <button wire:click="$set('situacao', '{{ $val }}')"
        style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;border:none;cursor:pointer;
               background:{{ $situacao === $val ? $cfg['bg'] : '#fff' }};
               color:{{ $situacao === $val ? $cfg['cor'] : '#64748b' }};
               border:1.5px solid {{ $situacao === $val ? $cfg['cor'] : '#e2e8f0' }};">
        {{ $cfg['label'] }}
    </button>
    @endforeach
</div>

{{-- Tabela --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    @if($custas->isEmpty())
    <div style="padding:48px;text-align:center;color:#94a3b8;">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 12px;display:block;opacity:.4;"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
        <div style="font-size:14px;font-weight:600;">Nenhuma custa encontrada</div>
        <div style="font-size:12px;margin-top:4px;">Marque despesas de processo como "reembolsável" para elas aparecerem aqui.</div>
    </div>
    @else
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Descrição</th>
                <th style="padding:10px 16px;text-align:right;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Valor Pago</th>
                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Data</th>
                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Processo</th>
                <th style="padding:10px 16px;text-align:left;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Cliente</th>
                <th style="padding:10px 16px;text-align:center;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Situação</th>
                <th style="padding:10px 16px;text-align:right;font-weight:600;color:#64748b;font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Ação</th>
            </tr>
        </thead>
        <tbody>
            @foreach($custas as $custa)
            <tr style="border-bottom:1px solid #f1f5f9;vertical-align:middle;">
                <td style="padding:12px 16px;">
                    <div style="font-weight:600;color:#1e293b;">{{ $custa->descricao }}</div>
                </td>
                <td style="padding:12px 16px;text-align:right;font-weight:700;color:#1e293b;white-space:nowrap;">
                    R$ {{ number_format($custa->valor_pago ?: $custa->valor, 2, ',', '.') }}
                </td>
                <td style="padding:12px 16px;color:#64748b;white-space:nowrap;">
                    {{ $custa->data_pagamento ? \Carbon\Carbon::parse($custa->data_pagamento)->format('d/m/Y') : '—' }}
                </td>
                <td style="padding:12px 16px;">
                    <a href="{{ route('processos.show', $custa->processo_id) }}"
                       style="color:#2563eb;text-decoration:none;font-weight:600;font-size:12px;">
                        {{ $custa->numero_processo }}
                    </a>
                </td>
                <td style="padding:12px 16px;color:#475569;">{{ $custa->cliente_nome }}</td>
                <td style="padding:12px 16px;text-align:center;">
                    @php
                        $badge = match($custa->situacao_reembolso) {
                            'cobrado'              => ['bg' => '#dcfce7', 'cor' => '#15803d', 'label' => 'Cobrado'],
                            'pendente_cobranca'    => ['bg' => '#fef9c3', 'cor' => '#854d0e', 'label' => 'Pendente'],
                            'aguardando_pagamento' => ['bg' => '#f1f5f9', 'cor' => '#64748b', 'label' => 'Ag. Pagamento'],
                            default                => ['bg' => '#f1f5f9', 'cor' => '#64748b', 'label' => $custa->situacao_reembolso],
                        };
                    @endphp
                    <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:{{ $badge['bg'] }};color:{{ $badge['cor'] }};">
                        {{ $badge['label'] }}
                    </span>
                </td>
                <td style="padding:12px 16px;text-align:right;">
                    @if($custa->situacao_reembolso === 'pendente_cobranca')
                    <button wire:click="abrirModal({{ $custa->id }})"
                        style="padding:6px 12px;background:#7c3aed;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
                        Gerar reembolso
                    </button>
                    @elseif($custa->recebimento_reembolso_id)
                    <span style="font-size:11px;color:#16a34a;font-weight:600;">✓ Gerado</span>
                    @else
                    <span style="font-size:11px;color:#94a3b8;">—</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Modal de confirmação --}}
@if($modalConfirmar)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;">
    <div style="background:#fff;border-radius:14px;width:100%;max-width:440px;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;">
            <div style="font-size:16px;font-weight:800;color:#1e3a5f;">Confirmar geração de reembolso</div>
            <div style="font-size:12px;color:#64748b;margin-top:3px;">Será criado um recebimento pendente vinculado ao processo.</div>
        </div>
        <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px;">
            <div>
                <label style="display:block;font-size:12px;font-weight:600;color:#475569;margin-bottom:5px;">Observação (opcional)</label>
                <input wire:model="observacao" type="text" placeholder="Ex: Taxa cobrada em audiência de 15/04"
                    style="width:100%;padding:9px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:13px;box-sizing:border-box;">
            </div>
            <div style="display:flex;gap:10px;">
                <button wire:click="confirmarReembolso"
                    style="flex:1;padding:10px;background:#7c3aed;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                    Confirmar
                </button>
                <button wire:click="$set('modalConfirmar', false)"
                    style="padding:10px 16px;background:#f1f5f9;color:#475569;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
