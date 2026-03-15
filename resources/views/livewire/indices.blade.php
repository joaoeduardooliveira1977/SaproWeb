<div>


  {{-- Cabeçalho + ação --}}
  <div class="card" style="margin-bottom:16px">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;">
      <div>
        <div style="font-weight:700;font-size:15px;color:var(--primary)"><div style="display:flex;align-items:center;gap:8px;"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg> Índices Monetários</div></div>
        <div style="font-size:12px;color:var(--muted);margin-top:2px">
          IPCA, IGP-M, SELIC e TR — importados via API do Banco Central do Brasil (SGS/BACEN).
        </div>
      </div>
      <div class="card-actions">
        <select wire:model="siglaFiltro">
          <option value="">Todos os índices</option>
          <option value="IPCA">IPCA</option>
          <option value="IGPM">IGP-M</option>
          <option value="SELIC">SELIC</option>
          <option value="TR">TR</option>
        </select>
        <button wire:click="atualizarTodos" wire:loading.attr="disabled" wire:target="atualizarTodos"
                class="btn btn-primary btn-sm">
          <span wire:loading.remove wire:target="atualizarTodos" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg> Atualizar agora</span>
          <span wire:loading wire:target="atualizarTodos">Buscando BACEN...</span>
        </button>
      </div>
    </div>
  </div>

  {{-- Cards de status por índice --}}
  @if($resumo->isEmpty())
    <div class="card" style="text-align:center;padding:48px">
      <div style="margin-bottom:12px"><svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg></div>
      <div style="font-weight:600;color:var(--text)">Nenhum índice importado ainda</div>
      <div style="color:var(--muted);font-size:13px;margin-top:8px;margin-bottom:20px">
        Clique em <strong>Atualizar agora</strong> para importar IPCA, IGP-M, SELIC e TR desde 2000.
      </div>
      <button wire:click="atualizarTodos" wire:loading.attr="disabled"
              class="btn btn-primary">
        <span wire:loading.remove wire:target="atualizarTodos" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.51"/></svg> Importar índices</span>
        <span wire:loading wire:target="atualizarTodos" style="display:inline-flex;align-items:center;gap:6px;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Importando (pode levar 1–2 min)...</span>
      </button>
    </div>
  @else
    {{-- Resumo por sigla --}}
    <div class="stat-grid">
      @php
        $corSigla = ['IPCA'=>'#2563eb','IGPM'=>'#7c3aed','SELIC'=>'#16a34a','TR'=>'#d97706'];
      @endphp
      @foreach($resumo as $r)
        @php $cor = $corSigla[$r->sigla] ?? '#64748b'; @endphp
        <div class="stat-card" style="border-left-color:{{ $cor }}">
          <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/><line x1="2" y1="20" x2="22" y2="20"/></svg></div>
          <div style="font-size:11px;font-weight:700;color:{{ $cor }};text-transform:uppercase;letter-spacing:.5px">{{ $r->sigla }}</div>
          <div style="font-size:11px;color:var(--muted);margin:2px 0 8px">{{ $r->nome }}</div>
          <div style="font-size:20px;font-weight:800;color:var(--text)">{{ $r->total_meses }} <span style="font-size:12px;font-weight:400;color:var(--muted)">meses</span></div>
          <div style="font-size:11px;color:var(--muted);margin-top:4px">
            De {{ \Carbon\Carbon::parse($r->de)->format('m/Y') }}
            até {{ \Carbon\Carbon::parse($r->ate)->format('m/Y') }}
          </div>
          <div style="font-size:10px;color:var(--muted);margin-top:2px">
            Atualizado: {{ \Carbon\Carbon::parse($r->atualizado_em)->format('d/m/Y H:i') }}
          </div>
        </div>
      @endforeach
    </div>

    {{-- Últimos 12 meses --}}
    <div class="card">
      <div style="padding:14px 16px;font-weight:600;font-size:14px;color:var(--primary);border-bottom:1px solid var(--border)">
        <div style="display:flex;align-items:center;gap:8px;"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg> Últimos 12 meses</div>
      </div>
      <div class="table-wrap" style="margin:0">
        <table>
          <thead>
            <tr>
              <th>Mês/Ano</th>
              @foreach(['IPCA','IGPM','SELIC','TR'] as $s)
                <th style="text-align:right;color:{{ $corSigla[$s] ?? '#64748b' }}">{{ $s }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @php
              // Monta mapa: mes_ref → sigla → percentual
              $mapa = [];
              foreach(['IPCA','IGPM','SELIC','TR'] as $s) {
                  foreach(($ultimos[$s] ?? collect()) as $row) {
                      $mapa[$row->mes_ref][$s] = $row->percentual;
                  }
              }
              krsort($mapa); // mais recente primeiro
            @endphp

            @forelse($mapa as $mesRef => $vals)
              <tr>
                <td style="font-weight:600">{{ \Carbon\Carbon::parse($mesRef)->format('m/Y') }}</td>
                @foreach(['IPCA','IGPM','SELIC','TR'] as $s)
                  @php
                    $v = $vals[$s] ?? null;
                    $cor = $v === null ? 'var(--muted)' : ($v >= 0 ? 'var(--text)' : 'var(--danger)');
                  @endphp
                  <td style="text-align:right;color:{{ $cor }};font-family:monospace;font-size:12px">
                    {{ $v !== null ? number_format($v, 4, ',', '.') . '%' : '—' }}
                  </td>
                @endforeach
              </tr>
            @empty
              <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px">Sem dados nos últimos 12 meses.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Instrução CLI --}}
    <div class="card" style="background:#f8fafc;margin-top:8px">
      <div style="padding:14px 16px;font-size:12px;color:var(--muted)">
        <strong>Comandos disponíveis:</strong><br>
        <code style="background:#e2e8f0;padding:2px 6px;border-radius:4px">php artisan indices:atualizar</code>
        — importa todos os índices<br>
        <code style="background:#e2e8f0;padding:2px 6px;border-radius:4px">php artisan indices:atualizar --sigla=IPCA</code>
        — apenas um índice<br>
        <code style="background:#e2e8f0;padding:2px 6px;border-radius:4px">php artisan indices:atualizar --desde=2010</code>
        — a partir de um ano específico<br>
        <code style="background:#e2e8f0;padding:2px 6px;border-radius:4px">php artisan indices:atualizar --force</code>
        — reimporta mesmo os já existentes
      </div>
    </div>
  @endif

</div>
