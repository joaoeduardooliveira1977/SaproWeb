<div wire:poll.30000ms="atualizar">
@section('page-title', 'Infraestrutura')

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;">
        <p style="font-size:12px;color:#94a3b8;">Auto-refresh a cada 30 segundos &nbsp;Â·&nbsp; <span wire:loading>atualizandoâ€¦</span></p>
        <button wire:click="atualizar" class="btn btn-sm btn-outline">â†» Atualizar agora</button>
    </div>

    {{-- â”€â”€ Servidor â”€â”€ --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header"><span class="card-title">ðŸ–¥ï¸ Servidor</span></div>
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
                        <span style="font-size:13px;font-weight:600;">MemÃ³ria RAM</span>
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
                â±ï¸ Uptime: <strong>{{ $servidor['uptime'] ?: 'N/A' }}</strong>
            </div>
        </div>
    </div>

    {{-- â”€â”€ Fila de Jobs â”€â”€ --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="card">
            <div class="card-header"><span class="card-title">ðŸ“¬ Fila de Jobs</span></div>
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
            <div class="card-header">
                <span class="card-title">âŒ Ãšltimas Falhas</span>
                @if(count($falhas))
                <button wire:click="retryTodos" wire:confirm="Reenfileirar todos os jobs com falha?"
                        class="btn btn-sm btn-primary">â†» Retentar todos</button>
                @endif
            </div>
            <div style="max-height:220px;overflow-y:auto;">
                @forelse($falhas as $f)
                <div style="padding:10px 16px;border-bottom:1px solid var(--border);font-size:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                        <div style="flex:1;">
                            <div style="font-weight:600;color:#1e293b;">{{ $f['job'] }}</div>
                            <div style="color:#dc2626;margin-top:2px;font-family:monospace;font-size:11px;word-break:break-all;">{{ Str::limit($f['erro'], 120) }}</div>
                            <div style="color:#94a3b8;margin-top:3px;">{{ \Carbon\Carbon::parse($f['failed_at'])->diffForHumans() }} Â· fila: {{ $f['queue'] }}</div>
                        </div>
                        <div style="display:flex;gap:5px;flex-shrink:0;">
                            <button wire:click="retryJob({{ $f['id'] }})"
                                    class="btn btn-sm" style="background:#eff6ff;color:#2563a8;">â†» Retry</button>
                            <button wire:click="deleteJob({{ $f['id'] }})"
                                    wire:confirm="Remover este job permanentemente?"
                                    class="btn btn-sm" style="background:#fee2e2;color:#dc2626;">âœ•</button>
                        </div>
                    </div>
                </div>
                @empty
                <div style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">âœ… Nenhuma falha registrada.</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- â”€â”€ Logs â”€â”€ --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">ðŸ“‹ Laravel Log (Ãºltimas 20 linhas)</span>
            <button wire:click="limparLog" wire:confirm="Limpar o arquivo de log?"
                    class="btn btn-sm btn-danger">ðŸ—‘ï¸ Limpar Log</button>
        </div>
        <div style="background:#0f172a;border-radius:0 0 10px 10px;padding:16px;overflow-x:auto;">
            <pre style="font-family:monospace;font-size:11px;color:#94a3b8;white-space:pre-wrap;word-break:break-all;margin:0;max-height:300px;overflow-y:auto;">{{ $logConteudo ?: 'Log vazio.' }}</pre>
        </div>
    </div>

</div>

