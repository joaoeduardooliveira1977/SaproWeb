<div>
@verbatim
<style>
.juri-card {
    background:#fff;border:1px solid #e2e8f0;border-radius:10px;
    padding:16px;margin-bottom:12px;transition:box-shadow .15s;
}
.juri-card:hover { box-shadow:0 4px 16px rgba(0,0,0,.08); }
.juri-badge {
    display:inline-block;padding:2px 10px;border-radius:20px;
    font-size:11px;font-weight:700;letter-spacing:.3px;
}
.juri-ementa {
    font-size:12px;color:#475569;line-height:1.6;
    display:-webkit-box;-webkit-line-clamp:4;-webkit-box-orient:vertical;overflow:hidden;
    margin-top:6px;
}
.juri-ementa.expanded {
    display:block;-webkit-line-clamp:unset;
}
.juri-meta { font-size:11px;color:#94a3b8;margin-top:6px;display:flex;gap:14px;flex-wrap:wrap; }
.juri-meta span { display:flex;align-items:center;gap:4px; }
.juri-actions { display:flex;gap:8px;flex-wrap:wrap;margin-top:10px; }
.juri-section-title {
    font-size:13px;font-weight:700;color:#1e293b;
    padding:10px 0 6px;border-bottom:2px solid #e2e8f0;margin-bottom:14px;
    display:flex;align-items:center;gap:8px;
}
.juri-links-bar {
    display:flex;gap:6px;flex-wrap:wrap;margin-top:8px;margin-bottom:18px;
}
.juri-link-btn {
    padding:5px 14px;font-size:12px;font-weight:600;cursor:pointer;
    border-radius:6px;border:1.5px solid #cbd5e1;color:#475569;
    background:#f8fafc;text-decoration:none;display:inline-flex;align-items:center;gap:5px;
    transition:all .15s;
}
.juri-link-btn:hover { background:#e2e8f0;color:#1e293b;border-color:#94a3b8; }
</style>
@endverbatim

{{-- ── Barra de busca ── --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-header">
        <span class="card-title">&#9878; Pesquisa de Jurisprudência</span>
    </div>

    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;margin-bottom:12px;">
        <div style="flex:1;min-width:260px;">
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Termos de busca</label>
            <input wire:model="busca" type="text" placeholder="ex: dano moral, rescisão contratual…"
                style="width:100%;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;"
                wire:keydown.enter="pesquisar">
        </div>
        <div style="min-width:140px;">
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Tribunal</label>
            <select wire:model="tribunal"
                style="width:100%;padding:9px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;background:#fff;outline:none;">
                <option value="stj">STJ</option>
                <option value="stf">STF</option>
                <option value="tjsp">TJSP</option>
                <option value="trf1">TRF-1</option>
                <option value="trf3">TRF-3</option>
                <option value="jusbrasil">Jusbrasil</option>
            </select>
        </div>
        <button wire:click="pesquisar" wire:loading.attr="disabled"
            style="padding:9px 22px;background:var(--primary);color:#fff;border:none;border-radius:8px;
                   font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
            <span wire:loading.remove wire:target="pesquisar">&#128269; Pesquisar</span>
            <span wire:loading wire:target="pesquisar">Buscando…</span>
        </button>
        <button wire:click="abrirForm"
            style="padding:9px 18px;background:#f1f5f9;color:#374151;border:1.5px solid #cbd5e1;border-radius:8px;
                   font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
            + Manual
        </button>
    </div>

    {{-- Links rápidos --}}
    <div class="juri-links-bar">
        <span style="font-size:11px;color:var(--muted);font-weight:600;align-self:center;">Buscar diretamente em:</span>
        @foreach(['stj','stf','tjsp','trf1','trf3','jusbrasil'] as $trib)
        <a href="{{ $this->urlBusca($trib) }}" target="_blank" class="juri-link-btn">
            &#8599; {{ strtoupper($trib) }}
        </a>
        @endforeach
    </div>
</div>

{{-- ── Formulário manual / edição ── --}}
@if($formAberto)
<div class="card" style="margin-bottom:16px;border:2px solid var(--primary);">
    <div class="card-header">
        <span class="card-title">{{ $editandoId ? 'Editar Jurisprudência' : 'Adicionar Jurisprudência Manualmente' }}</span>
        <button wire:click="$set('formAberto', false)"
            style="background:none;border:none;cursor:pointer;font-size:18px;color:var(--muted);">✕</button>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Tribunal *</label>
            <input wire:model="formTribunal" type="text" placeholder="STJ"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
            @error('formTribunal') <span style="color:#dc2626;font-size:11px;">{{ $message }}</span> @enderror
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Número do Acórdão</label>
            <input wire:model="formNumero" type="text" placeholder="REsp 1234567/SP"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Relator</label>
            <input wire:model="formRelator" type="text" placeholder="Min. Nome Sobrenome"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
        </div>
        <div>
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Data do Julgamento</label>
            <input wire:model="formData" type="date"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
            @error('formData') <span style="color:#dc2626;font-size:11px;">{{ $message }}</span> @enderror
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Ementa</label>
            <textarea wire:model="formEmenta" rows="4" placeholder="Texto da ementa…"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;"></textarea>
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">URL do Acórdão</label>
            <input wire:model="formUrl" type="url" placeholder="https://…"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;">
            @error('formUrl') <span style="color:#dc2626;font-size:11px;">{{ $message }}</span> @enderror
        </div>
        <div style="grid-column:1/-1;">
            <label style="font-size:11px;font-weight:600;color:var(--muted);display:block;margin-bottom:4px;">Observações internas</label>
            <textarea wire:model="formObs" rows="2" placeholder="Notas sobre como esta decisão se aplica ao processo…"
                style="width:100%;padding:8px 12px;border:1.5px solid #cbd5e1;border-radius:8px;font-size:13px;outline:none;box-sizing:border-box;resize:vertical;"></textarea>
        </div>
    </div>
    <div style="display:flex;gap:10px;margin-top:14px;">
        <button wire:click="salvar"
            style="padding:9px 24px;background:var(--primary);color:#fff;border:none;border-radius:8px;
                   font-size:13px;font-weight:600;cursor:pointer;">
            {{ $editandoId ? 'Atualizar' : 'Salvar no Processo' }}
        </button>
        <button wire:click="$set('formAberto', false)"
            style="padding:9px 18px;background:#f1f5f9;color:#374151;border:1.5px solid #cbd5e1;border-radius:8px;
                   font-size:13px;font-weight:600;cursor:pointer;">
            Cancelar
        </button>
    </div>
</div>
@endif

{{-- ── Resultados da busca ── --}}
@if($buscaRealizada)
<div class="card" style="margin-bottom:16px;">
    <div class="juri-section-title">
        <svg aria-hidden="true" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        Resultados da busca
        @if(count($resultados) > 0)
            <span class="juri-badge" style="background:#dbeafe;color:#1d4ed8;">{{ count($resultados) }} encontrados</span>
        @endif
    </div>

    @if(count($resultados) === 0)
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg aria-hidden="true" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <div class="empty-state-title">Nenhum resultado encontrado via API</div>
            <div class="empty-state-sub">Use os links acima para buscar diretamente no site do tribunal.</div>
        </div>
    @else
        @foreach($resultados as $idx => $res)
        <div class="juri-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span class="juri-badge" style="background:#dbeafe;color:#1d4ed8;">{{ $res['tribunal'] }}</span>
                    @if($res['numero'])
                    <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ $res['numero'] }}</span>
                    @endif
                </div>
                <div class="juri-actions">
                    <button wire:click="preencherFormulario({{ json_encode($res) }})"
                        style="padding:5px 14px;background:#f0fdf4;color:#16a34a;border:1.5px solid #86efac;
                               border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                        &#8594; Citar no Processo
                    </button>
                    @if($res['url'])
                    <a href="{{ $res['url'] }}" target="_blank"
                        style="padding:5px 12px;background:#f8fafc;color:#475569;border:1.5px solid #cbd5e1;
                               border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                        &#8599; Acórdão
                    </a>
                    @endif
                </div>
            </div>
            <div class="juri-ementa" id="ementa-{{ $idx }}">{{ $res['ementa'] ?: 'Ementa não disponível.' }}</div>
            @if(strlen($res['ementa'] ?? '') > 300)
            <button onclick="toggleEmenta({{ $idx }})" style="background:none;border:none;color:var(--primary);font-size:11px;font-weight:600;cursor:pointer;margin-top:4px;padding:0;">
                ver mais ▼
            </button>
            @endif
            <div class="juri-meta">
                @if($res['relator'])
                <span>&#9878; {{ $res['relator'] }}</span>
                @endif
                @if($res['data'])
                <span>&#128197; {{ \Carbon\Carbon::parse($res['data'])->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
        @endforeach
    @endif
</div>
@endif

{{-- ── Jurisprudências salvas no processo ── --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">
            <svg aria-hidden="true" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="vertical-align:-2px"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
            Citadas neste Processo
            @if($salvas->count() > 0)
            <span class="juri-badge" style="background:#f0fdf4;color:#16a34a;margin-left:6px;">{{ $salvas->count() }}</span>
            @endif
        </span>
        <button wire:click="abrirForm"
            style="padding:5px 14px;background:var(--primary);color:#fff;border:none;border-radius:6px;
                   font-size:12px;font-weight:600;cursor:pointer;">
            + Adicionar
        </button>
    </div>

    @if($salvas->isEmpty())
        <div class="empty-state" style="padding:30px 0;">
            <div class="empty-state-icon">
                <svg aria-hidden="true" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
            </div>
            <div class="empty-state-title">Nenhuma jurisprudência citada</div>
            <div class="empty-state-sub">Pesquise acima e clique em "Citar no Processo".</div>
        </div>
    @else
        @foreach($salvas as $j)
        <div class="juri-card">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span class="juri-badge" style="background:#dbeafe;color:#1d4ed8;">{{ $j->tribunal }}</span>
                    @if($j->numero_acordao)
                    <span style="font-size:13px;font-weight:700;color:#1e293b;">{{ $j->numero_acordao }}</span>
                    @endif
                    @if($j->data_julgamento)
                    <span style="font-size:11px;color:#94a3b8;">{{ $j->data_julgamento->format('d/m/Y') }}</span>
                    @endif
                </div>
                <div class="juri-actions">
                    <button wire:click="editarSalva({{ $j->id }})"
                        style="padding:4px 12px;background:#f8fafc;color:#475569;border:1.5px solid #cbd5e1;
                               border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                        Editar
                    </button>
                    <button wire:click="excluir({{ $j->id }})"
                        wire:confirm="Remover esta jurisprudência do processo?"
                        style="padding:4px 12px;background:#fff5f5;color:#dc2626;border:1.5px solid #fca5a5;
                               border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;">
                        Remover
                    </button>
                    @if($j->url)
                    <a href="{{ $j->url }}" target="_blank"
                        style="padding:4px 12px;background:#f8fafc;color:#475569;border:1.5px solid #cbd5e1;
                               border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                        &#8599; Ver
                    </a>
                    @endif
                </div>
            </div>
            @if($j->ementa)
            <div class="juri-ementa" style="margin-top:8px;">{{ $j->ementa }}</div>
            @endif
            @if($j->relator)
            <div class="juri-meta"><span>&#9878; {{ $j->relator }}</span></div>
            @endif
            @if($j->observacoes)
            <div style="margin-top:8px;padding:8px 12px;background:#fefce8;border-left:3px solid #fbbf24;
                        border-radius:0 6px 6px 0;font-size:12px;color:#92400e;">
                <strong>Obs:</strong> {{ $j->observacoes }}
            </div>
            @endif
        </div>
        @endforeach
    @endif
</div>

<script>
function toggleEmenta(idx) {
    var el = document.getElementById('ementa-' + idx);
    if (!el) return;
    var btn = el.nextElementSibling;
    if (el.classList.contains('expanded')) {
        el.classList.remove('expanded');
        if (btn) btn.textContent = 'ver mais ▼';
    } else {
        el.classList.add('expanded');
        if (btn) btn.textContent = 'ver menos ▲';
    }
}
</script>
</div>
