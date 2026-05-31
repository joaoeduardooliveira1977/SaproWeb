<?php

namespace App\Livewire\MasterAdmin;

use App\Models\MasterAdminLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Logs extends Component
{
    use WithPagination;

    public string $busca          = '';
    public string $filtroAcao     = '';
    public string $filtroContexto = '';
    public string $dataInicio     = '';
    public string $dataFim        = '';
    public string $filtroTenant   = '';

    public function updatingBusca(): void          { $this->resetPage(); }
    public function updatingFiltroAcao(): void     { $this->resetPage(); }
    public function updatingFiltroContexto(): void { $this->resetPage(); }
    public function updatingDataInicio(): void     { $this->resetPage(); }
    public function updatingDataFim(): void        { $this->resetPage(); }

    public function exportarCsv(): StreamedResponse
    {
        $logs = $this->buildQuery()->get();

        return response()->streamDownload(function () use ($logs) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($handle, ['Data/Hora', 'Usuário', 'Ação', 'Contexto', 'Tenant', 'Detalhes', 'IP', 'User Agent'], ';');
            foreach ($logs as $log) {
                fputcsv($handle, [
                    \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s'),
                    $log->admin_nome,
                    $log->acao,
                    $log->contexto ?? '',
                    $log->tenant_nome ?? '',
                    $log->detalhes ?? '',
                    $log->ip ?? '',
                    $log->user_agent ?? '',
                ], ';');
            }
            fclose($handle);
        }, 'master-logs-' . now()->format('Ymd-His') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function buildQuery()
    {
        return MasterAdminLog::query()
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('admin_nome',  'ilike', "%{$this->busca}%")
                  ->orWhere('tenant_nome','ilike', "%{$this->busca}%")
                  ->orWhere('detalhes',   'ilike', "%{$this->busca}%")
                  ->orWhere('ip',         'ilike', "%{$this->busca}%");
            }))
            ->when($this->filtroAcao,     fn($q) => $q->where('acao', $this->filtroAcao))
            ->when($this->filtroContexto, fn($q) => $q->where('contexto', $this->filtroContexto))
            ->when($this->filtroTenant,   fn($q) => $q->where('tenant_nome', 'ilike', "%{$this->filtroTenant}%"))
            ->when($this->dataInicio,     fn($q) => $q->where('created_at', '>=', $this->dataInicio))
            ->when($this->dataFim,        fn($q) => $q->where('created_at', '<=', $this->dataFim . ' 23:59:59'))
            ->orderByDesc('created_at');
    }

    public function render(): \Illuminate\View\View
    {
        $logs  = $this->buildQuery()->paginate(40);
        $acoes = MasterAdminLog::selectRaw('distinct acao')->orderBy('acao')->pluck('acao');

        return view('livewire.master-admin.logs', compact('logs', 'acoes'))
            ->extends('layouts.master')
            ->section('content');
    }
}
