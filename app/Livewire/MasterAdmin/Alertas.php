<?php

namespace App\Livewire\MasterAdmin;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Alertas extends Component
{
    public array $alertas = [];

    public function mount(): void
    {
        $this->carregarAlertas();
    }

    public function carregarAlertas(): void
    {
        $alertas = [];

        // Tenants suspensos
        $suspensos = Tenant::where('ativo', false)->get(['id', 'nome', 'slug', 'created_at']);
        foreach ($suspensos as $t) {
            $alertas[] = [
                'nivel'   => 'critico',
                'icone'   => '🔴',
                'titulo'  => "Tenant suspenso: {$t->nome}",
                'detalhe' => "Slug: {$t->slug} — suspenso manualmente.",
                'link'    => route('master.tenant-show', $t->id),
            ];
        }

        // Tenants na lixeira há mais de 30 dias
        $lixeiraAntiga = Tenant::onlyTrashed()
            ->where('deleted_at', '<', now()->subDays(30))
            ->get(['id', 'nome', 'slug', 'deleted_at']);
        foreach ($lixeiraAntiga as $t) {
            $alertas[] = [
                'nivel'   => 'aviso',
                'icone'   => '🗑️',
                'titulo'  => "Na lixeira há 30+ dias: {$t->nome}",
                'detalhe' => "Excluído em {$t->deleted_at->format('d/m/Y')}. Considere exclusão definitiva ou restauração.",
                'link'    => route('master.lixeira'),
            ];
        }

        // Jobs com falha na fila
        try {
            $falhas = DB::table('failed_jobs')->count();
            if ($falhas > 0) {
                $alertas[] = [
                    'nivel'   => 'critico',
                    'icone'   => '🔴',
                    'titulo'  => "{$falhas} job(s) com falha na fila",
                    'detalhe' => 'Verifique a aba Infraestrutura para detalhes.',
                    'link'    => route('master.infra'),
                ];
            }
        } catch (\Exception) {}

        // Tenants sem acesso há mais de 30 dias
        $semAcesso = Tenant::where('ativo', true)
            ->withMax('usuarios', 'ultimo_acesso')
            ->get(['id', 'nome', 'slug']);

        foreach ($semAcesso as $t) {
            $ultimoAcesso = $t->usuarios_max_ultimo_acesso;
            if (!$ultimoAcesso || \Carbon\Carbon::parse($ultimoAcesso)->lt(now()->subDays(30))) {
                $alertas[] = [
                    'nivel'   => 'aviso',
                    'icone'   => '🟡',
                    'titulo'  => "Sem acesso há 30+ dias: {$t->nome}",
                    'detalhe' => 'Último acesso: ' . ($ultimoAcesso ? \Carbon\Carbon::parse($ultimoAcesso)->diffForHumans() : 'nunca'),
                    'link'    => route('master.tenant-show', $t->id),
                ];
            }
        }

        // Tenants no plano Demo há mais de 15 dias
        $demoAntigos = Tenant::where('plano', 'demo')
            ->where('ativo', true)
            ->where('created_at', '<', now()->subDays(15))
            ->get(['id', 'nome', 'slug', 'created_at']);

        foreach ($demoAntigos as $t) {
            $alertas[] = [
                'nivel'   => 'aviso',
                'icone'   => '🟡',
                'titulo'  => "Demo há mais de 15 dias: {$t->nome}",
                'detalhe' => 'Cadastrado em ' . $t->created_at->format('d/m/Y') . '. Considere contato comercial.',
                'link'    => route('master.tenant-show', $t->id),
            ];
        }

        // Disco acima de 80%
        $diskTotal = @disk_total_space('/') ?: 0;
        $diskFree  = @disk_free_space('/') ?: 0;
        if ($diskTotal > 0) {
            $diskPct = round((($diskTotal - $diskFree) / $diskTotal) * 100, 1);
            if ($diskPct > 80) {
                $alertas[] = [
                    'nivel'   => 'aviso',
                    'icone'   => '🟡',
                    'titulo'  => "Disco acima de 80% ({$diskPct}%)",
                    'detalhe' => 'Considere limpeza de logs ou expansão de armazenamento.',
                    'link'    => route('master.infra'),
                ];
            }
        }

        // Ordena: crítico primeiro
        usort($alertas, fn($a, $b) => ($a['nivel'] === 'critico' ? 0 : 1) - ($b['nivel'] === 'critico' ? 0 : 1));

        $this->alertas = $alertas;
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.master-admin.alertas')
            ->extends('layouts.master')
            ->section('content');
    }
}
