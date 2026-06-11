<div>
{{-- ══ CABEÇALHO ══ --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <div style="width:44px;height:44px;background:#eff6ff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">💸</div>
        <div>
            <h1 style="font-size:20px;font-weight:800;color:#0f2540;margin:0;">Despesas & Reembolsos</h1>
            <p style="font-size:12px;color:#64748b;margin:0;">Lançamentos de despesas de clientes e pedidos de reembolso</p>
        </div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('relatorios.despesas') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#f1f5f9;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">📊 Relatório</a>
        <a href="{{ route('relatorios.reembolso') }}" style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:#f1f5f9;color:#374151;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;">📄 Pedido Reembolso</a>
        <button wire:click="abrir()" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:700;cursor:pointer;">
            + Novo Lançamento
        </button>
    </div>
</div>

{{-- ══ TOTALIZADORES ══ --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
    @php
    $fmtR = fn($v) => 'R$ '.number_format($v,2,',','.');
    @endphp
    <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #ef4444;border-radius:12px;padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">💸 Despesas no período</div>
        <div style="font-size:20px;font-weight:800;color:#0f2540;">{{ $fmtR($totais['despesas']) }}</div>
    </div>
    <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #22c55e;border-radius:12px;padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">🔄 Reembolsos no período</div>
        <div style="font-size:20px;font-weight:800;color:#0f2540;">{{ $fmtR($totais['reembolsos']) }}</div>
    </div>
    <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #f59e0b;border-radius:12px;padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">⏳ Pendente aprovação</div>
        <div style="font-size:20px;font-weight:800;color:#0f2540;">{{ $fmtR($totais['pendente_aprovacao']) }}</div>
    </div>
    <div style="background:#fff;border:1px solid #e2e8f0;border-left:4px solid #3b82f6;border-radius:12px;padding:16px;">
        <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px;">✅ Aprovado p/ cobrança</div>
        <div style="font-size:20px;font-weight:800;color:#0f2540;">{{ $fmtR($totais['aprovado_cobranca']) }}</div>
    </div>
</div>

{{-- ══ FILTROS ══ --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;">
    <div style="flex:2;min-width:160px;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:4px;">Busca</label>
        <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Cliente ou descrição..."
            style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;">
    </div>
    <div style="min-width:120px;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:4px;">Tipo</label>
        <select wire:model.live="filtroTipo" style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;background:#fff;">
            <option value="">Todos</option>
            <option value="despesa">Despesa</option>
            <option value="reembolso">Reembolso</option>
        </select>
    </div>
    <div style="min-width:140px;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:4px;">Status</label>
        <select wire:model.live="filtroStatus" style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;background:#fff;">
            <option value="">Todos</option>
            <option value="pendente">Pendente</option>
            <option value="aprovado">Aprovado</option>
            <option value="nao_cobrar">Não cobrar</option>
            <option value="ja_cobrado">Já cobrado</option>
        </select>
    </div>
    <div style="min-width:120px;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:4px;">De</label>
        <input wire:model.live="filtroDataIni" type="date" style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;">
    </div>
    <div style="min-width:120px;">
        <label style="font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:4px;">Até</label>
        <input wire:model.live="filtroDataFim" type="date" style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;">
    </div>
</div>

{{-- ══ TABELA ══ --}}
<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead>
            <tr style="background:#f8fafc;">
                <th style="padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Data</th>
                <th style="padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Cliente</th>
                <th style="padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Tipo</th>
                <th style="padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Descrição</th>
                <th style="padding:11px 14px;text-align:right;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Valor</th>
                <th style="padding:11px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Status</th>
                <th style="padding:11px 14px;text-align:center;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Anx.</th>
                <th style="padding:11px 14px;text-align:right;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #e2e8f0;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($despesas as $d)
            <tr style="border-bottom:1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                <td style="padding:10px 14px;color:#374151;">{{ $d->data_lancamento->format('d/m/Y') }}</td>
                <td style="padding:10px 14px;font-weight:600;color:#0f2540;">{{ $d->cliente?->nome ?? '—' }}</td>
                <td style="padding:10px 14px;">
                    @if($d->tipo === 'reembolso')
                        <span style="background:#dcfce7;color:#16a34a;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">🔄 Reembolso</span>
                    @else
                        <span style="background:#fee2e2;color:#dc2626;font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">💸 Despesa</span>
                    @endif
                </td>
                <td style="padding:10px 14px;color:#475569;max-width:220px;">
                    <div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="{{ $d->descricao }}">{{ $d->descricao }}</div>
                    @if($d->responsavel)<div style="font-size:11px;color:#94a3b8;">{{ $d->responsavel->nome }}</div>@endif
                </td>
                <td style="padding:10px 14px;text-align:right;font-weight:700;color:#0f2540;">R$ {{ number_format($d->valor,2,',','.') }}</td>
                <td style="padding:10px 14px;">
                    @php $sc = match($d->status) {
                        'pendente'   => ['#fef9c3','#ca8a04','⏳ Pendente'],
                        'aprovado'   => ['#dcfce7','#16a34a','✅ Aprovado'],
                        'nao_cobrar' => ['#f1f5f9','#64748b','🚫 Não cobrar'],
                        'ja_cobrado' => ['#dbeafe','#1d4ed8','💰 Já cobrado'],
                        default      => ['#f1f5f9','#64748b',$d->status],
                    }; @endphp
                    <span style="background:{{ $sc[0] }};color:{{ $sc[1] }};font-size:11px;font-weight:700;padding:2px 8px;border-radius:99px;">{{ $sc[2] }}</span>
                </td>
                <td style="padding:10px 14px;text-align:center;">
                    @if($d->anexos_count > 0)
                        <button wire:click="abrirAnexos({{ $d->id }})" style="background:#eff6ff;color:#1d4ed8;border:none;border-radius:99px;padding:2px 8px;font-size:11px;font-weight:700;cursor:pointer;">
                            📎 {{ $d->anexos_count }}
                        </button>
                    @else
                        <button wire:click="abrirAnexos({{ $d->id }})" style="background:#f1f5f9;color:#94a3b8;border:none;border-radius:99px;padding:2px 8px;font-size:11px;cursor:pointer;">+</button>
                    @endif
                </td>
                <td style="padding:10px 14px;text-align:right;white-space:nowrap;">
                    <button wire:click="abrir({{ $d->id }})" style="background:#f1f5f9;color:#374151;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;margin-right:4px;" title="Editar">✏️</button>

                    @if($d->status === 'pendente')
                        @if($confirmarAprovarId === $d->id)
                            <span style="font-size:12px;color:#374151;">Aprovar? </span>
                            <button wire:click="aprovar({{ $d->id }})" style="background:#dcfce7;color:#16a34a;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;">Sim</button>
                            <button wire:click="$set('confirmarAprovarId', null)" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;margin-left:2px;">Não</button>
                        @else
                            <button wire:click="$set('confirmarAprovarId', {{ $d->id }})" style="background:#dcfce7;color:#16a34a;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;margin-right:4px;" title="Aprovar">✅</button>
                        @endif

                        @if($confirmarNaoCobrarId === $d->id)
                            <span style="font-size:12px;color:#374151;">Não cobrar? </span>
                            <button wire:click="marcarNaoCobrar({{ $d->id }})" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;">Sim</button>
                            <button wire:click="$set('confirmarNaoCobrarId', null)" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;margin-left:2px;">Não</button>
                        @else
                            <button wire:click="$set('confirmarNaoCobrarId', {{ $d->id }})" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;margin-right:4px;" title="Não cobrar">🚫</button>
                        @endif
                    @endif

                    @if($d->status === 'aprovado')
                        @if($gerarTituloId === $d->id)
                            <span style="font-size:12px;color:#374151;">Gerar título? </span>
                            <button wire:click="gerarTitulo({{ $d->id }})" wire:loading.attr="disabled" style="background:#dbeafe;color:#1d4ed8;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;">Sim</button>
                            <button wire:click="$set('gerarTituloId', null)" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;margin-left:2px;">Não</button>
                        @else
                            <button wire:click="$set('gerarTituloId', {{ $d->id }})" style="background:#dbeafe;color:#1d4ed8;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;margin-right:4px;" title="Gerar título financeiro">💰</button>
                        @endif
                    @endif

                    @if($confirmarExcluirId === $d->id)
                        <span style="font-size:12px;color:#374151;">Excluir? </span>
                        <button wire:click="excluir({{ $d->id }})" style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;">Sim</button>
                        <button wire:click="$set('confirmarExcluirId', null)" style="background:#f1f5f9;color:#64748b;border:none;border-radius:6px;padding:4px 8px;font-size:12px;cursor:pointer;margin-left:2px;">Não</button>
                    @else
                        <button wire:click="$set('confirmarExcluirId', {{ $d->id }})" style="background:#fee2e2;color:#dc2626;border:none;border-radius:6px;padding:5px 10px;font-size:12px;cursor:pointer;" title="Excluir">🗑️</button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;">
                    Nenhum lançamento encontrado no período.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ══ MODAL CREATE/EDIT ══ --}}
@if($modalAberto)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:flex-start;justify-content:center;overflow-y:auto;padding:24px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:640px;margin:auto;box-shadow:0 24px 60px rgba(0,0,0,.3);">
        <div style="padding:24px 28px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:17px;font-weight:800;color:#0f2540;margin:0;">{{ $despesaId ? 'Editar Lançamento' : 'Novo Lançamento' }}</h2>
            <button wire:click="fechar" style="background:none;border:none;font-size:20px;cursor:pointer;color:#64748b;">✕</button>
        </div>
        <div style="padding:24px 28px;display:flex;flex-direction:column;gap:16px;">

            {{-- Cliente --}}
            <div style="position:relative;">
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Cliente *</label>
                <input wire:model.live.debounce.300ms="clienteBusca" type="text" placeholder="Digite para buscar..."
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @error('clienteId') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
                @if(count($clienteSugestoes))
                <div style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #e2e8f0;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:100;max-height:200px;overflow-y:auto;">
                    @foreach($clienteSugestoes as $c)
                    <div wire:click="selecionarCliente({{ $c['id'] }}, '{{ addslashes($c['nome']) }}')"
                        style="padding:10px 14px;cursor:pointer;font-size:13px;border-bottom:1px solid #f1f5f9;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
                        <strong>{{ $c['nome'] }}</strong>
                        @if($c['cpf_cnpj']) <span style="color:#94a3b8;font-size:11px;"> — {{ $c['cpf_cnpj'] }}</span> @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Data --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Data *</label>
                <input wire:model="dataLancamento" type="date" style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
            </div>

            {{-- Descrição --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Descrição *</label>
                <input wire:model="descricao" type="text" placeholder="Ex: Custas processuais, DARF, GRU..."
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @error('descricao') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Valor --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Valor *</label>
                <input wire:model="valor" type="text" placeholder="0,00"
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;">
                @error('valor') <span style="font-size:12px;color:#dc2626;">{{ $message }}</span> @enderror
            </div>

            {{-- Observações --}}
            <div>
                <label style="display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.8px;margin-bottom:6px;">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Notas adicionais..."
                    style="width:100%;padding:10px 12px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;box-sizing:border-box;resize:vertical;"></textarea>
            </div>

            {{-- Anexos no modal --}}
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px;">
                <div style="font-size:12px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">📎 Anexar Documentos</div>
                <div style="display:grid;grid-template-columns:1fr auto auto;gap:8px;align-items:end;margin-bottom:10px;">
                    <div>
                        <select wire:model="tipoDocAnexo" style="width:100%;padding:8px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:13px;outline:none;background:#fff;">
                            <option value="comprovante">Comprovante</option>
                            <option value="nota_fiscal">Nota Fiscal</option>
                            <option value="guia">Guia</option>
                            <option value="darf">DARF</option>
                            <option value="gru">GRU</option>
                            <option value="recibo">Recibo</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <input wire:model="novoAnexo" type="file" accept=".pdf,.jpg,.jpeg,.png"
                            style="padding:6px 10px;border:1.5px solid #e5e7eb;border-radius:8px;font-size:12px;">
                    </div>
                    <button wire:click="adicionarAnexo" wire:loading.attr="disabled"
                        style="padding:8px 14px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">
                        + Adicionar
                    </button>
                </div>
                @error('novoAnexo') <div style="font-size:12px;color:#dc2626;margin-bottom:8px;">{{ $message }}</div> @enderror

                @if(count($anexosPendentes))
                <div style="display:flex;flex-direction:column;gap:6px;">
                    @foreach($anexosPendentes as $i => $a)
                    <div style="display:flex;align-items:center;justify-content:space-between;background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:8px 12px;">
                        <div style="font-size:12px;color:#374151;">
                            <span style="background:#eff6ff;color:#1d4ed8;padding:1px 6px;border-radius:4px;font-size:10px;font-weight:700;margin-right:6px;">{{ ucfirst($a['tipo_documento']) }}</span>
                            {{ $a['nome_original'] }}
                        </div>
                        <button wire:click="removerAnexoPendente({{ $i }})" style="background:none;border:none;color:#dc2626;cursor:pointer;font-size:14px;">✕</button>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        <div style="padding:16px 28px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px;">
            <button wire:click="fechar" style="padding:10px 20px;background:#f1f5f9;color:#374151;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;">Cancelar</button>
            <button wire:click="salvar" wire:loading.attr="disabled" style="padding:10px 24px;background:#1a3a5c;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;">
                <span wire:loading.remove wire:target="salvar">{{ $despesaId ? 'Salvar alterações' : 'Criar lançamento' }}</span>
                <span wire:loading wire:target="salvar">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ══ MODAL ANEXOS ══ --}}
@if($modalAnexos)
<div style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;display:flex;align-items:center;justify-content:center;padding:24px;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:540px;box-shadow:0 24px 60px rgba(0,0,0,.3);">
        <div style="padding:20px 24px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
            <h2 style="font-size:16px;font-weight:800;color:#0f2540;margin:0;">📎 Anexos do Lançamento</h2>
            <button wire:click="fecharAnexos" style="background:none;border:none;font-size:20px;cursor:pointer;color:#64748b;">✕</button>
        </div>
        <div style="padding:20px 24px;max-height:400px;overflow-y:auto;">
            @forelse($anexosDaDespesa as $anx)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <div>
                    <div style="font-size:13px;font-weight:600;color:#0f2540;">{{ $anx->nome_original }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                        {{ $anx->label_tipo_documento }} · {{ $anx->tamanho_formatado }} · {{ $anx->created_at->format('d/m/Y') }}
                    </div>
                </div>
                <div style="display:flex;gap:6px;">
                    <a href="{{ Storage::url($anx->caminho) }}" target="_blank" style="padding:5px 10px;background:#eff6ff;color:#1d4ed8;border-radius:6px;font-size:12px;text-decoration:none;">👁️ Ver</a>
                    <a href="{{ Storage::url($anx->caminho) }}" download="{{ $anx->nome_original }}" style="padding:5px 10px;background:#f1f5f9;color:#374151;border-radius:6px;font-size:12px;text-decoration:none;">⬇️</a>
                    <button wire:click="removerAnexo({{ $anx->id }})" wire:confirm="Excluir este anexo?" style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:12px;cursor:pointer;">🗑️</button>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:32px;color:#94a3b8;font-size:13px;">Nenhum anexo neste lançamento.</div>
            @endforelse
        </div>
    </div>
</div>
@endif

</div>
