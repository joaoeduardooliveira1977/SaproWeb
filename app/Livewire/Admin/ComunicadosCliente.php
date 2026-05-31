<?php

namespace App\Livewire\Admin;

use App\Models\Comunicado;
use Livewire\Component;

class ComunicadosCliente extends Component
{
    public string $filtroTipo   = '';
    public string $filtroLeitura = '';

    public function marcarLido(int $id): void
    {
        $usuario = auth('usuarios')->user();
        $c = Comunicado::find($id);
        $c?->marcarLidoPor($usuario->id, $usuario->tenant_id ?? 0);
    }

    public function render(): \Illuminate\View\View
    {
        $usuario = auth('usuarios')->user();
        $tenant  = app()->bound('tenant') ? app('tenant') : \App\Models\Tenant::find($usuario->tenant_id);

        $query = Comunicado::ativos()
            ->paraTenant($tenant->id, $tenant->plano)
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->orderByDesc('data_inicio');

        $comunicados = $query->get()->map(function ($c) use ($usuario) {
            $lido = $c->foiLidoPor($usuario->id);

            if ($this->filtroLeitura === 'lido'    && !$lido) return null;
            if ($this->filtroLeitura === 'nao_lido' && $lido) return null;

            return [
                'id'       => $c->id,
                'titulo'   => $c->titulo,
                'mensagem' => $c->mensagem,
                'tipo'     => $c->tipo,
                'info'     => $c->tipoInfo(),
                'data'     => $c->data_inicio,
                'lido'     => $lido,
            ];
        })->filter()->values();

        return view('livewire.admin.comunicados-cliente', compact('comunicados'))
            ->extends('layouts.app')
            ->section('content');
    }
}
