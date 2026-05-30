<?php

namespace App\Livewire\MasterAdmin;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class Infra extends Component
{
    public array  $servidor    = [];
    public array  $fila        = [];
    public array  $falhas      = [];
    public string $logConteudo = '';

    public function mount(): void
    {
        $this->atualizar();
    }

    public function atualizar(): void
    {
        $this->carregarServidor();
        $this->carregarFila();
        $this->carregarLog();
    }

    private function carregarServidor(): void
    {
        // CPU — via /proc/loadavg (Linux)
        $cpuLoad    = 0;
        $cpuPercent = 0;
        if (@file_exists('/proc/loadavg')) {
            $raw     = explode(' ', file_get_contents('/proc/loadavg'));
            $cpuLoad = (float) ($raw[0] ?? 0);
            $cores   = (int) @shell_exec('nproc 2>/dev/null') ?: 1;
            $cpuPercent = min(100, round(($cpuLoad / $cores) * 100, 1));
        }

        // RAM — via /proc/meminfo (Linux)
        $ramTotal = $ramUsed = $ramPercent = 0;
        if (@file_exists('/proc/meminfo')) {
            $info = [];
            foreach (@file('/proc/meminfo') ?: [] as $line) {
                if (preg_match('/^(\w+):\s+(\d+)/', $line, $m)) {
                    $info[$m[1]] = (int) $m[2];
                }
            }
            $ramTotal   = $info['MemTotal'] ?? 0;
            $ramFree    = $info['MemAvailable'] ?? $info['MemFree'] ?? 0;
            $ramUsed    = $ramTotal - $ramFree;
            $ramPercent = $ramTotal ? round(($ramUsed / $ramTotal) * 100, 1) : 0;
        }

        // Disco
        $diskTotal = @disk_total_space('/') ?: 0;
        $diskFree  = @disk_free_space('/')  ?: 0;
        $diskUsed  = $diskTotal - $diskFree;
        $diskPct   = $diskTotal ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

        // Uptime
        $uptime = trim(@shell_exec('uptime -p 2>/dev/null') ?: (@shell_exec('uptime 2>/dev/null') ?: 'N/A'));

        $this->servidor = [
            'cpu_percent'  => $cpuPercent,
            'cpu_load'     => $cpuLoad,
            'ram_total_mb' => round($ramTotal / 1024),
            'ram_used_mb'  => round($ramUsed  / 1024),
            'ram_percent'  => $ramPercent,
            'disk_total_gb'=> round($diskTotal / 1024 / 1024 / 1024, 1),
            'disk_used_gb' => round($diskUsed  / 1024 / 1024 / 1024, 1),
            'disk_percent' => $diskPct,
            'uptime'       => $uptime,
        ];
    }

    private function carregarFila(): void
    {
        try {
            $pendentes = DB::table('jobs')->count();
        } catch (\Exception) {
            $pendentes = '—';
        }

        try {
            $falhasTotal = DB::table('failed_jobs')->count();
        } catch (\Exception) {
            $falhasTotal = 0;
        }

        $this->fila = [
            'pendentes'    => $pendentes,
            'falhas_total' => $falhasTotal,
        ];

        try {
            $this->falhas = DB::table('failed_jobs')
                ->orderByDesc('failed_at')
                ->limit(5)
                ->get(['id', 'queue', 'payload', 'exception', 'failed_at'])
                ->map(function ($j) {
                    $payload = json_decode($j->payload, true);
                    return [
                        'id'         => $j->id,
                        'queue'      => $j->queue,
                        'job'        => $payload['displayName'] ?? $payload['job'] ?? 'Desconhecido',
                        'erro'       => Str_limit($j->exception ?? '', 200),
                        'failed_at'  => $j->failed_at,
                    ];
                })
                ->toArray();
        } catch (\Exception) {
            $this->falhas = [];
        }
    }

    private function carregarLog(): void
    {
        $logPath = storage_path('logs/laravel.log');
        if (!file_exists($logPath)) {
            $this->logConteudo = 'Arquivo de log não encontrado.';
            return;
        }

        // Lê as últimas 20 linhas
        try {
            $file  = new \SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $total = $file->key();
            $start = max(0, $total - 20);
            $lines = [];
            $file->seek($start);
            while (!$file->eof()) {
                $lines[] = rtrim($file->current());
                $file->next();
            }
            $this->logConteudo = implode("\n", array_filter($lines));
        } catch (\Exception) {
            $this->logConteudo = 'Erro ao ler o log.';
        }
    }

    public function limparLog(): void
    {
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
        }
        $this->logConteudo = '';
        $this->dispatch('toast', message: 'Log limpo com sucesso.', type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.master-admin.infra')
            ->extends('layouts.master-admin')
            ->section('content');
    }
}
