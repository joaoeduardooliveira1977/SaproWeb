<div @if($verificacao?->emAndamento()) wire:poll.2000ms @endif>

    {{-- Cabeçalho --}}
    <div style="margin-bottom:24px;">
        <h2 style="font-size:20px; font-weight:700; color:#1a3a5c;">🏛️ Atualizações do TJSP</h2>
            <p style="font-size:13px; color:#64748b; margin-top:4px;">Verifique se houve novos andamentos nos processos</p>
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
        <button wire:click="iniciarVerificacao"
            @if($verificacao?->emAndamento() || $totalFiltrado === 0) disabled @endif
        style="padding:12px 24px; background:{{ $verificacao?->emAndamento() || $totalFiltrado === 0 ? '#94a3b8' : '#16a34a' }}; color:white; border:none; border-radius:8px; font-size:14px; font-weight:700; cursor:{{ $verificacao?->emAndamento() || $totalFiltrado === 0 ? 'not-allowed' : 'pointer' }};">
        {{ $verificacao?->emAndamento() ? '⏳ Verificando...' : '🔄 Verificar Atualizações' }}
    </button>
    </div>



    {{-- Barra de progresso --}}
    @if($verificacao?->emAndamento())
    <div style="background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:24px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <span style="font-size:14px; font-weight:600; color:#1a3a5c;">Consultando processos no DATAJUD/CNJ...</span>
            <span style="font-size:14px; font-weight:700; color:#2563a8;">{{ $verificacao->porcentagem() }}%</span>
        </div>
        <div style="background:#e2e8f0; border-radius:99px; height:12px; overflow:hidden; margin-bottom:10px;">
            <div style="background:linear-gradient(90deg,#2563a8,#16a34a); height:12px; border-radius:99px; transition:width 0.5s ease;
                width:{{ $verificacao->porcentagem() }}%;"></div>
        </div>
        <div style="display:flex; justify-content:space-between; font-size:12px; color:#64748b;">
            <span>{{ $verificacao->processado }} / {{ $verificacao->total }} processos</span>
            @if($verificacao->processo_atual)
            <span>📄 {{ $verificacao->processo_atual }}</span>
            @endif
        </div>
        @if($verificacao->novos_total > 0)
        <div style="margin-top:12px; padding:8px 12px; background:#dcfce7; border-radius:6px; font-size:13px; color:#16a34a; font-weight:600;">
            ✅ {{ $verificacao->novos_total }} novo(s) andamento(s) encontrado(s) até agora...
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
        <div style="background:#dcfce7; border:1px solid #86efac; border-radius:12px; padding:16px 20px; margin-bottom:20px;">
            <strong style="color:#16a34a; font-size:15px;">
                ✅ {{ count($verificacao->novos_andamentos) }} processo(s) com andamentos novos!
            </strong>
        </div>

        @foreach($verificacao->novos_andamentos as $item)
        <div style="background:white; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.08); margin-bottom:16px; overflow:hidden; border-left:4px solid #16a34a;">
            <div style="padding:14px 20px; background:#f0fdf4; display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:14px; font-weight:700; color:#1a3a5c;">{{ $item['numero'] }}</span>
                    <span style="font-size:13px; color:#64748b; margin-left:12px;">{{ $item['cliente'] }}</span>
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
