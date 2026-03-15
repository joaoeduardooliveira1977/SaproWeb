<div @if($verificacao?->emAndamento()) wire:poll.2000ms @endif>

    {{-- Erro de inicialização --}}
    @if($erroMensagem)
    <div style="background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;">
        <span>⚠️ {{ $erroMensagem }}</span>
        <button wire:click="$set('erroMensagem','')" style="background:none;border:none;cursor:pointer;font-size:16px;color:#991b1b;">✕</button>
    </div>
    @endif

    {{-- Cabeçalho --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">🏛️ Consulta Judicial</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Verifique novos andamentos via DATAJUD/CNJ — tribunal detectado automaticamente pelo número do processo</p>
    </div>


    {{-- Filtros --}}
    <div style="background:white; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <strong style="font-size:14px; color:#1a3a5c;">🔍 Filtros</strong>
            <button wire:click="limparFiltros" style="font-size:12px; color:#64748b; background:none; border:none; cursor:pointer; text-decoration:underline;">
                Limpar filtros
            </button>
        </div>

        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:12px;">
            {{-- Cliente --}}
            <div>
                <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">Cliente</label>
                <input wire:model.live.debounce.300ms="filtroCliente" type="text"
                    placeholder="Buscar cliente..."
                    style="width:100%; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; box-sizing:border-box;">
            </div>

            {{-- Fase --}}
            <div>
                <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">Fase</label>
                <select wire:model.live="filtroFase"
                    style="width:100%; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; background:white;">
                    <option value="">Todas as fases</option>
                    @foreach($fases as $fase)
                    <option value="{{ $fase->descricao }}">{{ $fase->descricao }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Advogado --}}
            <div>
                <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">Advogado</label>
                <select wire:model.live="filtroAdvogado"
                    style="width:100%; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; background:white;">
                    <option value="">Todos os advogados</option>
                    @foreach($advogados as $adv)
                    <option value="{{ $adv->nome }}">{{ $adv->nome }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div>
                <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">Status</label>
                <select wire:model.live="filtroStatus"
                    style="width:100%; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; background:white;">
                    <option value="Ativo">Apenas ativos</option>
                    <option value="">Todos</option>
                    <option value="Arquivado">Arquivados</option>
                </select>
            </div>

            {{-- Última consulta --}}
            <div>
                <label style="font-size:12px; color:#64748b; font-weight:600; display:block; margin-bottom:4px;">Última Consulta</label>
                <select wire:model.live="filtroConsulta"
                    style="width:100%; padding:8px 12px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; outline:none; background:white;">
                    <option value="">Todos</option>
                    <option value="nunca">Nunca consultados</option>
                    <option value="semana">Não consultados esta semana</option>
                    <option value="mes">Não consultados este mês</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Botão verificar atualização --}}
    <div style="display:flex; justify-content:flex-end; align-items:center; gap:12px; margin-top:16px; padding-top:16px; border-top:1px solid #f1f5f9;">
        <span style="font-size:13px; color:#64748b;">{{ $totalFiltrado }} processo(s) selecionado(s)</span>
        @php $bloqueado = $consultando || $verificacao?->emAndamento() || $totalFiltrado === 0; @endphp
        <button wire:click="iniciarVerificacao"
            wire:loading.attr="disabled" wire:target="iniciarVerificacao"
            @if($bloqueado) disabled @endif
            style="padding:12px 24px; background:{{ $bloqueado ? '#94a3b8' : '#16a34a' }}; color:white; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:{{ $bloqueado ? 'not-allowed' : 'pointer' }};">
            <span wire:loading.remove wire:target="iniciarVerificacao">
                {{ $verificacao?->emAndamento() ? '⏳ Verificando...' : '🔄 Verificar Atualizações' }}
            </span>
            <span wire:loading wire:target="iniciarVerificacao">⏳ Iniciando...</span>
        </button>
    </div>



    {{-- Barra de progresso + feed ao vivo --}}
    @if($verificacao?->emAndamento())
    <div class="card" style="margin-bottom:20px">

        {{-- Cabeçalho progresso --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <span style="font-size:14px;font-weight:700;color:var(--primary)">
                🔄 Consultando processos no DATAJUD/CNJ...
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
            <span style="color:var(--success);font-weight:700">✅ {{ $verificacao->novos_total }} andamento(s) novo(s) encontrado(s)</span>
            @endif
        </div>

        {{-- Feed ao vivo --}}
        @if(!empty($verificacao->log_linhas))
        @php
            $linhas = array_reverse(array_slice($verificacao->log_linhas, -15)); // últimas 15, mais recentes no topo
            $icones = ['consultando'=>'🔍','ok'=>'✅','sem_novos'=>'➖','erro'=>'❌','ignorado'=>'⚪'];
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
                    <span style="flex-shrink:0">{{ $icones[$linha['tipo']] ?? '•' }}</span>
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
            <strong style="color:#16a34a;font-size:15px;">
                ✅ {{ count($verificacao->novos_andamentos) }} processo(s) com andamentos novos!
            </strong>
            @if(($verificacao->prazos_criados ?? 0) > 0)
            <a href="{{ route('prazos') }}"
               style="display:inline-flex;align-items:center;gap:6px;background:#1a3a5c;color:#fff;padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;">
                ⏳ {{ $verificacao->prazos_criados }} prazo(s) criado(s) automaticamente →
            </a>
            @endif
        </div>

        @foreach($verificacao->novos_andamentos as $item)
        <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:16px; overflow:hidden; border-left:4px solid #16a34a;">
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
        <div style="background:white; border-radius:12px; padding:60px 40px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:48px; margin-bottom:12px;">✅</div>
            <p style="font-size:16px; font-weight:600; color:#1a3a5c;">Nenhum andamento novo encontrado!</p>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Todos os processos selecionados estão atualizados.</p>
        </div>
        @endif
    </div>
    @endif

    {{-- Estado inicial --}}
    @if(!$verificacao || $verificacao->status === 'erro')
    <div style="background:white; border-radius:12px; padding:60px 40px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <div style="font-size:56px; margin-bottom:16px;">🏛️</div>
        <p style="font-size:16px; font-weight:600; color:#1a3a5c;">Clique em "Verificar Atualizações"</p>
        <p style="font-size:13px; color:#64748b; margin-top:6px;">
            Use os filtros acima para selecionar quais processos consultar<br>
            e o sistema mostrará apenas os que tiveram novos andamentos.
        </p>
    </div>
    @endif




</div>
