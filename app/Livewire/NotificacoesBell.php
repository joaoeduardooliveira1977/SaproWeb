<?php

namespace App\Livewire;

use App\Models\Notificacao;
use Livewire\Component;

class NotificacoesBell extends Component
{
    public bool $aberto = false;

    public function toggle(): void
    {
        $this->aberto = !$this->aberto;
    }

    public function fechar(): void
    {
        $this->aberto = false;
    }

    public function marcarLida(int $id): void
    {
        $usuarioId = auth('usuarios')->id();

        Notificacao::where('id', $id)
            ->where(function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId)
                  ->orWhereNull('usuario_id');
            })
            ->update(['lida' => true]);
    }

    public function marcarTodasLidas(): void
    {
        $usuarioId = auth('usuarios')->id();

        Notificacao::where(function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId)
                  ->orWhereNull('usuario_id');
            })
            ->where('lida', false)
            ->update(['lida' => true]);
    }

    public function render(): \Illuminate\View\View
    {
        $usuarioId = auth('usuarios')->id();

        $notificacoes = Notificacao::where(function ($q) use ($usuarioId) {
                $q->where('usuario_id', $usuarioId)
                  ->orWhereNull('usuario_id');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $naoLidas = $notificacoes->where('lida', false)->count();

        return view('livewire.notificacoes-bell', compact('notificacoes', 'naoLidas'));
    }
}