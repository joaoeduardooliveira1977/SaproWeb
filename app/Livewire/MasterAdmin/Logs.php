<?php

namespace App\Livewire\MasterAdmin;

use App\Models\MasterAdminLog;
use Livewire\Component;
use Livewire\WithPagination;

class Logs extends Component
{
    use WithPagination;

    public string $filtroAcao  = '';
    public string $busca       = '';

    public function updatingBusca(): void     { $this->resetPage(); }
    public function updatingFiltroAcao(): void { $this->resetPage(); }

    public function render(): \Illuminate\View\View
    {
        $logs = MasterAdminLog::query()
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('admin_nome',  'ilike', "%{$this->busca}%")
                  ->orWhere('tenant_nome','ilike', "%{$this->busca}%")
                  ->orWhere('detalhes',   'ilike', "%{$this->busca}%");
            }))
            ->when($this->filtroAcao, fn($q) => $q->where('acao', $this->filtroAcao))
            ->orderByDesc('created_at')
            ->paginate(30);

        $acoes = MasterAdminLog::selectRaw('distinct acao')->orderBy('acao')->pluck('acao');

        return view('livewire.master-admin.logs', compact('logs', 'acoes'))
            ->extends('layouts.master')
            ->section('content');
    }
}

