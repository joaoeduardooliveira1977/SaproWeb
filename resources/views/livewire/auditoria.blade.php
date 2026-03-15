<div>

  {{-- Cabeçalho --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
      <div>
        <div style="font-weight:700;font-size:15px;color:var(--primary);display:flex;align-items:center;gap:6px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
          Log de Auditoria
        </div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px">Histórico de todas as ações realizadas no sistema.</div>
      </div>
    </div>

    {{-- Filtros --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-top:14px">
      <div class="form-field">
        <label class="lbl">Usuário</label>
        <input wire:model.live.debounce.300ms="filtroUsuario" type="text" placeholder="Login...">
      </div>
      <div class="form-field">
        <label class="lbl">Ação</label>
        <input wire:model.live.debounce.300ms="filtroAcao" type="text" placeholder="Ex: login, criar...">
      </div>
      <div class="form-field">
        <label class="lbl">Tabela</label>
        <select wire:model.live="filtroTabela">
          <option value="">Todas</option>
          @foreach($tabelas as $t)
            <option value="{{ $t }}">{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label class="lbl">De</label>
        <input wire:model.live="filtroDataIni" type="date">
      </div>
      <div class="form-field">
        <label class="lbl">Até</label>
        <input wire:model.live="filtroDataFim" type="date">
      </div>
      <div class="form-field" style="display:flex;align-items:flex-end">
        <button wire:click="limpar" class="btn btn-secondary-outline" style="width:100%">Limpar</button>
      </div>
    </div>
  </div>

  {{-- Tabela --}}
  <div class="card" style="padding:0">
    <div class="table-wrap" style="margin:0">
      <table>
        <thead>
          <tr>
            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Data/Hora</th>
            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Usuário</th>
            <th style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Ação</th>
            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Tabela</th>
            <th class="hide-xs" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">Registro</th>
            <th class="hide-sm" style="font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.5px;">IP</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($registros as $a)
          @php
            $corAcao = match(true) {
              str_contains(strtolower($a->acao), 'exclu') || str_contains(strtolower($a->acao), 'delet') => ['bg'=>'#fee2e2','txt'=>'#dc2626'],
              str_contains(strtolower($a->acao), 'cri')  || str_contains(strtolower($a->acao), 'cad')   => ['bg'=>'#dcfce7','txt'=>'#16a34a'],
              str_contains(strtolower($a->acao), 'edi')  || str_contains(strtolower($a->acao), 'atu')   => ['bg'=>'#fef9c3','txt'=>'#854d0e'],
              str_contains(strtolower($a->acao), 'login')                                                 => ['bg'=>'#ede9fe','txt'=>'#7c3aed'],
              default => ['bg'=>'#dbeafe','txt'=>'#2563a8'],
            };
          @endphp
          <tr>
            <td style="white-space:nowrap;font-size:12px;color:var(--muted)">
              {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i') }}
            </td>
            <td style="font-weight:600;">{{ $a->login ?? '—' }}</td>
            <td><span class="badge" style="background:{{ $corAcao['bg'] }};color:{{ $corAcao['txt'] }}">{{ $a->acao }}</span></td>
            <td class="hide-sm" style="font-size:12px;color:var(--muted);font-family:monospace">{{ $a->tabela ?? '—' }}</td>
            <td class="hide-xs" style="font-size:12px;color:var(--muted)">#{{ $a->registro_id ?? '—' }}</td>
            <td class="hide-sm" style="font-size:12px;color:var(--muted)">{{ $a->ip ?? '—' }}</td>
            <td>
              @if($a->dados_antes || $a->dados_apos)
              <button wire:click="verDetalhe({{ $a->id }})" class="btn btn-sm btn-secondary-outline" style="font-size:11px">diff</button>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" style="text-align:center;padding:40px;color:var(--muted)">
              Nenhum registro encontrado.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Paginação --}}
    @if($registros->hasPages())
    <div class="pagination-bar" style="padding:12px 16px;border-top:1px solid var(--border);">
      <span>{{ $registros->firstItem() }}–{{ $registros->lastItem() }} de {{ $registros->total() }}</span>
      <div class="page-btns">
        <button wire:click="previousPage" class="page-btn" @disabled($registros->onFirstPage())>← Anterior</button>
        <span class="page-current">{{ $registros->currentPage() }} / {{ $registros->lastPage() }}</span>
        <button wire:click="nextPage" class="page-btn" @disabled(!$registros->hasMorePages())>Próxima →</button>
      </div>
    </div>
    @endif
  </div>

  {{-- Modal detalhe diff --}}
  @if($detalhe)
  <div class="modal-backdrop" wire:click.self="fecharDetalhe">
    <div class="modal" style="max-width:680px;width:95vw">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px">
        <div>
          <div style="font-weight:700;font-size:14px;color:var(--primary)">Detalhe da Ação</div>
          <div style="font-size:12px;color:var(--muted);margin-top:2px">
            {{ $detalhe->login }} · {{ \Carbon\Carbon::parse($detalhe->created_at)->format('d/m/Y H:i:s') }}
          </div>
        </div>
        <button wire:click="fecharDetalhe" style="background:none;border:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <div class="form-grid">

        {{-- Antes --}}
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--danger);margin-bottom:6px">
            Antes
          </div>
          @if($detalhe->dados_antes)
          @php $antes = is_string($detalhe->dados_antes) ? json_decode($detalhe->dados_antes, true) : (array)$detalhe->dados_antes; @endphp
          <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;padding:12px;font-size:12px;font-family:monospace;overflow:auto;max-height:300px">
            @foreach($antes as $campo => $valor)
            <div style="margin-bottom:4px">
              <span style="color:#991b1b;font-weight:700">{{ $campo }}</span>:
              <span style="color:#1e293b">{{ is_array($valor) ? json_encode($valor) : $valor }}</span>
            </div>
            @endforeach
          </div>
          @else
          <div style="color:var(--muted);font-size:12px;padding:12px;background:var(--bg);border-radius:6px">—</div>
          @endif
        </div>

        {{-- Depois --}}
        <div>
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--success);margin-bottom:6px">
            Depois
          </div>
          @if($detalhe->dados_apos)
          @php $apos = is_string($detalhe->dados_apos) ? json_decode($detalhe->dados_apos, true) : (array)$detalhe->dados_apos; @endphp
          <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:12px;font-size:12px;font-family:monospace;overflow:auto;max-height:300px">
            @foreach($apos as $campo => $valor)
            @php
              $mudou = isset($antes[$campo]) && $antes[$campo] != $valor;
            @endphp
            <div style="margin-bottom:4px;{{ $mudou ? 'background:#dcfce7;border-radius:3px;padding:1px 4px;margin-left:-4px' : '' }}">
              <span style="color:#15803d;font-weight:700">{{ $campo }}</span>:
              <span style="color:#1e293b">{{ is_array($valor) ? json_encode($valor) : $valor }}</span>
              @if($mudou)<span style="color:#16a34a;font-size:10px;margin-left:4px">← alterado</span>@endif
            </div>
            @endforeach
          </div>
          @else
          <div style="color:var(--muted);font-size:12px;padding:12px;background:var(--bg);border-radius:6px">—</div>
          @endif
        </div>

      </div>
    </div>
  </div>
  @endif

</div>
