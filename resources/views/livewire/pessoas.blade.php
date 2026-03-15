<div>
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 Pessoas Cadastradas</span>
            <div class="card-actions">
                <button wire:click="exportarCsv" wire:loading.attr="disabled"
                    class="btn btn-sm btn-secondary-outline" title="Exportar CSV">
                    <span wire:loading.remove wire:target="exportarCsv">📥 CSV</span>
                    <span wire:loading wire:target="exportarCsv">Gerando…</span>
                </button>
                <button wire:click="abrirModal()" class="btn btn-primary btn-sm">＋ Nova Pessoa</button>
            </div>
        </div>

        <div class="search-bar">
            <input type="text" wire:model.live.debounce.300ms="busca" placeholder="Buscar por nome, CPF, e-mail...">
            <select wire:model.live="tipo">
                <option value="">Todos os tipos</option>
                @foreach($tiposDisponiveis as $t)
                    <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
            </select>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th class="hide-sm">CPF/CNPJ</th>
                        <th>Tipos</th>
                        <th class="hide-sm">Telefone</th>
                        <th class="hide-sm">Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pessoas as $p)
                    <tr>
                        <td><strong>{{ $p->nome }}</strong></td>
                        <td class="hide-sm">{{ $p->cpf_cnpj ?? '—' }}</td>
                        <td>
                            @foreach($tiposPorPessoa->get($p->id, []) as $tipo)
                            @php
                            $corTipo = match($tipo) {
                                'Cliente'       => ['#2563a8','#e0ecff'],
                                'Advogado'      => ['#16a34a','#dcfce7'],
                                'Juiz'          => ['#d97706','#fef3c7'],
                                'Parte Contrária' => ['#dc2626','#fee2e2'],
                                default         => ['#7c3aed','#ede9fe'],
                            };
                            @endphp
                            <span class="badge" style="color:{{ $corTipo[0] }};background:{{ $corTipo[1] }};margin-right:3px">{{ $tipo }}</span>
                            @endforeach
                        </td>
                        <td class="hide-sm">{{ $p->telefone ?? $p->celular ?? '—' }}</td>
                        <td class="hide-sm">{{ $p->email ?? '—' }}</td>
                        <td>
                            <button wire:click="abrirModal({{ $p->id }})" class="btn-icon" title="Editar">✏️</button>
                            <button wire:click="desativar({{ $p->id }})" class="btn-icon" title="Desativar" onclick="return confirm('Desativar {{ addslashes($p->nome) }}?')">🗑️</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:#64748b;padding:24px">Nenhuma pessoa encontrada.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span>Mostrando {{ $pessoas->firstItem() }}–{{ $pessoas->lastItem() }} de {{ $pessoas->total() }}</span>
            <div class="page-btns">
                <button wire:click="previousPage" class="page-btn" @disabled($pessoas->onFirstPage())>← Anterior</button>
                <span class="page-current">{{ $pessoas->currentPage() }} / {{ $pessoas->lastPage() }}</span>
                <button wire:click="nextPage" class="page-btn" @disabled(!$pessoas->hasMorePages())>Próxima →</button>
            </div>
        </div>

        <div style="margin-top:12px;padding:10px 14px;background:#f0f9ff;border-radius:8px;font-size:12px;color:#64748b;border:1px solid #bae6fd">
            ℹ️ Uma pessoa pode ter múltiplos tipos (ex: Advogado que também é Cliente) sem duplicar o cadastro.
        </div>
    </div>

    {{-- Modal Cadastro --}}
    @if($modalAberto)
    <div class="modal-backdrop" wire:click.self="fecharModal">
        <div class="modal">
            <div class="modal-header">
                <span class="modal-title">{{ $pessoaId ? '✏️ Editar Pessoa' : '👤 Nova Pessoa' }}</span>
                <button wire:click="fecharModal" class="modal-close">×</button>
            </div>

            <div class="form-grid">
                <div class="form-field" style="grid-column:1/-1">
                    <label class="lbl">Nome Completo *</label>
                    <input type="text" wire:model="nome" placeholder="Nome da pessoa">
                    @error('nome')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">CPF / CNPJ</label>
                    <input type="text" wire:model="cpf_cnpj" placeholder="000.000.000-00">
                    @error('cpf_cnpj')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-field">
                    <label class="lbl">RG</label>
                    <input type="text" wire:model="rg" placeholder="Número do RG">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Data de Nascimento</label>
                    <input type="date" wire:model="data_nascimento">
                </div>
                <div class="form-field">
                    <label class="lbl">OAB (se Advogado)</label>
                    <input type="text" wire:model="oab" placeholder="Número da OAB">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label class="lbl">Telefone</label>
                    <input type="text" wire:model="telefone" placeholder="(00) 0000-0000">
                </div>
                <div class="form-field">
                    <label class="lbl">Celular</label>
                    <input type="text" wire:model="celular" placeholder="(00) 00000-0000">
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field" style="grid-column:1/-1">
                    <label class="lbl">Email</label>
                    <input type="email" wire:model="email" placeholder="email@exemplo.com">
                    @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Endereço</label>
                <input type="text" wire:model="logradouro" placeholder="Logradouro, número, complemento" style="margin-bottom:8px">
                <div class="form-grid-3" style="margin-bottom:0">
                    <input type="text" wire:model="cidade" placeholder="Cidade">
                    <input type="text" wire:model="estado" placeholder="UF" maxlength="2">
                    <input type="text" wire:model="cep" placeholder="CEP">
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Tipos de Pessoa * (marque todos que se aplicam)</label>
                @error('tipos_selecionados')<span class="invalid-feedback">{{ $message }}</span>@enderror
                <div style="display:flex;gap:14px;flex-wrap:wrap;margin-top:6px">
                    @foreach($tiposDisponiveis as $t)
                    <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
                        <input type="checkbox" wire:model="tipos_selecionados" value="{{ $t }}" style="width:auto">
                        {{ $t }}
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="form-field" style="margin-bottom:14px">
                <label class="lbl">Observações</label>
                <textarea wire:model="observacoes" rows="2" placeholder="Observações internas..."></textarea>
            </div>

            <div class="modal-footer">
                <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
                <button wire:click="salvar" class="btn btn-success">✓ Salvar</button>
            </div>
        </div>
    </div>
    @endif
</div>
