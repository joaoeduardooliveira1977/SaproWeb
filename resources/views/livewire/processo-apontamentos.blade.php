<div
    x-data="{
        segundos: 0,
        intervalo: null,
        get display() {
            let h = Math.floor(this.segundos / 3600);
            let m = Math.floor((this.segundos % 3600) / 60);
            let s = this.segundos % 60;
            return String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        },
        iniciar() {
            this.intervalo = setInterval(() => {
                this.segundos = Math.floor(Date.now() / 1000) - $wire.timerInicio;
            }, 1000);
        },
        parar() {
            clearInterval(this.intervalo);
            this.intervalo = null;
            $wire.pararTimer(this.segundos);
            this.segundos = 0;
        }
    }"
    x-init="
        $watch('$wire.timerAtivo', val => {
            if (val) iniciar(); else { clearInterval(intervalo); intervalo = null; }
        });
    "
>

    {{-- ── Painel do Timer ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;
                background:{{ $timerAtivo ? '#f0fdf4' : '#f8fafc' }};
                border:2px solid {{ $timerAtivo ? '#86efac' : 'var(--border)' }};
                border-radius:12px;padding:18px 24px;margin-bottom:20px;transition:all .3s;">
        <div style="display:flex;align-items:center;gap:16px;">
            <span style="font-size:28px;">{{ $timerAtivo ? '⏱️' : '🕐' }}</span>
            <div>
                <div x-text="display"
                    style="font-size:32px;font-weight:800;font-family:monospace;
                           color:{{ $timerAtivo ? '#16a34a' : '#1e293b' }};line-height:1;
                           letter-spacing:2px;">
                    00:00:00
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:3px;">
                    {{ $timerAtivo ? '⚡ Cronômetro em andamento' : 'Pronto para iniciar' }}
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            @if(!$timerAtivo)
            <button wire:click="iniciarTimer"
                style="padding:10px 20px;background:#16a34a;color:#fff;border:none;border-radius:8px;
                       font-size:14px;font-weight:700;cursor:pointer;">
                ▶ Iniciar
            </button>
            <button wire:click="novoManual"
                style="padding:10px 16px;background:transparent;color:var(--muted);border:1.5px solid var(--border);
                       border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                + Manual
            </button>
            @else
            <button @click="parar()"
                style="padding:10px 20px;background:#dc2626;color:#fff;border:none;border-radius:8px;
                       font-size:14px;font-weight:700;cursor:pointer;animation:pulse 1.5s infinite;">
                ■ Parar
            </button>
            @endif
        </div>
    </div>

    <style>
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.7} }
    </style>

    {{-- ── Totais ── --}}
    @if(count($apontamentos) > 0)
    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
        <div style="flex:1;min-width:120px;background:#eff6ff;border-radius:8px;padding:12px 16px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#2563a8;">
                {{ number_format($totalHoras, 2, ',', '.') }}h
            </div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;">Total de Horas</div>
        </div>
        @if($totalValor > 0)
        <div style="flex:1;min-width:120px;background:#dcfce7;border-radius:8px;padding:12px 16px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#16a34a;">
                R$ {{ number_format($totalValor, 2, ',', '.') }}
            </div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;">Total em Valor</div>
        </div>
        @endif
        <div style="flex:1;min-width:120px;background:#f8fafc;border-radius:8px;padding:12px 16px;text-align:center;">
            <div style="font-size:20px;font-weight:800;color:#334155;">{{ count($apontamentos) }}</div>
            <div style="font-size:11px;color:#64748b;margin-top:2px;">Registros</div>
        </div>
    </div>
    @endif

    {{-- ── Tabela ── --}}
    @if(empty($apontamentos))
    <p style="text-align:center;color:var(--muted);font-size:13px;padding:24px 0;">
        Nenhum apontamento registrado. Use o timer ou clique em "+ Manual".
    </p>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Advogado</th>
                    <th style="text-align:center;">Horas</th>
                    <th style="text-align:right;">Valor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($apontamentos as $a)
                <tr>
                    <td style="white-space:nowrap;font-size:12px;color:var(--muted);">
                        {{ \Carbon\Carbon::parse($a->data)->format('d/m/Y') }}
                    </td>
                    <td style="font-size:13px;">{{ $a->descricao }}</td>
                    <td style="font-size:12px;color:var(--muted);">{{ $a->advogado_nome ?? '—' }}</td>
                    <td style="text-align:center;">
                        <span style="font-weight:700;color:#2563a8;font-family:monospace;">
                            {{ number_format($a->horas, 2, ',', '.') }}h
                        </span>
                    </td>
                    <td style="text-align:right;font-size:12px;">
                        {{ $a->valor ? 'R$ '.number_format($a->valor, 2, ',', '.') : '—' }}
                    </td>
                    <td style="white-space:nowrap;">
                        <button wire:click="editar({{ $a->id }})"
                            style="background:none;border:none;cursor:pointer;font-size:14px;padding:3px 5px;" title="Editar">✏️</button>
                        <button wire:click="excluir({{ $a->id }})"
                            wire:confirm="Excluir este apontamento?"
                            style="background:none;border:none;cursor:pointer;font-size:14px;padding:3px 5px;" title="Excluir">🗑️</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#f1f5f9;font-weight:700;">
                    <td colspan="3" style="padding:8px 14px;font-size:12px;color:#64748b;">Total</td>
                    <td style="text-align:center;color:#2563a8;font-family:monospace;">
                        {{ number_format($totalHoras, 2, ',', '.') }}h
                    </td>
                    <td style="text-align:right;">
                        {{ $totalValor > 0 ? 'R$ '.number_format($totalValor, 2, ',', '.') : '' }}
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endif

    {{-- ── Modal Salvar Apontamento ── --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal" style="max-width:480px;">
            <div class="modal-header">
                <span class="modal-title">
                    {{ $editandoId ? '✏️ Editar Apontamento' : '⏱️ Salvar Apontamento' }}
                </span>
                <button wire:click="fecharModal" class="modal-close">×</button>
            </div>

            <div class="form-field" style="margin-bottom:14px;">
                <label class="lbl">Descrição da atividade *</label>
                <textarea wire:model="descricao" rows="3"
                    placeholder="O que foi feito neste período?"
                    style="resize:vertical;"></textarea>
                @error('descricao')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data *</label>
                    <input type="date" wire:model="data">
                    @error('data')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">Horas *</label>
                    <input type="number" wire:model="horas" step="0.01" min="0.01" placeholder="0.00">
                    @error('horas')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Valor (R$)</label>
                    <input type="number" wire:model="valor" step="0.01" min="0" placeholder="Opcional">
                </div>
                <div class="form-field">
                    <label class="lbl">Advogado</label>
                    <select wire:model="advogado_id">
                        <option value="">— Nenhum —</option>
                        @foreach($advogados as $adv)
                        <option value="{{ $adv->id }}">{{ $adv->nome }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success">✓ Salvar</button>
            </div>
        </div>
    </div>
    @endif

</div>
