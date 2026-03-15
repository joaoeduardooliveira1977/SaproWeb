<div @if($verificacao?->emAndamento()) wire:poll.2000ms @endif>


    {{-- Cabeçalho --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;display:flex;align-items:center;gap:8px;"><svg width="20" height="20" fill="none" stroke="var(--primary)" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg> Consulta Judicial</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Verifique novos andamentos via DATAJUD/CNJ — tribunal detectado automaticamente pelo número do processo</p>
    </div>


    {{-- Filtros --}}
    <div class="card" style="margin-bottom:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <strong style="font-size:14px;color:#1a3a5c;display:inline-flex;align-items:center;gap:6px;"><svg width="16" height="16" fill="none" stroke="#1a3a5c" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Filtros</strong>
            <button wire:click="limparFiltros" class="btn btn-sm btn-secondary-outline">
                Limpar filtros
            </button>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label class="lbl">Cliente</label>
                <input wire:model.live.debounce.300ms="filtroCliente" type="text" placeholder="Buscar cliente...">
            </div>
            <div class="form-field">
                <label class="lbl">Fase</label>
                <select wire:model.live="filtroFase">
                    <option value="">Todas as fases</option>
                    @foreach($fases as $fase)
                    <option value="{{ $fase->descricao }}">{{ $fase->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label class="lbl">Advogado</label>
                <select wire:model.live="filtroAdvogado">
                    <option value="">Todos os advogados</option>
                    @foreach($advogados as $adv)
                    <option value="{{ $adv->nome }}">{{ $adv->nome }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-field">
                <label class="lbl">Status</label>
                <select wire:model.live="filtroStatus">
                    <option value="Ativo">Apenas ativos</option>
                    <option value="">Todos</option>
                    <option value="Arquivado">Arquivados</option>
                </select>
            </div>
            <div class="form-field">
                <label class="lbl">Última Consulta</label>
                <select wire:model.live="filtroConsulta">
                    <option value="">Todos</option>
                    <option value="nunca">Nunca consultados</option>
                    <option value="semana">Não consultados esta semana</option>
                    <option value="mes">Não consultados este mês</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Botão verificar atualização --}}
    <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;flex-wrap:wrap;">
        <span style="font-size:13px; color:#64748b;">{{ $totalFiltrado }} processo(s) selecionado(s)</span>
        @php $bloqueado = $consultando || $verificacao?->emAndamento() || $totalFiltrado === 0; @endphp
        <button wire:click="iniciarVerificacao"
            wire:loading.attr="disabled" wire:target="iniciarVerificacao"
            @if($bloqueado) disabled @endif
            style="padding:12px 24px; background:{{ $bloqueado ? '#94a3b8' : '#16a34a' }}; color:white; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:{{ $bloqueado ? 'not-allowed' : 'pointer' }};display:inline-flex;align-items:center;gap:8px;">
            <span wire:loading.remove wire:target="iniciarVerificacao" style="display:inline-flex;align-items:center;gap:8px;">
                @if($verificacao?->emAndamento())
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Verificando...
                @else
                    <svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Verificar Atualizações
                @endif
            </span>
            <span wire:loading wire:target="iniciarVerificacao" style="display:inline-flex;align-items:center;gap:8px;"><svg width="16" height="16" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Iniciando...</span>
        </button>
    </div>



    {{-- Barra de progresso + feed ao vivo --}}
    @if($verificacao?->emAndamento())
    <div class="card" style="margin-bottom:20px">

        {{-- Cabeçalho progresso --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <span style="font-size:14px;font-weight:700;color:var(--primary);display:inline-flex;align-items:center;gap:8px;">
                <svg width="16" height="16" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Consultando processos no DATAJUD/CNJ...
            </span>
            <span style="font-size:16px;font-weight:800;color:var(--primary)">{{ $verificacao->porcentagem() }}%</span>
        </div>

        {{-- Barra --}}
        <div style="background:var(--border);border-radius:99px;height:10px;overflow:hidden;margin-bottom:10px">
            <div style="background:linear-gradient(90deg,var(--primary),#16a34a);height:10px;border-radius:99px;transition:width .5s ease;width:{{ $verificacao->porcentagem() }}%"></div>
        </div>

        {{-- Contadores --}}
        <div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted);margin-bottom:12px">
            <span>{{ $verificacao->processado }} / {{ $verificacao->total }} processos</span>
            @if($verificacao->novos_total > 0)
            <span style="color:var(--success);font-weight:700;display:inline-flex;align-items:center;gap:5px;"><svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> {{ $verificacao->novos_total }} andamento(s) novo(s) encontrado(s)</span>
            @endif
        </div>

        {{-- Feed ao vivo --}}
        @if(!empty($verificacao->log_linhas))
        @php
            $linhas = array_reverse(array_slice($verificacao->log_linhas, -15)); // últimas 15, mais recentes no topo
            $icones = [
                'consultando' => '<svg width="12" height="12" fill="none" stroke="var(--primary)" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
                'ok'          => '<svg width="12" height="12" fill="none" stroke="var(--success)" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>',
                'sem_novos'   => '<svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/></svg>',
                'erro'        => '<svg width="12" height="12" fill="none" stroke="var(--danger)" stroke-width="2.5" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
                'ignorado'    => '<svg width="12" height="12" fill="none" stroke="var(--muted)" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/></svg>',
            ];
            $cores  = ['consultando'=>'var(--primary)','ok'=>'var(--success)','sem_novos'=>'var(--muted)','erro'=>'var(--danger)','ignorado'=>'var(--muted)'];
        @endphp
        <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden">
            <div style="padding:8px 12px;background:var(--bg);border-bottom:1px solid var(--border);font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">
                Log em tempo real
            </div>
            <div style="max-height:220px;overflow-y:auto;font-family:monospace;font-size:12px">
                @foreach($linhas as $linha)
                <div style="display:flex;align-items:baseline;gap:8px;padding:5px 12px;border-bottom:1px solid #f8fafc;{{ $loop->first ? 'background:#f0fdf4' : '' }}">
                    <span style="color:var(--muted);flex-shrink:0;font-size:10px">{{ $linha['ts'] }}</span>
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;">{!! $icones[$linha['tipo']] ?? '•' !!}</span>
                    <span style="color:var(--text);font-weight:600;flex-shrink:0;min-width:220px">{{ $linha['numero'] }}</span>
                    @if($linha['tribunal'])
                    <span style="background:#e0f2fe;color:#0369a1;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;flex-shrink:0">{{ $linha['tribunal'] }}</span>
                    @endif
                    <span style="color:{{ $cores[$linha['tipo']] ?? 'var(--muted)' }}">{{ $linha['msg'] }}</span>
                    @if(($linha['novos'] ?? 0) > 0)
                    <span style="background:var(--success);color:#fff;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;margin-left:auto;flex-shrink:0">+{{ $linha['novos'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    {{-- Resultado concluído --}}
    @if($verificacao?->status === 'concluido')
    <div style="margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; font-size:12px; color:#94a3b8; margin-bottom:16px;">
            <span>Verificação concluída em {{ $verificacao->concluido_em?->format('d/m/Y H:i') }}</span>
            <span>{{ $verificacao->total }} processos em {{ $verificacao->iniciado_em->diffForHumans($verificacao->concluido_em, true) }}</span>
        </div>

        @if(count($verificacao->novos_andamentos ?? []) > 0)
        <div style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
            <strong style="color:#16a34a;font-size:15px;display:inline-flex;align-items:center;gap:6px;">
                <svg width="18" height="18" fill="none" stroke="#16a34a" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> {{ count($verificacao->novos_andamentos) }} processo(s) com andamentos novos!
            </strong>
            @if(($verificacao->prazos_criados ?? 0) > 0)
            <a href="{{ route('prazos') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:#1a3a5c;color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;">
                <svg width="14" height="14" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> {{ $verificacao->prazos_criados }} prazo(s) criado(s) automaticamente →
            </a>
            @endif
        </div>

        @foreach($verificacao->novos_andamentos as $item)
        <div class="card" style="margin-bottom:16px;overflow:hidden;border-left:4px solid #16a34a;padding:0;">
            <div style="padding:14px 20px; background:#f0fdf4; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:14px; font-weight:700; color:#1a3a5c;">{{ $item['numero'] }}</span>
                    <span style="font-size:13px; color:#64748b; margin-left:12px;">{{ $item['cliente'] }}</span>
                    @if(!empty($item['tribunal']))
                    <span style="background:#e0f2fe; color:#0369a1; padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; margin-left:8px;">
                        {{ $item['tribunal'] }}
                    </span>
                    @endif
                </div>
                <span style="background:#16a34a; color:white; padding:3px 10px; border-radius:99px; font-size:12px; font-weight:600;">
                    {{ count($item['andamentos']) }} novo(s)
                </span>
            </div>
            <div style="padding:12px 20px;">
                @foreach($item['andamentos'] as $a)
                <div style="display:flex; gap:16px; padding:8px 0; border-bottom:1px solid #f1f5f9; font-size:13px;">
                    <span style="color:#16a34a; font-weight:600; min-width:90px;">
                        {{ \Carbon\Carbon::parse($a['data'])->format('d/m/Y') }}
                    </span>
                    <span>{{ $a['descricao'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        @else
        <div class="card" style="text-align:center;padding:60px 24px;">
            <div style="margin-bottom:12px;display:flex;justify-content:center;"><svg width="48" height="48" fill="none" stroke="var(--success)" stroke-width="1.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
            <p style="font-size:16px;font-weight:600;color:#1a3a5c;">Nenhum andamento novo encontrado!</p>
            <p style="font-size:13px;color:#64748b;margin-top:4px;">Todos os processos selecionados estão atualizados.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Estado inicial --}}
    @if(!$verificacao || $verificacao->status === 'erro')
    <div class="card" style="text-align:center;padding:60px 24px;">
        <div style="margin-bottom:16px;display:flex;justify-content:center;"><svg width="48" height="48" fill="none" stroke="var(--muted)" stroke-width="1.5" viewBox="0 0 24 24"><line x1="3" y1="22" x2="21" y2="22"/><line x1="6" y1="18" x2="6" y2="11"/><line x1="10" y1="18" x2="10" y2="11"/><line x1="14" y1="18" x2="14" y2="11"/><line x1="18" y1="18" x2="18" y2="11"/><polygon points="12 2 20 7 4 7"/></svg></div>
        <p style="font-size:16px; font-weight:600; color:#1a3a5c;">Clique em "Verificar Atualizações"</p>
        <p style="font-size:13px; color:#64748b; margin-top:6px;">
            Use os filtros acima para selecionar quais processos consultar<br>
            e o sistema mostrará apenas os que tiveram novos andamentos.
        </p>
    </div>
    @endif




</div>
