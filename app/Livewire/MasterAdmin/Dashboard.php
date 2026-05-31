<?php

namespace App\Livewire\MasterAdmin;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public array $stats          = [];
    public array $chartTenants   = [];
    public array $chartProcessos = [];

    public function mount(): void
    {
        $this->carregarStats();
        $this->carregarCharts();
    }

    private function carregarStats(): void
    {
        $diskBytes = $this->calcularDisco(storage_path('app/public'));

        $this->stats = [
            'ativos'      => Tenant::where('ativo', true)->count(),
            'inativos'    => Tenant::where('ativo', false)->count(),
            'usuarios'    => DB::table('usuarios')->count(),
            'processos'   => DB::table('processos')->whereNull('deleted_at')->count(),
            'disco_mb'    => round($diskBytes / 1024 / 1024, 1),
            'novos_30d'   => Tenant::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }

    private function carregarCharts(): void
    {
        // Ãšltimos 12 meses
        $meses = collect();
        for ($i = 11; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        // Tenants por mÃªs
        $rawTenants = DB::table('tenants')
            ->selectRaw("to_char(created_at, 'YYYY-MM') as mes, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Processos por mÃªs
        $rawProcessos = DB::table('processos')
            ->selectRaw("to_char(created_at, 'YYYY-MM') as mes, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->whereNull('deleted_at')
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $labels = $meses->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->isoFormat('MMM/YY'))->toArray();

        $this->chartTenants = [
            'labels' => $labels,
            'data'   => $meses->map(fn($m) => (int) ($rawTenants[$m] ?? 0))->toArray(),
        ];

        $this->chartProcessos = [
            'labels' => $labels,
            'data'   => $meses->map(fn($m) => (int) ($rawProcessos[$m] ?? 0))->toArray(),
        ];
    }

    private function calcularDisco(string $path): int
    {
        if (!is_dir($path)) return 0;
        $bytes = 0;
        try {
            $it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS));
            foreach ($it as $file) {
                if ($file->isFile()) $bytes += $file->getSize();
            }
        } catch (\Exception) {}
        return $bytes;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.master-admin.dashboard')
            ->extends('layouts.master')
            ->section('content');
    }
}

