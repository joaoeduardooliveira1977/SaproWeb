<div>

{{-- ── Cabeçalho ──────────────────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
        <h1 style="font-size:22px;font-weight:800;color:var(--primary);margin:0;">Workflow de Automação</h1>
        <p style="font-size:13px;color:var(--muted);margin-top:4px;">Defina regras que disparam ações automáticas em resposta a eventos do sistema.</p>
    </div>
    <button wire:click="novaRegra"
        style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:10px;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s;"
        onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nova Regra
    </button>
</div>

{{-- ── KPIs ──────────────────────────────────────────────────── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;" class="wf-kpis">
    @php
    $kpis = [
        ['label'=>'Total de Regras',    'val'=>$totalRegras,   'bg'=>'linear-gradient(135deg,#1d4ed8,#2563a8)',   'ico'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
        ['label'=>'Regras Ativas',      'val'=>$totalAtivas,   'bg'=>'linear-gradient(135deg,#059669,#16a34a)',   'ico'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>'],
        ['label'=>'Total de Execuções', 'val'=>$totalExecucoes,'bg'=>'linear-gradient(135deg,#7c3aed,#6d28d9)',   'ico'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>'],
        ['label'=>'Erros Hoje',         'val'=>$errosHoje,     'bg'=>'linear-gradient(135deg,#dc2626,#b91c1c)',   'ico'=>'<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>'],
    ];
    @endphp
    @foreach($kpis as $k)
    <div style="background:{{ $k['bg'] }};border-radius:12px;padding:18px;color:#fff;box-shadow:0 4px 15px rgba(0,0,0,.12);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
            {!! $k['ico'] !!}
        </div>
        <div style="font-size:26px;font-weight:800;letter-spacing:-1px;">{{ $k['val'] }}</div>
        <div style="font-size:12px;color:rgba(255,255,255,.8);margin-top:3px;">{{ $k['label'] }}</div>
    </div>
    @endforeach
</div>

{{-- ── Abas ──────────────────────────────────────────────────── --}}
<div style="display:flex;gap:4px;border-bottom:2px solid var(--border);margin-bottom:20px;">
    @foreach(['regras'=>'Regras','historico'=>'Histórico de Execuções'] as $key=>$label)
    <button wire:click="$set('aba','{{ $key }}')"
        style="padding:9px 18px;border:none;background:none;font-size:13px;font-weight:600;cursor:pointer;border-bottom:2px solid {{ $aba===$key ? 'var(--primary)' : 'transparent' }};color:{{ $aba===$key ? 'var(--primary)' : 'var(--muted)' }};margin-bottom:-2px;transition:color .15s;">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA REGRAS                                                   --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'regras')

{{-- Filtros --}}
<div style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
    <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por nome…"
        style="flex:1;min-width:180px;padding:9px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:var(--white);outline:none;">
    <select wire:model.live="filtroGatilho"
        style="padding:9px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:var(--white);cursor:pointer;">
        <option value="">Todos os gatilhos</option>
        @foreach($gatilhos as $val => $label)
        <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
    </select>
</div>

@if($regras->isEmpty())
<div style="text-align:center;padding:60px 20px;color:var(--muted);">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="opacity:.3;margin-bottom:16px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
    <div style="font-size:15px;font-weight:600;">Nenhuma regra encontrada</div>
    <div style="font-size:13px;margin-top:6px;">Clique em "Nova Regra" para criar a primeira automação.</div>
</div>
@else

<div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;" class="wf-grid">
    @foreach($regras as $regra)
    <div style="background:var(--white);border:1.5px solid {{ $regra->ativo ? 'var(--border)' : '#e5e7eb' }};border-radius:14px;padding:20px;transition:box-shadow .15s;opacity:{{ $regra->ativo ? '1' : '.65' }};"
        onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)'"
        onmouseout="this.style.boxShadow=''">

        {{-- Cabeçalho do card --}}
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:12px;">
            <div style="flex:1;min-width:0;">
                <div style="font-size:15px;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $regra->nome }}</div>
                @if($regra->descricao)
                <div style="font-size:12px;color:var(--muted);margin-top:3px;line-height:1.4;">{{ Str::limit($regra->descricao, 80) }}</div>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                {{-- Toggle ativo --}}
                <button wire:click="toggleAtivo({{ $regra->id }})" title="{{ $regra->ativo ? 'Desativar' : 'Ativar' }}"
                    style="width:36px;height:20px;border-radius:10px;border:none;cursor:pointer;position:relative;background:{{ $regra->ativo ? '#059669' : '#d1d5db' }};transition:background .2s;flex-shrink:0;">
                    <span style="position:absolute;top:3px;{{ $regra->ativo ? 'right:3px' : 'left:3px' }};width:14px;height:14px;border-radius:50%;background:#fff;transition:all .2s;"></span>
                </button>
                {{-- Editar --}}
                <button wire:click="editarRegra({{ $regra->id }})" title="Editar"
                    style="padding:5px;border:1.5px solid var(--border);border-radius:7px;background:var(--white);cursor:pointer;color:var(--muted);display:flex;transition:color .15s,border-color .15s;"
                    onmouseover="this.style.color='var(--primary)';this.style.borderColor='var(--primary)'"
                    onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </button>
                {{-- Excluir --}}
                @if($confirmandoExclusao === $regra->id)
                    <button wire:click="excluirRegra" title="Confirmar exclusão"
                        style="padding:5px 8px;border:1.5px solid #dc2626;border-radius:7px;background:#fef2f2;cursor:pointer;color:#dc2626;font-size:11px;font-weight:700;">
                        Confirmar
                    </button>
                    <button wire:click="cancelarExclusao" style="padding:5px;border:1.5px solid var(--border);border-radius:7px;background:var(--white);cursor:pointer;color:var(--muted);">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                @else
                    <button wire:click="confirmarExclusao({{ $regra->id }})" title="Excluir"
                        style="padding:5px;border:1.5px solid var(--border);border-radius:7px;background:var(--white);cursor:pointer;color:var(--muted);display:flex;transition:color .15s,border-color .15s;"
                        onmouseover="this.style.color='#dc2626';this.style.borderColor='#dc2626'"
                        onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                @endif
            </div>
        </div>

        {{-- Gatilho --}}
        <div style="margin-bottom:10px;">
            <span style="display:inline-flex;align-items:center;gap:5px;padding:4px 10px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:20px;font-size:11px;font-weight:600;color:#0369a1;">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                {{ $gatilhos[$regra->gatilho] ?? $regra->gatilho }}
            </span>
            @if($regra->gatilho === 'processo.sem_andamento_dias' && isset($regra->gatilho_config['dias']))
            <span style="font-size:11px;color:var(--muted);margin-left:6px;">→ {{ $regra->gatilho_config['dias'] }} dias</span>
            @endif
        </div>

        {{-- Ações --}}
        @if($regra->acoes->count())
        <div style="display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px;">
            @foreach($regra->acoes as $acao)
            @php
            $acaoCores = [
                'criar_prazo'        => ['#1d4ed8','#eff6ff'],
                'criar_notificacao'  => ['#7c3aed','#f5f3ff'],
                'criar_agenda'       => ['#059669','#f0fdf4'],
                'enviar_whatsapp'    => ['#16a34a','#f0fdf4'],
                'atualizar_score'    => ['#d97706','#fffbeb'],
                'chamar_ia'          => ['#0891b2','#f0f9ff'],
            ];
            [$cor, $bg] = $acaoCores[$acao->tipo] ?? ['#6b7280','#f9fafb'];
            @endphp
            <span style="padding:3px 9px;background:{{ $bg }};color:{{ $cor }};border:1px solid {{ $cor }}33;border-radius:5px;font-size:11px;font-weight:600;">
                {{ $acoesTipos[$acao->tipo] ?? $acao->tipo }}
            </span>
            @endforeach
        </div>
        @endif

        {{-- Rodapé --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:10px;border-top:1px solid var(--border);font-size:11px;color:var(--muted);">
            <span>{{ $regra->condicoes ? count($regra->condicoes).' condição(ões)' : 'Sem condições' }}</span>
            <span style="display:flex;align-items:center;gap:4px;">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                {{ number_format($regra->execucoes_total) }} execuções
            </span>
        </div>
    </div>
    @endforeach
</div>

<div style="margin-top:20px;">
    {{ $regras->links() }}
</div>
@endif
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- ABA HISTÓRICO                                                --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($aba === 'historico')
<div style="background:var(--white);border:1.5px solid var(--border);border-radius:12px;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:var(--bg-light,#f8fafc);border-bottom:1.5px solid var(--border);">
                <th style="padding:11px 16px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Regra</th>
                <th style="padding:11px 16px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Status</th>
                <th style="padding:11px 16px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Processo</th>
                <th style="padding:11px 16px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Data</th>
                <th style="padding:11px 16px;text-align:left;font-weight:700;color:var(--muted);font-size:11px;text-transform:uppercase;letter-spacing:.5px;">Erro</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historico as $ex)
            @php
            $statusStyle = match($ex->status) {
                'executado' => ['#059669','#f0fdf4'],
                'erro'      => ['#dc2626','#fef2f2'],
                default     => ['#6b7280','#f9fafb'],
            };
            @endphp
            <tr style="border-bottom:1px solid var(--border);">
                <td style="padding:11px 16px;font-weight:600;color:var(--text);">{{ $ex->regra?->nome ?? '—' }}</td>
                <td style="padding:11px 16px;">
                    <span style="padding:3px 9px;background:{{ $statusStyle[1] }};color:{{ $statusStyle[0] }};border-radius:5px;font-size:11px;font-weight:600;">
                        {{ ucfirst($ex->status) }}
                    </span>
                </td>
                <td style="padding:11px 16px;color:var(--muted);">
                    @if($ex->processo_id)
                    <a href="{{ route('processos.show', $ex->processo_id) }}" style="color:var(--primary);text-decoration:none;" target="_blank">#{{ $ex->processo_id }}</a>
                    @else —
                    @endif
                </td>
                <td style="padding:11px 16px;color:var(--muted);white-space:nowrap;">{{ $ex->created_at?->format('d/m/Y H:i') }}</td>
                <td style="padding:11px 16px;color:#dc2626;font-size:11px;max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $ex->erro_mensagem }}">
                    {{ $ex->erro_mensagem ?? '—' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:40px;text-align:center;color:var(--muted);">Nenhuma execução registrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:16px;">{{ $historico->links() }}</div>
@endif

{{-- ════════════════════════════════════════════════════════════ --}}
{{-- MODAL CRIAR / EDITAR                                         --}}
{{-- ════════════════════════════════════════════════════════════ --}}
@if($modal)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;display:flex;align-items:flex-start;justify-content:center;padding:24px 16px;overflow-y:auto;">
    <div style="background:var(--white);border-radius:16px;width:100%;max-width:760px;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:wf-slide-in .18s ease;">

        {{-- Cabeçalho do modal --}}
        <div style="display:flex;justify-content:space-between;align-items:center;padding:20px 24px;border-bottom:1.5px solid var(--border);">
            <div style="font-size:17px;font-weight:800;color:var(--text);">
                {{ $editandoId ? 'Editar Regra' : 'Nova Regra de Workflow' }}
            </div>
            <button wire:click="fecharModal"
                style="width:30px;height:30px;border-radius:8px;border:1.5px solid var(--border);background:var(--white);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <div style="padding:24px;">

            {{-- Erros de validação --}}
            @if($errors->any())
            <div style="background:#fef2f2;border:1.5px solid #fecaca;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:#dc2626;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $erro)
                    <li>{{ $erro }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Nome e status --}}
            <div style="display:grid;grid-template-columns:1fr auto;gap:14px;align-items:end;margin-bottom:16px;">
                <div>
                    <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px;">Nome da Regra *</label>
                    <input wire:model="nome" type="text" placeholder="Ex: Notificar ao receber intimação"
                        style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('nome') ? '#fca5a5' : 'var(--border)' }};border-radius:8px;font-size:14px;color:var(--text);background:var(--white);outline:none;box-sizing:border-box;">
                </div>
                <div style="display:flex;align-items:center;gap:8px;padding-bottom:2px;">
                    <label style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;white-space:nowrap;">Ativa</label>
                    <input wire:model="ativo" type="checkbox" style="width:16px;height:16px;cursor:pointer;accent-color:var(--primary);">
                </div>
            </div>

            {{-- Descrição --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px;">Descrição (opcional)</label>
                <input wire:model="descricao" type="text" placeholder="Descreva quando esta regra se aplica…"
                    style="width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:13px;color:var(--text);background:var(--white);outline:none;box-sizing:border-box;">
            </div>

            {{-- Gatilho --}}
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px;">Gatilho (evento disparador) *</label>
                <select wire:model.live="gatilho"
                    style="width:100%;padding:10px 14px;border:1.5px solid {{ $errors->has('gatilho') ? '#fca5a5' : 'var(--border)' }};border-radius:8px;font-size:13px;color:var(--text);background:var(--white);cursor:pointer;outline:none;">
                    <option value="">— Selecione o gatilho —</option>
                    @foreach($gatilhos as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Config do gatilho (só para sem_andamento_dias) --}}
            @if($gatilho === 'processo.sem_andamento_dias')
            <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:14px 16px;margin-bottom:16px;">
                <label style="display:block;font-size:12px;font-weight:700;color:#92400e;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;">
                    Configuração do Gatilho
                </label>
                <div style="display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text);">
                    <span>Disparar após</span>
                    <input type="number" min="1" max="365"
                        wire:model="gatilhoConfigJson"
                        x-data
                        x-init="
                            let parsed = {};
                            try { parsed = JSON.parse($wire.gatilhoConfigJson); } catch(e) {}
                            $el.value = parsed.dias ?? 30;
                            $el.addEventListener('input', () => {
                                $wire.gatilhoConfigJson = JSON.stringify({ dias: parseInt($el.value) || 30 });
                            });
                        "
                        style="width:70px;padding:8px;border:1.5px solid #fcd34d;border-radius:8px;font-size:13px;text-align:center;"
                        placeholder="30">
                    <span>dias sem andamento</span>
                </div>
            </div>
            @endif

            {{-- Separador seção condições --}}
            <div style="display:flex;align-items:center;gap:10px;margin:20px 0 12px;">
                <div style="height:1.5px;flex:1;background:var(--border);"></div>
                <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;white-space:nowrap;">Condições (opcionais — AND lógico)</span>
                <div style="height:1.5px;flex:1;background:var(--border);"></div>
            </div>

            {{-- Lista de condições --}}
            @foreach($condicoes as $ci => $cond)
            <div style="display:grid;grid-template-columns:1fr 140px 1fr auto;gap:8px;margin-bottom:8px;align-items:center;">
                <select wire:model="condicoes.{{ $ci }}.campo"
                    style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;color:var(--text);background:var(--white);cursor:pointer;">
                    @foreach($campos as $cv => $cl)
                    <option value="{{ $cv }}">{{ $cl }}</option>
                    @endforeach
                </select>
                <select wire:model="condicoes.{{ $ci }}.op"
                    style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;color:var(--text);background:var(--white);cursor:pointer;">
                    @foreach($operadores as $ov => $ol)
                    <option value="{{ $ov }}">{{ $ol }}</option>
                    @endforeach
                </select>
                <input wire:model="condicoes.{{ $ci }}.valor" type="text" placeholder="valor…"
                    style="padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;font-size:12px;color:var(--text);background:var(--white);">
                <button wire:click="removerCondicao({{ $ci }})"
                    style="width:30px;height:30px;border:1.5px solid #fecaca;border-radius:8px;background:#fef2f2;cursor:pointer;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            @endforeach

            <button wire:click="adicionarCondicao"
                style="display:inline-flex;align-items:center;gap:6px;padding:7px 12px;border:1.5px dashed var(--border);border-radius:8px;background:none;font-size:12px;color:var(--muted);cursor:pointer;transition:color .15s,border-color .15s;"
                onmouseover="this.style.color='var(--primary)';this.style.borderColor='var(--primary)'"
                onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Adicionar Condição
            </button>

            {{-- Separador seção ações --}}
            <div style="display:flex;align-items:center;gap:10px;margin:20px 0 14px;">
                <div style="height:1.5px;flex:1;background:var(--border);"></div>
                <span style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.8px;white-space:nowrap;">Ações (executadas em ordem)</span>
                <div style="height:1.5px;flex:1;background:var(--border);"></div>
            </div>

            @if($errors->has('acoes'))
            <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:8px 12px;margin-bottom:12px;font-size:12px;color:#dc2626;">
                {{ $errors->first('acoes') }}
            </div>
            @endif

            {{-- Lista de ações --}}
            @foreach($acoes as $ai => $acao)
            <div style="background:#f8fafc;border:1.5px solid var(--border);border-radius:10px;padding:14px;margin-bottom:10px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:10px;">
                    <span style="width:22px;height:22px;border-radius:6px;background:var(--primary);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0;">{{ $ai + 1 }}</span>

                    <select wire:model.live="acoes.{{ $ai }}.tipo"
                        style="flex:1;padding:8px 10px;border:1.5px solid {{ $errors->has('acoes.'.$ai.'.tipo') ? '#fca5a5' : 'var(--border)' }};border-radius:8px;font-size:13px;color:var(--text);background:var(--white);cursor:pointer;">
                        <option value="">— Selecione a ação —</option>
                        @foreach($acoesTipos as $av => $al)
                        <option value="{{ $av }}">{{ $al }}</option>
                        @endforeach
                    </select>

                    <button wire:click="removerAcao({{ $ai }})"
                        style="width:30px;height:30px;border:1.5px solid #fecaca;border-radius:8px;background:#fef2f2;cursor:pointer;color:#dc2626;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                {{-- Formulários visuais por tipo de ação --}}
                @if($acao['tipo'] === 'enviar_whatsapp')
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:#15803d;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Configuração do WhatsApp</div>
                    <div style="display:grid;grid-template-columns:1fr;gap:10px;">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Enviar para</label>
                            <select wire:model="acoes.{{ $ai }}.wpp_destinatario"
                                style="width:100%;padding:8px 10px;border:1.5px solid #d1fae5;border-radius:8px;font-size:13px;background:#fff;">
                                <option value="advogado_processo">Advogado responsável pelo processo</option>
                                <option value="cliente">Cliente do processo</option>
                                <option value="todos">Todos os advogados do escritório</option>
                            </select>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">
                                Mensagem
                                <span style="font-weight:400;color:#6b7280;margin-left:6px;">Use: <code style="background:#e5e7eb;padding:1px 5px;border-radius:3px;font-size:11px;">{numero}</code> <code style="background:#e5e7eb;padding:1px 5px;border-radius:3px;font-size:11px;">{cliente}</code> <code style="background:#e5e7eb;padding:1px 5px;border-radius:3px;font-size:11px;">{andamento}</code></span>
                            </label>
                            <textarea wire:model="acoes.{{ $ai }}.wpp_mensagem" rows="3"
                                placeholder="Ex: Novo andamento no processo {numero} do cliente {cliente}: {andamento}"
                                style="width:100%;padding:8px 10px;border:1.5px solid #d1fae5;border-radius:8px;font-size:13px;background:#fff;resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
                        </div>
                    </div>
                </div>
                @endif

                @if($acao['tipo'] === 'criar_prazo')
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:#1d4ed8;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Configuração do Prazo</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="grid-column:1/-1;">
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Título do prazo <span style="font-weight:400;color:#6b7280;margin-left:6px;">Use: <code style="background:#e5e7eb;padding:1px 5px;border-radius:3px;font-size:11px;">{andamento}</code> <code style="background:#e5e7eb;padding:1px 5px;border-radius:3px;font-size:11px;">{numero}</code></span></label>
                            <input wire:model="acoes.{{ $ai }}.prazo_titulo" type="text"
                                placeholder="Ex: Prazo recursal — {andamento}"
                                style="width:100%;padding:8px 10px;border:1.5px solid #bfdbfe;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Quantidade de dias</label>
                            <input wire:model="acoes.{{ $ai }}.prazo_dias" type="number" min="1" max="365"
                                style="width:100%;padding:8px 10px;border:1.5px solid #bfdbfe;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Tipo de contagem</label>
                            <select wire:model="acoes.{{ $ai }}.prazo_tipo_contagem"
                                style="width:100%;padding:8px 10px;border:1.5px solid #bfdbfe;border-radius:8px;font-size:13px;background:#fff;">
                                <option value="uteis">Dias úteis</option>
                                <option value="corridos">Dias corridos</option>
                            </select>
                        </div>
                        <div style="grid-column:1/-1;">
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Responsável pelo prazo</label>
                            <select wire:model="acoes.{{ $ai }}.prazo_responsavel"
                                style="width:100%;padding:8px 10px;border:1.5px solid #bfdbfe;border-radius:8px;font-size:13px;background:#fff;">
                                <option value="advogado_processo">Advogado responsável pelo processo</option>
                                <option value="criador">Quem criou a regra</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                @if($acao['tipo'] === 'criar_notificacao')
                <div style="background:#faf5ff;border:1px solid #e9d5ff;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:#7c3aed;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Configuração da Notificação</div>
                    <div style="display:grid;grid-template-columns:1fr;gap:10px;">
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Título</label>
                            <input wire:model="acoes.{{ $ai }}.notif_titulo" type="text"
                                placeholder="Ex: Atenção: novo andamento em {numero}"
                                style="width:100%;padding:8px 10px;border:1.5px solid #e9d5ff;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Mensagem</label>
                            <textarea wire:model="acoes.{{ $ai }}.notif_mensagem" rows="2"
                                placeholder="Ex: O processo {numero} teve novo andamento: {andamento}"
                                style="width:100%;padding:8px 10px;border:1.5px solid #e9d5ff;border-radius:8px;font-size:13px;background:#fff;resize:vertical;font-family:inherit;box-sizing:border-box;"></textarea>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Notificar</label>
                            <select wire:model="acoes.{{ $ai }}.notif_destinatario"
                                style="width:100%;padding:8px 10px;border:1.5px solid #e9d5ff;border-radius:8px;font-size:13px;background:#fff;">
                                <option value="advogado_processo">Advogado responsável pelo processo</option>
                                <option value="todos">Todos os usuários do escritório</option>
                            </select>
                        </div>
                    </div>
                </div>
                @endif

                @if($acao['tipo'] === 'criar_agenda')
                <div style="background:#fefce8;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:#d97706;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Configuração da Agenda</div>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                        <div style="grid-column:1/-1;">
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Título do compromisso</label>
                            <input wire:model="acoes.{{ $ai }}.agenda_titulo" type="text"
                                placeholder="Ex: Acompanhar processo {numero}"
                                style="width:100%;padding:8px 10px;border:1.5px solid #fde68a;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Agendar para daqui</label>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <input wire:model="acoes.{{ $ai }}.agenda_dias" type="number" min="0" max="365"
                                    style="width:80px;padding:8px 10px;border:1.5px solid #fde68a;border-radius:8px;font-size:13px;background:#fff;text-align:center;">
                                <span style="font-size:13px;color:#374151;">dia(s)</span>
                            </div>
                        </div>
                        <div>
                            <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Horário</label>
                            <input wire:model="acoes.{{ $ai }}.agenda_hora" type="time"
                                style="width:100%;padding:8px 10px;border:1.5px solid #fde68a;border-radius:8px;font-size:13px;background:#fff;box-sizing:border-box;">
                        </div>
                        <div style="grid-column:1/-1;display:flex;align-items:center;gap:8px;margin-top:4px;">
                            <input wire:model="acoes.{{ $ai }}.agenda_urgente" type="checkbox"
                                style="width:16px;height:16px;accent-color:#d97706;cursor:pointer;">
                            <label style="font-size:13px;color:#374151;cursor:pointer;font-weight:500;">Marcar como urgente</label>
                        </div>
                    </div>
                </div>
                @endif

                @if($acao['tipo'] === 'atualizar_score')
                <div style="background:#fff7ed;border:1px solid #fed7aa;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="font-size:11px;font-weight:700;color:#ea580c;margin-bottom:10px;text-transform:uppercase;letter-spacing:.5px;">Configuração do Score</div>
                    <div>
                        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Definir score como</label>
                        <select wire:model="acoes.{{ $ai }}.score_valor"
                            style="width:100%;padding:8px 10px;border:1.5px solid #fed7aa;border-radius:8px;font-size:13px;background:#fff;">
                            <option value="auto">Automático (o sistema decide)</option>
                            <option value="critico">Crítico</option>
                            <option value="atencao">Atenção</option>
                            <option value="normal">Normal</option>
                        </select>
                    </div>
                </div>
                @endif

                @if($acao['tipo'] === 'chamar_ia')
                <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:10px;padding:14px;margin-top:10px;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        <span style="font-size:13px;color:#0c4a6e;font-weight:500;">A IA irá gerar automaticamente um resumo do andamento e salvará no processo. Nenhuma configuração necessária.</span>
                    </div>
                </div>
                @endif

            </div>
            @endforeach

            <button wire:click="adicionarAcao"
                style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border:1.5px dashed var(--border);border-radius:8px;background:none;font-size:12px;color:var(--muted);cursor:pointer;transition:color .15s,border-color .15s;"
                onmouseover="this.style.color='var(--primary)';this.style.borderColor='var(--primary)'"
                onmouseout="this.style.color='var(--muted)';this.style.borderColor='var(--border)'">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Adicionar Ação
            </button>

        </div>

        {{-- Rodapé do modal --}}
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1.5px solid var(--border);">
            <button wire:click="fecharModal"
                style="padding:10px 20px;border:1.5px solid var(--border);border-radius:8px;background:var(--white);font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;transition:color .15s;"
                onmouseover="this.style.color='var(--text)'" onmouseout="this.style.color='var(--muted)'">
                Cancelar
            </button>
            <button wire:click="salvar" wire:loading.attr="disabled" wire:loading.class="opacity-50"
                style="padding:10px 24px;background:linear-gradient(135deg,#7c3aed,#6d28d9);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;transition:opacity .15s;"
                onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                <span wire:loading.remove wire:target="salvar">{{ $editandoId ? 'Salvar Alterações' : 'Criar Regra' }}</span>
                <span wire:loading wire:target="salvar">Salvando…</span>
            </button>
        </div>
    </div>
</div>
@endif



<style>
@keyframes wf-slide-in {
    from { opacity: 0; transform: translateY(-12px); }
    to   { opacity: 1; transform: translateY(0); }
}
@media (max-width: 900px) {
    .wf-kpis { grid-template-columns: repeat(2, 1fr) !important; }
    .wf-grid { grid-template-columns: 1fr !important; }
}
@media (max-width: 480px) {
    .wf-kpis { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>



</div>

