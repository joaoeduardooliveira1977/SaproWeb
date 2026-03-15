<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class Auditoria extends Component
{
    use WithPagination;

    public string $filtroUsuario = '';
    public string $filtroAcao    = '';
    public string $filtroTabela  = '';
    public string $filtroDataIni = '';
    public string $filtroDataFim = '';

    public ?int $detalheId = null;

    protected $queryString = ['filtroUsuario', 'filtroAcao', 'filtroTabela'];

    public function updatingFiltroUsuario(): void { $this->resetPage(); }
    public function updatingFiltroAcao(): void    { $this->resetPage(); }
    public function updatingFiltroTabela(): void  { $this->resetPage(); }
    public function updatingFiltroDataIni(): void { $this->resetPage(); }
    public function updatingFiltroDataFim(): void { $this->resetPage(); }

    public function limpar(): void
    {
        $this->filtroUsuario = '';
        $this->filtroAcao    = '';
        $this->filtroTabela  = '';
        $this->filtroDataIni = '';
        $this->filtroDataFim = '';
        $this->resetPage();
    }

    public function verDetalhe(int $id): void
    {
        $this->detalheId = $id;
    }

    public function fecharDetalhe(): void
    {
        $this->detalheId = null;
    }

    public function render(): \Illuminate\View\View
    {
        $query = DB::table('auditorias as a')
            ->leftJoin('usuarios as u', 'u.id', '=', 'a.usuario_id')
            ->select('a.*')
            ->orderByDesc('a.created_at');

        if ($this->filtroUsuario) {
            $query->where('a.login', 'ilike', '%' . $this->filtroUsuario . '%');
        }
        if ($this->filtroAcao) {
            $query->where('a.acao', 'ilike', '%' . $this->filtroAcao . '%');
        }
        if ($this->filtroTabela) {
            $query->where('a.tabela', $this->filtroTabela);
        }
        if ($this->filtroDataIni) {
            $query->whereDate('a.created_at', '>=', $this->filtroDataIni);
        }
        if ($this->filtroDataFim) {
            $query->whereDate('a.created_at', '<=', $this->filtroDataFim);
        }

        $registros = $query->paginate(25);

        // Opções para filtros
        $tabelas = DB::table('auditorias')
            ->selectRaw('tabela, COUNT(*) as total')
            ->whereNotNull('tabela')
            ->groupBy('tabela')
            ->orderByDesc('total')
            ->pluck('tabela');

        $detalhe = $this->detalheId
            ? DB::table('auditorias')->where('id', $this->detalheId)->first()
            : null;

        return view('livewire.auditoria', compact('registros', 'tabelas', 'detalhe'));
    }
}
