<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProcessoChecklist extends Component
{
    public int $processoId;

    // Formulário inline de nova tarefa
    public bool   $mostrarForm  = false;
    public string $novoTitulo   = '';
    public string $novaDataLimite = '';
    public int    $novaResponsavel = 0;

    // Edição inline
    public ?int   $editandoId   = null;
    public string $editTitulo   = '';
    public string $editDataLimite = '';
    public int    $editResponsavel = 0;

    // Dados auxiliares
    public array $usuarios = [];

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
        $this->usuarios   = DB::select(
            "SELECT u.id, p.nome FROM usuarios u JOIN pessoas p ON p.id = u.pessoa_id WHERE u.ativo = true ORDER BY p.nome"
        );
    }

    // ── Adicionar ─────────────────────────────────────────────────

    public function abrirForm(): void
    {
        $this->mostrarForm      = true;
        $this->novoTitulo       = '';
        $this->novaDataLimite   = '';
        $this->novaResponsavel  = 0;
    }

    public function salvarNova(): void
    {
        $this->validate(['novoTitulo' => 'required|min:3|max:300']);

        $ordem = (int) DB::selectOne(
            'SELECT COALESCE(MAX(ordem), 0) + 1 as prox FROM processo_tarefas WHERE processo_id = ?',
            [$this->processoId]
        )->prox;

        DB::insert(
            'INSERT INTO processo_tarefas (processo_id, titulo, responsavel_id, data_limite, ordem, criado_por, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $this->processoId,
                trim($this->novoTitulo),
                $this->novaResponsavel ?: null,
                $this->novaDataLimite ?: null,
                $ordem,
                Auth::guard('usuarios')->id(),
            ]
        );

        $this->mostrarForm    = false;
        $this->novoTitulo     = '';
        $this->novaDataLimite = '';
        $this->novaResponsavel = 0;
    }

    public function cancelarForm(): void
    {
        $this->mostrarForm = false;
        $this->novoTitulo  = '';
    }

    // ── Toggle concluída ──────────────────────────────────────────

    public function toggleConcluida(int $id): void
    {
        $tarefa = DB::selectOne('SELECT concluida FROM processo_tarefas WHERE id = ? AND processo_id = ?', [$id, $this->processoId]);
        if (! $tarefa) return;

        $nova = $tarefa->concluida ? false : true;
        DB::update(
            'UPDATE processo_tarefas SET concluida = ?, concluida_em = ?, updated_at = NOW() WHERE id = ?',
            [$nova, $nova ? now() : null, $id]
        );
    }

    // ── Editar ────────────────────────────────────────────────────

    public function abrirEdicao(int $id): void
    {
        $tarefa = DB::selectOne('SELECT * FROM processo_tarefas WHERE id = ? AND processo_id = ?', [$id, $this->processoId]);
        if (! $tarefa) return;

        $this->editandoId      = $id;
        $this->editTitulo      = $tarefa->titulo;
        $this->editDataLimite  = $tarefa->data_limite ?? '';
        $this->editResponsavel = $tarefa->responsavel_id ?? 0;
        $this->mostrarForm     = false;
    }

    public function salvarEdicao(): void
    {
        $this->validate(['editTitulo' => 'required|min:3|max:300']);

        DB::update(
            'UPDATE processo_tarefas SET titulo = ?, responsavel_id = ?, data_limite = ?, updated_at = NOW() WHERE id = ? AND processo_id = ?',
            [
                trim($this->editTitulo),
                $this->editResponsavel ?: null,
                $this->editDataLimite ?: null,
                $this->editandoId,
                $this->processoId,
            ]
        );

        $this->editandoId = null;
    }

    public function cancelarEdicao(): void
    {
        $this->editandoId = null;
    }

    // ── Excluir ───────────────────────────────────────────────────

    public function excluir(int $id): void
    {
        DB::delete('DELETE FROM processo_tarefas WHERE id = ? AND processo_id = ?', [$id, $this->processoId]);
    }

    // ── Render ────────────────────────────────────────────────────

    public function render()
    {
        $tarefas = DB::select(
            'SELECT pt.*, u.id as resp_id, p.nome as resp_nome
             FROM processo_tarefas pt
             LEFT JOIN usuarios u ON u.id = pt.responsavel_id
             LEFT JOIN pessoas p ON p.id = u.pessoa_id
             WHERE pt.processo_id = ?
             ORDER BY pt.concluida ASC, pt.ordem ASC, pt.id ASC',
            [$this->processoId]
        );

        $total     = count($tarefas);
        $concluidas = count(array_filter($tarefas, fn($t) => $t->concluida));
        $progresso  = $total > 0 ? round(($concluidas / $total) * 100) : 0;

        return view('livewire.processo-checklist', compact('tarefas', 'total', 'concluidas', 'progresso'));
    }
}
