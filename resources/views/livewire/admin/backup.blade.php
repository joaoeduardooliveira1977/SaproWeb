<div>
    {{-- Cabeçalho --}}
    <div style="display:flex;align-items:center;justify-content:space-between;
                margin-bottom:24px;flex-wrap:wrap;gap:12px;">
        <div>
            <h1 style="font-size:20px;font-weight:700;color:var(--text);margin:0;">
                Backup do Banco de Dados
            </h1>
            <p style="font-size:13px;color:var(--muted);margin:4px 0 0;">
                Backup automático diário às 02:00h — retenção de 7 dias
            </p>
        </div>
        <button wire:click="executarBackup"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50"
                style="display:flex;align-items:center;gap:8px;padding:10px 20px;
                       background:var(--primary);color:#fff;border:none;border-radius:8px;
                       font-size:13px;font-weight:700;cursor:pointer;">
            <span wire:loading.remove wire:target="executarBackup">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5">
                    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
            </span>
            <span wire:loading wire:target="executarBackup" style="font-size:13px;">⏳</span>
            <span wire:loading.remove wire:target="executarBackup">Fazer Backup Agora</span>
            <span wire:loading wire:target="executarBackup">Gerando backup...</span>
        </button>
    </div>

    {{-- Mensagem de resultado --}}
    @if($mensagem)
    <div style="padding:12px 16px;border-radius:8px;margin-bottom:20px;font-size:13px;
                font-weight:600;
                background:{{ $tipoMensagem === 'success' ? '#dcfce7' : '#fef2f2' }};
                color:{{ $tipoMensagem === 'success' ? '#166534' : '#991b1b' }};
                border:1px solid {{ $tipoMensagem === 'success' ? '#bbf7d0' : '#fecaca' }};">
        {{ $mensagem }}
    </div>
    @endif

    {{-- Cards de status --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
                gap:12px;margin-bottom:24px;">
        <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;
                    padding:16px;border-left:4px solid #22c55e;">
            <div style="font-size:11px;color:var(--muted);font-weight:600;
                        text-transform:uppercase;margin-bottom:6px;">Backup Automático</div>
            <div style="font-size:14px;font-weight:700;color:#166534;">Ativo</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Todo dia às 02:00h</div>
        </div>
        <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;
                    padding:16px;border-left:4px solid var(--primary);">
            <div style="font-size:11px;color:var(--muted);font-weight:600;
                        text-transform:uppercase;margin-bottom:6px;">Backups Disponíveis</div>
            <div style="font-size:22px;font-weight:700;color:var(--primary);">
                {{ count($backups) }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;">Últimos 7 dias</div>
        </div>
        <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;
                    padding:16px;border-left:4px solid #d97706;">
            <div style="font-size:11px;color:var(--muted);font-weight:600;
                        text-transform:uppercase;margin-bottom:6px;">Último Backup</div>
            <div style="font-size:13px;font-weight:700;color:var(--text);">
                {{ $backups[0]['data'] ?? '—' }}
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                {{ $backups[0]['idade'] ?? 'Nenhum backup encontrado' }}
            </div>
        </div>
        <div style="background:var(--white);border:1px solid var(--border);border-radius:10px;
                    padding:16px;border-left:4px solid #7c3aed;">
            <div style="font-size:11px;color:var(--muted);font-weight:600;
                        text-transform:uppercase;margin-bottom:6px;">Retenção</div>
            <div style="font-size:22px;font-weight:700;color:#7c3aed;">7 dias</div>
            <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                Backups mais antigos removidos automaticamente
            </div>
        </div>
    </div>

    {{-- Lista de backups --}}
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;
                overflow:hidden;margin-bottom:20px;">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);
                    display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:700;color:var(--text);">
                Backups Disponíveis
            </span>
            <button wire:click="carregarBackups"
                    style="font-size:12px;color:var(--primary);background:none;
                           border:none;cursor:pointer;font-weight:600;">
                Atualizar
            </button>
        </div>

        @if(empty($backups))
            <div style="padding:32px;text-align:center;color:var(--muted);font-size:13px;">
                Nenhum backup encontrado. Clique em "Fazer Backup Agora" para criar o primeiro.
            </div>
        @else
            @foreach($backups as $backup)
            <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;
                        border-bottom:1px solid var(--border);">
                <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;
                            display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="#166534" stroke-width="2">
                        <ellipse cx="12" cy="5" rx="9" ry="3"/>
                        <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/>
                        <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>
                    </svg>
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-size:13px;font-weight:600;color:var(--text);font-family:monospace;">
                        {{ $backup['nome'] }}
                    </div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px;">
                        {{ $backup['data'] }} — {{ $backup['idade'] }}
                    </div>
                </div>
                <div style="font-size:12px;font-weight:700;color:var(--primary);
                            background:#eff6ff;padding:3px 10px;border-radius:20px;">
                    {{ $backup['tamanho'] }}
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Log --}}
    <div style="background:var(--white);border:1px solid var(--border);border-radius:12px;
                overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid var(--border);
                    display:flex;align-items:center;justify-content:space-between;">
            <span style="font-size:14px;font-weight:700;color:var(--text);">
                Log de Execuções
            </span>
            <button wire:click="carregarLog"
                    style="font-size:12px;color:var(--primary);background:none;
                           border:none;cursor:pointer;font-weight:600;">
                Ver Log
            </button>
        </div>

        @if(!empty($logLinhas))
        <div style="padding:16px 18px;background:#0f172a;border-radius:0 0 12px 12px;
                    max-height:250px;overflow-y:auto;">
            @foreach($logLinhas as $linha)
            <div style="font-size:11px;font-family:monospace;margin-bottom:4px;
                        color:{{ str_contains($linha,'OK') || str_contains($linha,'Concluído') ? '#86efac' : (str_contains($linha,'ERRO') ? '#fca5a5' : '#94a3b8') }};">
                {{ $linha }}
            </div>
            @endforeach
        </div>
        @else
        <div style="padding:20px 18px;text-align:center;color:var(--muted);font-size:12px;">
            Clique em "Ver Log" para carregar o histórico de execuções.
        </div>
        @endif
    </div>
</div>
