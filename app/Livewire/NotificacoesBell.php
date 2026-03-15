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
        Notificacao::where('id', $id)
            ->where(function ($q) {
                $q->where('usuario_id', auth('usuarios')->id())
                  ->orWhereNull('usuario_id');
            })
            ->update(['lida' => true]);
    }

    public function marcarTodasLidas(): void
    {
        $usuarioId = auth('usuarios')->id();

        Notificacao::where('usuario_id', $usuarioId)
            ->where('lida', false)
            ->update(['lida' => true]);

        Notificacao::whereNull('usuario_id')
            ->where('lida', false)
            ->update(['lida' => true]);
    }

    public function render(): \Illuminate\View\View
    {
        $usuarioId = auth('usuarios')->id();

        $notificacoes = Notificacao::paraUsuario($usuarioId)
            ->orderBy('lida')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $naoLidas = $notificacoes->where('lida', false)->count();

        return view('livewire.notificacoes-bell', compact('notificacoes', 'naoLidas'));
    }
}
