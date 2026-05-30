@section('page-title', 'Infraestrutura')
<div wire:poll.30000ms="atualizar">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <p style="font-size:12px;color:#94a3b8;">Auto-refresh a cada 30 segundos &nbsp;·&nbsp; <span wire:loading>atualizando…</span></p>
        <button wire:click="atualizar" class="btn btn-sm btn-outline">↻ Atualizar agora</button>
    </div>

    {{-- ── Servidor ── --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header"><span class="card-title">🖥️ Servidor</span></div>
        <div class="card-body">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;">

                {{-- CPU --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:600;">CPU</span>
                        <span style="font-size:13px;font-weight:700;color:{{ $servidor['cpu_percent'] > 80 ? '#dc2626' : ($servidor['cpu_percent'] > 60 ? '#d97706' : '#16a34a') }};">
                            {{ $servidor['cpu_percent'] }}%
                        </span>
                    </div>
                    <div class="meter">
                        <div class="meter-fill {{ $servidor['cpu_percent'] > 80 ? 'danger' : ($servidor['cpu_percent'] > 60 ? 'warn' : '') }}"
                             style="width:{{ $servidor['cpu_percent'] }}%;"></div>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">Load: {{ $servidor['cpu_load'] }}</div>
                </div>

                {{-- RAM --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:600;">Memória RAM</span>
                        <span style="font-size:13px;font-weight:700;color:{{ $servidor['ram_percent'] > 85 ? '#dc2626' : ($servidor['ram_percent'] > 70 ? '#d97706' : '#16a34a') }};">
                            {{ $servidor['ram_percent'] }}%
                        </span>
                    </div>
                    <div class="meter">
                        <div class="meter-fill {{ $servidor['ram_percent'] > 85 ? 'danger' : ($servidor['ram_percent'] > 70 ? 'warn' : '') }}"
                             style="width:{{ $servidor['ram_percent'] }}%;"></div>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                        {{ $servidor['ram_used_mb'] }} MB / {{ $servidor['ram_total_mb'] }} MB
                    </div>
                </div>

                {{-- Disco --}}
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:13px;font-weight:600;">Disco</span>
                        <span style="font-size:13px;font-weight:700;color:{{ $servidor['disk_percent'] > 90 ? '#dc2626' : ($servidor['disk_percent'] > 75 ? '#d97706' : '#16a34a') }};">
                            {{ $servidor['disk_percent'] }}%
                        </span>
                    </div>
                    <div class="meter">
                        <div class="meter-fill {{ $servidor['disk_percent'] > 90 ? 'danger' : ($servidor['disk_percent'] > 75 ? 'warn' : '') }}"
                             style="width:{{ $servidor['disk_percent'] }}%;"></div>
                    </div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                        {{ $servidor['disk_used_gb'] }} GB / {{ $servidor['disk_total_gb'] }} GB
                    </div>
                </div>

            </div>
            <div style="margin-top:14px;padding-top:14px;border-top:1px solid var(--border);font-size:12px;color:#64748b;">
                ⏱️ Uptime: <strong>{{ $servidor['uptime'] ?: 'N/A' }}</strong>
            </div>
        </div>
    </div>

    {{-- ── Fila de Jobs ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="card">
            <div class="card-header"><span class="card-title">📬 Fila de Jobs</span></div>
            <div class="card-body" style="display:flex;gap:20px;">
                <div style="text-align:center;flex:1;">
                    <div style="font-size:28px;font-weight:700;color:var(--primary);">{{ $fila['pendentes'] }}</div>
                    <div style="font-size:12px;color:#64748b;">Jobs Pendentes</div>
                </div>
                <div style="text-align:center;flex:1;">
                    <div style="font-size:28px;font-weight:700;color:{{ $fila['falhas_total'] > 0 ? '#dc2626' : '#16a34a' }};">{{ $fila['falhas_total'] }}</div>
                    <div style="font-size:12px;color:#64748b;">Jobs com Falha</div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">❌ Últimas Falhas</span></div>
            <div style="max-height:150px;overflow-y:auto;">
                @forelse($falhas as $f)
                <div style="padding:8px 16px;border-bottom:1px solid var(--border);font-size:12px;">
                    <div style="font-weight:600;color:#1e293b;">{{ $f['job'] }}</div>
                    <div style="color:#dc2626;margin-top:2px;font-family:monospace;font-size:11px;">{{ Str::limit($f['erro'], 100) }}</div>
                    <div style="color:#94a3b8;margin-top:2px;">{{ \Carbon\Carbon::parse($f['failed_at'])->diffForHumans() }}</div>
                </div>
                @empty
                <div style="padding:16px;text-align:center;color:#94a3b8;font-size:13px;">✅ Nenhuma falha registrada.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ── Logs ── --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📋 Laravel Log (últimas 20 linhas)</span>
            <button wire:click="limparLog" wire:confirm="Limpar o arquivo de log?"
                    class="btn btn-sm btn-danger">🗑️ Limpar Log</button>
        </div>
        <div style="background:#0f172a;border-radius:0 0 10px 10px;padding:16px;overflow-x:auto;">
            <pre style="font-family:monospace;font-size:11px;color:#94a3b8;white-space:pre-wrap;word-break:break-all;margin:0;max-height:300px;overflow-y:auto;">{{ $logConteudo ?: 'Log vazio.' }}</pre>
        </div>
    </div>

</div>
