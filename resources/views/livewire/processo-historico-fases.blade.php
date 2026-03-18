<div>
    @if(empty($historico))
    <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
        Nenhuma mudança de fase registrada ainda.<br>
        <span style="font-size:12px;">As mudanças serão registradas automaticamente ao editar o processo.</span>
    </p>
    @else

    {{-- Timeline --}}
    <div style="position:relative;padding-left:28px;">

        {{-- Linha vertical --}}
        <div style="position:absolute;left:9px;top:6px;bottom:6px;width:2px;background:#e2e8f0;border-radius:2px;"></div>

        @foreach($historico as $i => $item)
        @php
            $data = \Carbon\Carbon::parse($item->created_at);
        @endphp
        <div style="position:relative;margin-bottom:{{ $loop->last ? '0' : '20px' }};">

            {{-- Bolinha da timeline --}}
            <div style="position:absolute;left:-24px;top:3px;width:12px;height:12px;border-radius:50%;
                        background:{{ $item->fase_anterior ? '#2563a8' : '#16a34a' }};
                        border:2px solid white;box-shadow:0 0 0 2px #e2e8f0;"></div>

            {{-- Card do evento --}}
            <div style="background:#f8fafc;border:1px solid var(--border);border-radius:8px;padding:12px 14px;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:6px;">

                    {{-- Descrição da mudança --}}
                    <div style="font-size:13px;color:#1e293b;">
                        @if($item->fase_anterior)
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;background:#f1f5f9;color:#64748b;font-size:12px;font-weight:600;">
                            {{ $item->fase_anterior }}
                        </span>
                        <span style="margin:0 6px;color:#94a3b8;font-size:14px;">→</span>
                        @else
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;background:#dcfce7;color:#16a34a;font-size:12px;font-weight:700;">
                            Fase inicial
                        </span>
                        <span style="margin:0 6px;color:#94a3b8;font-size:14px;">→</span>
                        @endif
                        <span style="display:inline-block;padding:2px 10px;border-radius:12px;background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;">
                            {{ $item->fase_nova ?? '—' }}
                        </span>
                    </div>

                    {{-- Data e usuário --}}
                    <div style="text-align:right;font-size:11px;color:var(--muted);white-space:nowrap;">
                        <div>{{ $data->format('d/m/Y H:i') }}</div>
                        @if($item->usuario_nome)
                        <div style="margin-top:2px;display:inline-flex;align-items:center;gap:4px;">
                            <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            {{ $item->usuario_nome }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <p style="font-size:11px;color:var(--muted);margin-top:16px;">
        {{ count($historico) }} {{ count($historico) === 1 ? 'alteração registrada' : 'alterações registradas' }}
    </p>
    @endif
</div>
