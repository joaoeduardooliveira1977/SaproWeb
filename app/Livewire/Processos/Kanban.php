<?php

namespace App\Livewire\Processos;

use App\Models\{Fase, Pessoa, Processo};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Kanban extends Component
{
    public string $busca          = '';
    public string $filtroAdvogado = '';

    protected $queryString = [
        'busca'          => ['except' => ''],
        'filtroAdvogado' => ['except' => ''],
    ];

    // ── Mover processo entre fases (drag-and-drop) ────────────

    public function moverProcesso(int $processoId, ?int $faseId): void
    {
        $faseAnteriorId = DB::table('processos')
            ->where('id', $processoId)
            ->value('fase_id');

        if ($faseAnteriorId === $faseId) return;

        DB::table('processos')
            ->where('id', $processoId)
            ->update(['fase_id' => $faseId, 'updated_at' => now()]);

        DB::table('processo_fases_historico')->insert([
            'processo_id'      => $processoId,
            'fase_anterior_id' => $faseAnteriorId,
            'fase_nova_id'     => $faseId,
            'usuario_id'       => Auth::id(),
            'created_at'       => now(),
        ]);

        $this->dispatch('toast', message: 'Fase atualizada.', type: 'success');
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $fases = Fase::orderBy('ordem')->orderBy('descricao')->get();

        $processos = Processo::with(['cliente', 'fase', 'advogados', 'risco'])
            ->where('status', 'Ativo')
            ->when($this->busca, fn($q) => $q->where(fn($sub) => $sub
                ->where('numero', 'ilike', "%{$this->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"))
            ))
            ->when($this->filtroAdvogado, fn($q) => $q->whereHas('advogados', fn($a) =>
                $a->where('pessoas.id', $this->filtroAdvogado)
            ))
            ->get();

        // Agrupar por fase_id (null = sem fase)
        $kanban = [];
        $kanban['sem_fase'] = $processos->whereNull('fase_id')->values();
        foreach ($fases as $fase) {
            $kanban[$fase->id] = $processos->where('fase_id', $fase->id)->values();
        }

        $advogados      = Pessoa::doTipo('Advogado')->orderBy('nome')->get();
        $totalProcessos = $processos->count();

        return view('livewire.processos.kanban', compact(
            'fases', 'kanban', 'advogados', 'totalProcessos'
        ))->extends('layouts.app')->section('content');
    }
}
