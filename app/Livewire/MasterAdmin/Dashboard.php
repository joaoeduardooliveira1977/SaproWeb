<?php

namespace App\Livewire\MasterAdmin;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public array $stats          = [];
    public array $financeiro     = [];
    public array $chartTenants   = [];
    public array $chartProcessos = [];
    public array $chartMrr       = [];

    // Receita mensal estimada por plano (R$)
    const PRECOS = [
        'demo'       => 0,
        'starter'    => 150,
        'pro'        => 350,
        'enterprise' => 700,
    ];

    public function mount(): void
    {
        $this->carregarStats();
        $this->carregarFinanceiro();
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

    private function carregarFinanceiro(): void
    {
        $porPlano = Tenant::where('ativo', true)
            ->selectRaw('plano, count(*) as total')
            ->groupBy('plano')
            ->pluck('total', 'plano');

        $mrr = 0;
        $detalhes = [];
        foreach (self::PRECOS as $plano => $preco) {
            $qtd      = (int) ($porPlano[$plano] ?? 0);
            $subtotal = $qtd * $preco;
            $mrr     += $subtotal;
            $detalhes[] = [
                'plano'    => ucfirst($plano),
                'qtd'      => $qtd,
                'preco'    => $preco,
                'subtotal' => $subtotal,
            ];
        }

        $totalPagos = Tenant::where('ativo', true)->whereNotIn('plano', ['demo'])->count();
        $totalAtivos = Tenant::where('ativo', true)->count();

        $this->financeiro = [
            'mrr'         => $mrr,
            'arr'         => $mrr * 12,
            'conversao'   => $totalAtivos ? round(($totalPagos / $totalAtivos) * 100, 1) : 0,
            'ticket_medio'=> $totalPagos ? round($mrr / $totalPagos, 2) : 0,
            'detalhes'    => $detalhes,
        ];

        // Gráfico de MRR: últimos 12 meses (estimativa por plano ativo na época)
        $meses = collect();
        for ($i = 11; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        $mrrPorMes = [];
        foreach ($meses as $mes) {
            $fim   = \Carbon\Carbon::createFromFormat('Y-m', $mes)->endOfMonth();
            $total = 0;
            foreach (self::PRECOS as $plano => $preco) {
                if ($preco === 0) continue;
                $qtd = DB::table('tenants')
                    ->where('plano', $plano)
                    ->where('ativo', true)
                    ->where('created_at', '<=', $fim)
                    ->count();
                $total += $qtd * $preco;
            }
            $mrrPorMes[$mes] = $total;
        }

        $labels = $meses->map(fn($m) => \Carbon\Carbon::createFromFormat('Y-m', $m)->isoFormat('MMM/YY'))->toArray();
        $this->chartMrr = [
            'labels' => $labels,
            'data'   => array_values($mrrPorMes),
        ];
    }

    private function carregarCharts(): void
    {
        // Últimos 12 meses
        $meses = collect();
        for ($i = 11; $i >= 0; $i--) {
            $meses->push(now()->subMonths($i)->format('Y-m'));
        }

        // Tenants por mês
        $rawTenants = DB::table('tenants')
            ->selectRaw("to_char(created_at, 'YYYY-MM') as mes, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(12)->startOfMonth())
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        // Processos por mês
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
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
