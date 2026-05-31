<div>

    <div style="margin-bottom:20px;">
        <h2 style="font-size:18px;font-weight:800;color:var(--text);margin-bottom:4px;">📢 Comunicados do Sistema</h2>
        <p style="font-size:13px;color:var(--muted);">Avisos, manutenções e novidades da plataforma.</p>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">
        <select wire:model.live="filtroTipo" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;background:var(--white);">
            <option value="">Todos os tipos</option>
            @foreach(\App\Models\Comunicado::$tipos as $val => $t)
            <option value="{{ $val }}">{{ $t['icon'] }} {{ $t['label'] }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroLeitura" style="padding:8px 12px;border:1.5px solid var(--border);border-radius:7px;font-size:13px;background:var(--white);">
            <option value="">Todos</option>
            <option value="nao_lido">Não lidos</option>
            <option value="lido">Lidos</option>
        </select>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
        @forelse($comunicados as $c)
        @php $ti = $c['info']; @endphp
        <div style="background:var(--white);border-radius:10px;border:1px solid {{ $c['lido'] ? 'var(--border)' : $ti['border'] }};padding:16px 18px;display:flex;gap:14px;align-items:flex-start;box-shadow:0 1px 4px rgba(0,0,0,.05);">

            <div style="font-size:24px;flex-shrink:0;margin-top:2px;">{{ $ti['icon'] }}</div>

            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;">
                    <span style="font-size:14px;font-weight:700;color:{{ $c['lido'] ? 'var(--muted)' : 'var(--text)' }};">
                        {{ $c['titulo'] }}
                    </span>
                    @if(!$c['lido'])
                    <span style="background:{{ $ti['bg'] }};color:{{ $ti['cor'] }};font-size:10px;font-weight:700;padding:2px 8px;border-radius:99px;border:1px solid {{ $ti['border'] }};">
                        NOVO
                    </span>
                    @endif
                </div>
                <div style="font-size:13px;color:var(--muted);line-height:1.6;white-space:pre-line;margin-bottom:8px;">{{ $c['mensagem'] }}</div>
                <div style="font-size:11px;color:#94a3b8;">
                    {{ $c['data']->format('d/m/Y H:i') }}
                    &nbsp;·&nbsp;
                    <span style="color:{{ $ti['cor'] }};font-weight:600;">{{ $ti['label'] }}</span>
                </div>
            </div>

            @if(!$c['lido'])
            <button wire:click="marcarLido({{ $c['id'] }})"
                    style="flex-shrink:0;padding:6px 14px;background:#f1f5f9;color:var(--muted);border:1px solid var(--border);border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;">
                ✓ Marcar lido
            </button>
            @endif

        </div>
        @empty
        <div style="text-align:center;padding:48px;color:#94a3b8;">
            <div style="font-size:36px;margin-bottom:10px;">📭</div>
            <div style="font-size:14px;">Nenhum comunicado no momento.</div>
        </div>
        @endforelse
    </div>

</div>
