<?php

namespace App\Livewire;

use App\Models\Comunicado;
use Livewire\Component;

class ComunicadoModal extends Component
{
    public array  $modais      = [];
    public int    $indice      = 0;
    public bool   $visivel     = false;

    public function mount(): void
    {
        $usuario = auth('usuarios')->user();
        if (!$usuario) return;

        $tenant = app()->bound('tenant') ? app('tenant') : \App\Models\Tenant::find($usuario->tenant_id);

        $query = Comunicado::ativos()
            ->prioridade('modal')
            ->orderByDesc('data_inicio');

        if ($tenant) {
            $query->paraTenant($tenant->id, $tenant->plano);
        }

        $this->modais = $query->get()
            ->reject(fn($c) => $c->foiLidoPor($usuario->id))
            ->map(fn($c) => [
                'id'       => $c->id,
                'titulo'   => $c->titulo,
                'mensagem' => $c->mensagem,
                'info'     => $c->tipoInfo(),
            ])
            ->values()
            ->toArray();

        $this->visivel = count($this->modais) > 0;
        $this->indice  = 0;
    }

    public function entendido(): void
    {
        $usuario = auth('usuarios')->user();
        $modal   = $this->modais[$this->indice] ?? null;
        if (!$modal || !$usuario) return;

        $c = Comunicado::find($modal['id']);
        $c?->marcarLidoPor($usuario->id, $usuario->tenant_id ?? 0);

        if ($this->indice + 1 < count($this->modais)) {
            $this->indice++;
        } else {
            $this->visivel = false;
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.comunicado-modal');
    }
}
