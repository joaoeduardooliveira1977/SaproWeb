<div>

  {{-- Aviso sem token --}}
  @if(!$kpis['configurado'])
  <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 16px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;margin-bottom:16px">
    <span style="display:inline-flex;align-items:center;color:#92400e;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg></span>
    <div style="font-size:13px">
      <strong style="color:#92400e">ClickSign não configurado.</strong>
      <span style="color:#b45309"> Adicione <code style="background:#fef3c7;padding:1px 4px;border-radius:3px">CLICKSIGN_ACCESS_TOKEN</code>
      e <code style="background:#fef3c7;padding:1px 4px;border-radius:3px">CLICKSIGN_SANDBOX=true</code> ao seu <code style="background:#fef3c7;padding:1px 4px;border-radius:3px">.env</code>
      para habilitar o envio.</span>
    </div>
  </div>
  @endif



  {{-- KPIs --}}
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-bottom:20px">

    <button wire:click="$set('filtroStatus', {{ $filtroStatus === 'rascunho' ? "''" : "'rascunho'" }})"
      style="text-align:left;background:var(--white);border-radius:10px;padding:16px;border-left:4px solid #64748b;border-top:none;border-right:none;border-bottom:none;box-shadow:{{ $filtroStatus === 'rascunho' ? '0 0 0 2px #64748b' : '0 1px 3px rgba(0,0,0,.08)' }};cursor:pointer;transition:box-shadow .15s,transform .1s;"
      onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'"
      onmouseout="this.style.transform='';this.style.boxShadow='{{ $filtroStatus === 'rascunho' ? '0 0 0 2px #64748b' : '0 1px 3px rgba(0,0,0,.08)' }}'">
      <div class="stat-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px">Rascunhos</div>
      <div style="font-size:26px;font-weight:800;color:#64748b;margin-top:4px">{{ $kpis['rascunhos'] }}</div>
      @if($filtroStatus === 'rascunho')
      <div style="font-size:10px;color:#64748b;font-weight:600;margin-top:4px;">● filtrando</div>
      @endif
    </button>

    <button wire:click="$set('filtroStatus', {{ $filtroStatus === 'assinando' ? "''" : "'assinando'" }})"
      style="text-align:left;background:var(--white);border-radius:10px;padding:16px;border-left:4px solid #d97706;border-top:none;border-right:none;border-bottom:none;box-shadow:{{ $filtroStatus === 'assinando' ? '0 0 0 2px #d97706' : '0 1px 3px rgba(0,0,0,.08)' }};cursor:pointer;transition:box-shadow .15s,transform .1s;"
      onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'"
      onmouseout="this.style.transform='';this.style.boxShadow='{{ $filtroStatus === 'assinando' ? '0 0 0 2px #d97706' : '0 1px 3px rgba(0,0,0,.08)' }}'">
      <div class="stat-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#d97706;text-transform:uppercase;letter-spacing:.5px">Aguardando Assinatura</div>
      <div style="font-size:26px;font-weight:800;color:#d97706;margin-top:4px">{{ $kpis['assinando'] }}</div>
      @if($filtroStatus === 'assinando')
      <div style="font-size:10px;color:#d97706;font-weight:600;margin-top:4px;">● filtrando</div>
      @endif
    </button>

    <button wire:click="$set('filtroStatus', {{ $filtroStatus === 'concluido' ? "''" : "'concluido'" }})"
      style="text-align:left;background:var(--white);border-radius:10px;padding:16px;border-left:4px solid #16a34a;border-top:none;border-right:none;border-bottom:none;box-shadow:{{ $filtroStatus === 'concluido' ? '0 0 0 2px #16a34a' : '0 1px 3px rgba(0,0,0,.08)' }};cursor:pointer;transition:box-shadow .15s,transform .1s;"
      onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.12)'"
      onmouseout="this.style.transform='';this.style.boxShadow='{{ $filtroStatus === 'concluido' ? '0 0 0 2px #16a34a' : '0 1px 3px rgba(0,0,0,.08)' }}'">
      <div class="stat-icon"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
      <div style="font-size:11px;font-weight:700;color:#166534;text-transform:uppercase;letter-spacing:.5px">Concluídos (mês)</div>
      <div style="font-size:26px;font-weight:800;color:#166534;margin-top:4px">{{ $kpis['concluidos'] }}</div>
      @if($filtroStatus === 'concluido')
      <div style="font-size:10px;color:#16a34a;font-weight:600;margin-top:4px;">● filtrando</div>
      @endif
    </button>

  </div>

  {{-- Filtros + Novo --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
      <div style="flex:1;min-width:200px">
        <input wire:model.live.debounce.300ms="filtroBusca" type="text"
               placeholder="Buscar por título ou número do processo..."
               class="search-bar" style="width:100%">
      </div>
      <select wire:model.live="filtroStatus" style="padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
        <option value="">Todos os status</option>
        @foreach(\App\Models\Assinatura::statusLabel() as $val => $label)
          <option value="{{ $val }}">{{ $label }}</option>
        @endforeach
      </select>
      <button wire:click="abrirModal" class="btn btn-primary btn-sm">＋ Nova Solicitação</button>
    </div>
  </div>

  {{-- Tabela --}}
  <div class="card" style="padding:0;overflow:hidden">
    @if($assinaturas->isEmpty())
      <div style="padding:48px;text-align:center">
        <div style="margin-bottom:12px;display:flex;justify-content:center;color:var(--muted)"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg></div>
        <div style="font-weight:600;color:var(--text)">Nenhuma solicitação encontrada</div>
        <div style="color:var(--muted);font-size:13px;margin-top:4px">Crie uma nova solicitação para enviar documentos para assinatura.</div>
      </div>
    @else
      <div class="table-wrap" style="margin:0">
        <table>
          <thead>
            <tr>
              <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Título</th>
              <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Processo</th>
              <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Signatários</th>
              <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Prazo</th>
              <th style="text-align:center;font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Status</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            @foreach($assinaturas as $ass)
              @php
                $total    = $ass->signatarios->count();
                $assinado = $ass->signatarios->where('status', 'assinado')->count();
              @endphp
              <tr>
                <td>
                  <div style="font-weight:600;color:var(--text)">{{ $ass->titulo }}</div>
                  @if($ass->arquivo_nome)
                    <div style="font-size:11px;color:var(--muted)">{{ $ass->arquivo_nome }}</div>
                  @endif
                </td>
                <td>
                  @if($ass->processo)
                    <div style="font-size:12px;font-family:monospace;color:var(--muted)">{{ $ass->processo->numero }}</div>
                    <div style="font-size:11px;color:var(--muted)">{{ $ass->processo->cliente?->nome }}</div>
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td style="text-align:center">
                  @if($total > 0)
                    <div style="font-size:13px;font-weight:600;color:{{ $assinado === $total ? '#16a34a' : 'var(--text)' }}">
                      {{ $assinado }}/{{ $total }}
                    </div>
                    <div style="width:56px;height:4px;background:#e2e8f0;border-radius:4px;margin:4px auto 0">
                      <div style="height:4px;border-radius:4px;background:{{ $assinado === $total ? '#16a34a' : '#2563eb' }};width:{{ $total ? round($assinado/$total*100) : 0 }}%"></div>
                    </div>
                  @else
                    <span style="color:var(--muted);font-size:12px">sem signatários</span>
                  @endif
                </td>
                <td>
                  @if($ass->deadline_at)
                    <div style="font-size:13px;{{ $ass->deadline_at->isPast() && !in_array($ass->status,['concluido','cancelado']) ? 'color:#dc2626;font-weight:700' : 'color:var(--text)' }}">
                      {{ $ass->deadline_at->format('d/m/Y') }}
                    </div>
                  @else
                    <span style="color:var(--muted)">—</span>
                  @endif
                </td>
                <td style="text-align:center">
                  <span class="badge" style="{{ $ass->statusCor() }};font-size:11px;padding:3px 10px;border-radius:12px">
                    {{ \App\Models\Assinatura::statusLabel()[$ass->status] ?? $ass->status }}
                  </span>
                </td>
                <td>
                  <div style="display:flex;gap:4px;justify-content:flex-end;align-items:center">

                    @if($ass->podeEnviar())
                      <button wire:click="enviarAssinatura({{ $ass->id }})"
                              wire:loading.attr="disabled"
                              wire:target="enviarAssinatura({{ $ass->id }})"
                              @if(!$kpis['configurado']) disabled title="Configure o ClickSign primeiro" @endif
                              class="btn btn-sm"
                              style="{{ $kpis['configurado'] ? 'background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe' : 'background:#f1f5f9;color:var(--muted);border:1px solid var(--border);cursor:not-allowed' }};padding:4px 10px;font-size:12px">
                        <span wire:loading.remove wire:target="enviarAssinatura({{ $ass->id }})">▶ Enviar</span>
                        <span wire:loading wire:target="enviarAssinatura({{ $ass->id }})">Enviando...</span>
                      </button>
                    @endif

                    @if(in_array($ass->status, ['enviado','assinando']) && $ass->clicksign_list_key)
                      <button wire:click="sincronizarStatus({{ $ass->id }})" title="Sincronizar status" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#eff6ff;color:#0369a1;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg></button>
                    @endif

                    <button wire:click="verDetalhe({{ $ass->id }})" title="Ver detalhes" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#f8fafc;color:#475569;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></button>

                    @if($ass->podeCancelar())
                      <button wire:click="cancelar({{ $ass->id }})" wire:confirm="Cancelar esta solicitação de assinatura?"
                              title="Cancelar" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></button>
                    @endif

                    @if(in_array($ass->status, ['rascunho','cancelado','erro']))
                      <button wire:click="excluir({{ $ass->id }})" wire:confirm="Excluir esta solicitação?"
                              title="Excluir" style="width:30px;height:30px;border:none;border-radius:6px;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;background:#fee2e2;color:#dc2626;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></button>
                    @endif
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @if($assinaturas->hasPages())
        <div class="pagination">{{ $assinaturas->links() }}</div>
      @endif
    @endif
  </div>


  {{-- ─── Modal Nova Solicitação ──────────────────────────── --}}
  @if($modalAberto)
  <div class="modal-backdrop" wire:click.self="fecharModal">
    <div class="modal" style="max-width:640px;max-height:90vh;overflow-y:auto">
      <div class="modal-header">
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/></svg> Nova Solicitação de Assinatura</span>
        <button wire:click="fecharModal" class="modal-close" aria-label="Fechar"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:16px">

        {{-- Dados gerais --}}
        <div class="form-field">
          <label class="lbl">Título *</label>
          <input wire:model="titulo" type="text" placeholder="Ex: Contrato de Honorários — João Silva">
          @error('titulo') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-grid">
          <div class="form-field">
            <label class="lbl">Processo (opcional)</label>
            <select wire:model="processo_id">
              <option value="">Sem processo</option>
              @foreach($processos as $proc)
                <option value="{{ $proc->id }}">{{ $proc->numero }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-field">
            <label class="lbl">Prazo para assinatura</label>
            <input wire:model="deadline_at" type="date">
            @error('deadline_at') <span class="invalid-feedback">{{ $message }}</span> @enderror
          </div>
        </div>

        {{-- Documento --}}
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Documento PDF</div>
          <div class="form-grid">
            <div class="form-field">
              <label class="lbl">Upload de arquivo</label>
              <input wire:model="arquivoUpload" type="file" accept=".pdf" style="font-size:13px">
              @error('arquivoUpload') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-field">
              <label class="lbl">Ou usar documento existente</label>
              <select wire:model="documento_id">
                <option value="">Selecionar documento...</option>
                @foreach($documentos as $doc)
                  <option value="{{ $doc->id }}">{{ $doc->titulo }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div style="font-size:11px;color:var(--muted);margin-top:4px">O arquivo de upload tem prioridade sobre o documento selecionado.</div>
        </div>

        {{-- Signatários --}}
        <div>
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">Signatários</div>
            @if(count($signatarios) > 0)
              <span style="font-size:12px;color:var(--muted)">{{ count($signatarios) }} adicionado(s)</span>
            @endif
          </div>

          @error('signatarios') <div style="font-size:12px;color:#dc2626;margin-bottom:8px">{{ $message }}</div> @enderror

          {{-- Lista dos adicionados --}}
          @foreach($signatarios as $i => $sig)
          <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border:1px solid var(--border);border-radius:6px;margin-bottom:6px;font-size:13px">
            <div>
              <span style="font-weight:600;color:var(--text)">{{ $sig['nome'] }}</span>
              <span style="color:var(--muted);margin-left:8px">{{ $sig['email'] }}</span>
              <span class="badge" style="background:#eef2ff;color:#4338ca;border:1px solid #c7d2fe;font-size:11px;margin-left:6px">
                {{ \App\Models\AssinaturaSignatario::papeisLabel()[$sig['papel']] ?? $sig['papel'] }}
              </span>
              <span class="badge" style="background:#f8fafc;color:var(--muted);border:1px solid var(--border);font-size:11px;margin-left:4px">
                via {{ \App\Models\AssinaturaSignatario::authsLabel()[$sig['auth']] ?? $sig['auth'] }}
              </span>
            </div>
            <button wire:click="removerSignatario({{ $i }})"
                    style="color:var(--muted);background:none;border:none;font-size:18px;cursor:pointer;line-height:1">&times;</button>
          </div>
          @endforeach

          {{-- Formulário para adicionar --}}
          <div style="padding:12px;border:1px dashed var(--border);border-radius:6px">
            <div style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:8px">Adicionar signatário</div>
            <div class="form-grid" style="margin-bottom:8px">
              <input wire:model="sig_nome" type="text" placeholder="Nome completo"
                     style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
              <input wire:model="sig_email" type="email" placeholder="E-mail"
                     style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
              <input wire:model="sig_cpf" type="text" placeholder="CPF (opcional)"
                     style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
              <input wire:model="sig_celular" type="text" placeholder="Celular (opcional)"
                     style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
              <select wire:model="sig_papel"
                      style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
                @foreach(\App\Models\AssinaturaSignatario::papeisLabel() as $val => $label)
                  <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
              </select>
              <select wire:model="sig_auth"
                      style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
                @foreach(\App\Models\AssinaturaSignatario::authsLabel() as $val => $label)
                  <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
              </select>
            </div>
            @error('sig_nome')  <div style="font-size:11px;color:#dc2626;margin-bottom:4px">{{ $message }}</div> @enderror
            @error('sig_email') <div style="font-size:11px;color:#dc2626;margin-bottom:4px">{{ $message }}</div> @enderror
            <button wire:click="adicionarSignatario" class="btn btn-outline btn-sm" style="width:100%">
              ＋ Adicionar Signatário
            </button>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button wire:click="fecharModal" class="btn btn-outline">Cancelar</button>
        <button wire:click="salvar" class="btn btn-primary" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg> Salvar como Rascunho</button>
      </div>
    </div>
  </div>
  @endif


  {{-- ─── Modal Detalhe ────────────────────────────────────── --}}
  @if($modalDetalhe && $detalhe)
  <div class="modal-backdrop" wire:click.self="$set('modalDetalhe', false)">
    <div class="modal" style="max-width:520px;max-height:85vh;overflow-y:auto">
      <div class="modal-header">
        <span class="modal-title" style="display:inline-flex;align-items:center;gap:6px;"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> {{ $detalhe->titulo }}</span>
        <button wire:click="$set('modalDetalhe', false)" class="modal-close" aria-label="Fechar"><svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></button>
      </div>

      <div style="padding:20px;display:flex;flex-direction:column;gap:14px;font-size:13px">

        <div class="form-grid">
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Status</div>
            <span class="badge" style="{{ $detalhe->statusCor() }};font-size:12px;padding:3px 10px;border-radius:12px">
              {{ \App\Models\Assinatura::statusLabel()[$detalhe->status] }}
            </span>
          </div>
          @if($detalhe->processo)
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Processo</div>
            <div style="font-family:monospace;color:var(--text)">{{ $detalhe->processo->numero }}</div>
          </div>
          @endif
          @if($detalhe->deadline_at)
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Prazo</div>
            <div>{{ $detalhe->deadline_at->format('d/m/Y') }}</div>
          </div>
          @endif
          @if($detalhe->enviado_em)
          <div>
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px">Enviado em</div>
            <div>{{ $detalhe->enviado_em->format('d/m/Y H:i') }}</div>
          </div>
          @endif
        </div>

        @if($detalhe->erro_mensagem)
        <div class="alert-error">
          <strong>Erro:</strong> {{ $detalhe->erro_mensagem }}
        </div>
        @endif

        {{-- Signatários --}}
        <div>
          <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Signatários</div>
          <div style="display:flex;flex-direction:column;gap:8px">
            @foreach($detalhe->signatarios as $sig)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:#f8fafc;border:1px solid var(--border);border-radius:6px">
              <div>
                <div style="font-weight:600;color:var(--text)">{{ $sig->nome }}</div>
                <div style="font-size:12px;color:var(--muted)">{{ $sig->email }}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px">
                  {{ \App\Models\AssinaturaSignatario::papeisLabel()[$sig->papel] ?? $sig->papel }}
                  · via {{ \App\Models\AssinaturaSignatario::authsLabel()[$sig->auth] ?? $sig->auth }}
                </div>
              </div>
              <div style="text-align:right">
                <span class="badge" style="{{ $sig->statusCor() }};font-size:12px;padding:3px 10px;border-radius:12px">
                  {{ ucfirst($sig->status) }}
                </span>
                @if($sig->assinado_em)
                  <div style="font-size:11px;color:#16a34a;margin-top:3px">{{ $sig->assinado_em->format('d/m/Y H:i') }}</div>
                @endif
              </div>
            </div>
            @endforeach
          </div>
        </div>

        @if($detalhe->clicksign_list_key)
        <div style="font-size:11px;color:var(--muted)">
          List key: <code style="background:#f1f5f9;padding:1px 4px;border-radius:3px">{{ $detalhe->clicksign_list_key }}</code>
        </div>
        @endif
      </div>
    </div>
  </div>
  @endif

</div>
