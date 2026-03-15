<div>

  {{-- Cabeçalho --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
      <div>
        <div style="font-weight:700;font-size:15px;color:var(--primary)">🔍 Log de Auditoria</div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px">Histórico de todas as ações realizadas no sistema.</div>
      </div>
    </div>

    {{-- Filtros --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:10px;margin-top:14px">
      <div class="form-field">
        <label class="lbl">Usuário</label>
        <input wire:model.live.debounce.300ms="filtroUsuario" type="text" placeholder="Login..."
               style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>
      <div class="form-field">
        <label class="lbl">Ação</label>
        <input wire:model.live.debounce.300ms="filtroAcao" type="text" placeholder="Ex: login, criar..."
               style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>
      <div class="form-field">
        <label class="lbl">Tabela</label>
        <select wire:model.live="filtroTabela"
                style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">Todas</option>
          @foreach($tabelas as $t)
            <option value="{{ $t }}">{{ $t }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-field">
        <label class="lbl">De</label>
        <input wire:model.live="filtroDataIni" type="date"
               style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>
      <div class="form-field">
        <label class="lbl">Até</label>
        <input wire:model.live="filtroDataFim" type="date"
               style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>
      <div class="form-field" style="display:flex;align-items:flex-end">
        <button wire:click="limpar" class="btn" style="width:100%;background:var(--bg);border:1px solid var(--border);color:var(--muted)">
          Limpar
        </button>
      </div>
    </div>
  </div>

  {{-- Tabela --}}
  <div class="card" style="padding:0">
    <div class="table-wrap" style="margin:0">
      <table>
        <thead>
          <tr>
            <th>Data/Hora</th>
            <th>Usuário</th>
            <th>Ação</th>
            <th>Tabela</th>
            <th>Registro</th>
            <th>IP</th>
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
              {{ \Carbon\Carbon::parse($a->created_at)->format('d/m/Y H:i:s') }}
            </td>
            <td style="font-weight:600;font-size:13px">{{ $a->login ?? '—' }}</td>
            <td>
              <span class="badge" style="background:{{ $corAcao['bg'] }};color:{{ $corAcao['txt'] }}">
                {{ $a->acao }}
              </span>
            </td>
            <td style="font-size:12px;color:var(--muted);font-family:monospace">{{ $a->tabela ?? '—' }}</td>
            <td style="font-size:12px;color:var(--muted)">#{{ $a->registro_id ?? '—' }}</td>
            <td style="font-size:12px;color:var(--muted)">{{ $a->ip ?? '—' }}</td>
            <td>
              @if($a->dados_antes || $a->dados_apos)
              <button wire:click="verDetalhe({{ $a->id }})" class="btn btn-sm"
                      style="background:var(--bg);border:1px solid var(--border);color:var(--muted);font-size:11px">
                Ver diff
              </button>
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
    <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;font-size:12px;color:var(--muted)">
      <span>{{ $registros->firstItem() }}–{{ $registros->lastItem() }} de {{ $registros->total() }} registros</span>
      {{ $registros->links() }}
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
        <button wire:click="fecharDetalhe" style="background:none;border:none;font-size:18px;cursor:pointer;color:var(--muted)">✕</button>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">

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
