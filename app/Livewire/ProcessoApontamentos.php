<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\{Auth, DB};

class ProcessoApontamentos extends Component
{
    public int $processoId;

    // Timer
    public bool $timerAtivo  = false;
    public ?int $timerInicio = null;   // Unix timestamp

    // Modal salvar / editar
    public bool   $modalAberto = false;
    public ?int   $editandoId  = null;
    public string $descricao   = '';
    public string $horas       = '';
    public string $valor       = '';
    public string $data        = '';
    public string $advogado_id = '';

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
        $this->data       = today()->format('Y-m-d');
    }

    // ── Timer ──────────────────────────────────────────────────

    public function iniciarTimer(): void
    {
        $this->timerAtivo  = true;
        $this->timerInicio = now()->timestamp;
    }

    public function pararTimer(int $segundos): void
    {
        $this->timerAtivo = false;
        $horas = max(0.01, round($segundos / 3600, 2));
        $this->horas      = number_format($horas, 2, '.', '');
        $this->descricao  = '';
        $this->valor      = '';
        $this->data       = today()->format('Y-m-d');
        $this->editandoId = null;
        $this->modalAberto = true;
        $this->resetErrorBag();
    }

    // ── CRUD manual ────────────────────────────────────────────

    public function novoManual(): void
    {
        $this->editandoId  = null;
        $this->descricao   = '';
        $this->horas       = '';
        $this->valor       = '';
        $this->data        = today()->format('Y-m-d');
        $this->modalAberto = true;
        $this->resetErrorBag();
    }

    public function editar(int $id): void
    {
        $row = DB::selectOne(
            'SELECT * FROM apontamentos WHERE id = ? AND processo_id = ?',
            [$id, $this->processoId]
        );
        if (! $row) return;

        $this->editandoId  = $id;
        $this->descricao   = $row->descricao;
        $this->horas       = $row->horas;
        $this->valor       = $row->valor ?? '';
        $this->data        = substr($row->data, 0, 10);
        $this->advogado_id = (string) ($row->advogado_id ?? '');
        $this->modalAberto = true;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $this->validate([
            'descricao' => 'required|min:3',
            'horas'     => 'required|numeric|min:0.01',
            'data'      => 'required|date',
        ]);

        $dados = [
            'processo_id'  => $this->processoId,
            'descricao'    => trim($this->descricao),
            'horas'        => (float) $this->horas,
            'valor'        => $this->valor !== '' ? (float) $this->valor : null,
            'data'         => $this->data,
            'advogado_id'  => $this->advogado_id ?: null,
            'usuario_id'   => Auth::guard('usuarios')->id(),
        ];

        if ($this->editandoId) {
            DB::update(
                'UPDATE apontamentos SET descricao=?, horas=?, valor=?, data=?, advogado_id=?, updated_at=NOW()
                 WHERE id=? AND processo_id=?',
                [$dados['descricao'], $dados['horas'], $dados['valor'],
                 $dados['data'], $dados['advogado_id'],
                 $this->editandoId, $this->processoId]
            );
        } else {
            DB::insert(
                'INSERT INTO apontamentos (processo_id,descricao,horas,valor,data,advogado_id,usuario_id,created_at,updated_at)
                 VALUES (?,?,?,?,?,?,?,NOW(),NOW())',
                [$dados['processo_id'], $dados['descricao'], $dados['horas'], $dados['valor'],
                 $dados['data'], $dados['advogado_id'], $dados['usuario_id']]
            );
        }

        $this->modalAberto = false;
        $this->editandoId  = null;
    }

    public function excluir(int $id): void
    {
        DB::delete('DELETE FROM apontamentos WHERE id=? AND processo_id=?', [$id, $this->processoId]);
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->editandoId  = null;
        $this->resetErrorBag();
    }

    // ── Render ─────────────────────────────────────────────────

    public function render()
    {
        $apontamentos = DB::select(
            'SELECT a.*, p.nome AS advogado_nome
             FROM   apontamentos a
             LEFT   JOIN pessoas p ON p.id = a.advogado_id
             WHERE  a.processo_id = ?
             ORDER  BY a.data DESC, a.id DESC',
            [$this->processoId]
        );

        $totalHoras = array_sum(array_column($apontamentos, 'horas'));
        $totalValor = array_sum(array_column($apontamentos, 'valor'));

        $advogados = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Advogado')
            ->where('p.ativo', true)
            ->where('p.tenant_id', tenant_id())
            ->orderBy('p.nome')
            ->select('p.id', 'p.nome')
            ->get();

        return view('livewire.processo-apontamentos', compact(
            'apontamentos', 'totalHoras', 'totalValor', 'advogados'
        ));
    }
}
