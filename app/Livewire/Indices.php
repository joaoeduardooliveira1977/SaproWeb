<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Indices extends Component
{
    public bool   $atualizando = false;
    public string $siglaFiltro = '';

    public function atualizarTodos(): void
    {
        try {
            Artisan::call('indices:atualizar', ['--sigla' => $this->siglaFiltro ?: null]);
            $output = Artisan::output();
            $this->dispatch('toast', message: 'Atualização concluída. ' . trim(strip_tags($output)), type: 'success');
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Erro: ' . $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $resumo = DB::table('indices_monetarios')
            ->selectRaw("sigla, nome, COUNT(*) as total_meses, MIN(mes_ref) as de, MAX(mes_ref) as ate, MAX(updated_at) as atualizado_em")
            ->groupBy('sigla', 'nome')
            ->orderBy('sigla')
            ->get();

        // Últimos 12 meses de cada índice
        $ultimos = DB::table('indices_monetarios')
            ->whereIn('sigla', ['IPCA', 'IGPM', 'SELIC', 'TR'])
            ->where('mes_ref', '>=', now()->subMonths(12)->startOfMonth())
            ->orderBy('mes_ref')
            ->get()
            ->groupBy('sigla');

        return view('livewire.indices', compact('resumo', 'ultimos'));
    }
}
