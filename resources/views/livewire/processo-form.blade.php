<div>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
    <div>
      <div style="font-weight:700;font-size:15px;color:var(--primary)">
        {{ $processoId ? '✏️ Editar Processo' : '➕ Novo Processo' }}
      </div>
    </div>
    <a href="{{ route('processos') }}" style="font-size:13px;color:var(--muted);text-decoration:none">← Voltar</a>
  </div>

  <div class="card">
    <div class="form-grid">

      {{-- 1. Cliente --}}
      <div class="form-field" style="grid-column:1/-1">
        <label class="lbl">Cliente *</label>
        <select wire:model="cliente_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($clientes as $c)
            <option value="{{ $c->id }}">{{ $c->nome }}</option>
          @endforeach
        </select>
        @error('cliente_id') <span style="color:var(--danger);font-size:11px">{{ $message }}</span> @enderror
      </div>

      {{-- 2. Número do Processo --}}
      <div class="form-field">
        <label class="lbl">Número do Processo *</label>
        <input wire:model.live.debounce.400ms="numero" type="text"
               placeholder="0000000-00.0000.8.26.0001"
               style="width:100%;padding:8px 12px;border:1px solid {{ $numeroValido ? 'var(--success)' : 'var(--border)' }};border-radius:6px;font-size:13px">
        @error('numero')
          <span style="color:var(--danger);font-size:11px">{{ $message }}</span>
        @else
          @if($tribunalDetectado)
            <span style="font-size:11px;color:var(--success);font-weight:600">
              ✓ Tribunal detectado: {{ $tribunalDetectado }} — compatível com Consulta Judicial
            </span>
          @elseif(strlen($numero) > 5)
            <span style="font-size:11px;color:var(--warning)">
              ⚠ Número não reconhecido pelo DATAJUD. Use o formato CNJ: <strong>NNNNNNN-DD.AAAA.J.TT.OOOO</strong>
            </span>
          @else
            <span style="font-size:11px;color:var(--muted)">
              Formato CNJ: <strong>0001234-56.2023.8.26.0001</strong> — necessário para Consulta Judicial
            </span>
          @endif
        @enderror
      </div>

      {{-- 3. Data de Distribuição --}}
      <div class="form-field">
        <label class="lbl">Data de Distribuição</label>
        <input wire:model="data_distribuicao" type="date"
               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>

      {{-- 4. Parte Contrária --}}
      <div class="form-field" style="grid-column:1/-1">
        <label class="lbl">Parte Contrária</label>
        <input wire:model="parte_contraria" type="text"
               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>

      {{-- 5. Advogado Responsável --}}
      <div class="form-field">
        <label class="lbl">Advogado Responsável</label>
        <select wire:model="advogado_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($advogados as $a)
            <option value="{{ $a->id }}">{{ $a->nome }}</option>
          @endforeach
        </select>
      </div>

      {{-- 6. Juiz --}}
      <div class="form-field">
        <label class="lbl">Juiz</label>
        <select wire:model="juiz_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($juizes as $j)
            <option value="{{ $j->id }}">{{ $j->nome }}</option>
          @endforeach
        </select>
      </div>

      {{-- 7. Tipo de Ação --}}
      <div class="form-field">
        <label class="lbl">Tipo de Ação</label>
        <select wire:model="tipo_acao_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($tiposAcao as $t)
            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 8. Tipo de Processo --}}
      <div class="form-field">
        <label class="lbl">Tipo de Processo</label>
        <select wire:model="tipo_processo_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($tiposProcesso as $t)
            <option value="{{ $t->id }}">{{ $t->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 9. Assunto --}}
      <div class="form-field">
        <label class="lbl">Assunto</label>
        <select wire:model="assunto_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($assuntos as $a)
            <option value="{{ $a->id }}">{{ $a->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 10. Situação/Fase --}}
      <div class="form-field">
        <label class="lbl">Situação/Fase</label>
        <select wire:model="fase_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($fases as $f)
            <option value="{{ $f->id }}">{{ $f->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 11. Status --}}
      <div class="form-field">
        <label class="lbl">Status</label>
        <select wire:model="status" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="Ativo">Ativo</option>
          <option value="Suspenso">Suspenso</option>
          <option value="Arquivado">Arquivado</option>
          <option value="Encerrado">Encerrado</option>
        </select>
      </div>

      {{-- 12. Grau de Risco --}}
      <div class="form-field">
        <label class="lbl">Grau de Risco</label>
        <select wire:model="risco_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($riscos as $r)
            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 13. Repartição/Fórum --}}
      <div class="form-field">
        <label class="lbl">Repartição/Fórum</label>
        <select wire:model="reparticao_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($reparticoes as $r)
            <option value="{{ $r->id }}">{{ $r->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 14. Secretaria --}}
      <div class="form-field">
        <label class="lbl">Secretaria</label>
        <select wire:model="secretaria_id" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
          <option value="">— Selecione —</option>
          @foreach($secretarias as $s)
            <option value="{{ $s->id }}">{{ $s->descricao }}</option>
          @endforeach
        </select>
      </div>

      {{-- 15. Vara --}}
      <div class="form-field">
        <label class="lbl">Vara</label>
        <input wire:model="vara" type="text"
               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>

      {{-- 16. Valor da Causa --}}
      <div class="form-field">
        <label class="lbl">Valor da Causa</label>
        <input wire:model="valor_causa" type="text" placeholder="0,00"
               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>

      {{-- 17. Valor em Risco --}}
      <div class="form-field">
        <label class="lbl">Valor em Risco</label>
        <input wire:model="valor_risco" type="text" placeholder="0,00"
               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px">
      </div>

      {{-- 18. Observações --}}
      <div class="form-field" style="grid-column:1/-1">
        <label class="lbl">Observações</label>
        <textarea wire:model="observacoes" rows="3"
                  style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:6px;font-size:13px;resize:vertical"></textarea>
      </div>

    </div>

    <div style="margin-top:20px;display:flex;gap:10px;justify-content:flex-end">
      <a href="{{ route('processos') }}" class="btn" style="background:var(--bg);color:var(--muted);border:1px solid var(--border)">
        Cancelar
      </a>
      <button wire:click="salvar" wire:loading.attr="disabled" class="btn btn-primary">
        <span wire:loading.remove>💾 Salvar Processo</span>
        <span wire:loading>Salvando...</span>
      </button>
    </div>
  </div>
</div>
