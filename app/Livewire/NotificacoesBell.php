<?php

namespace App\Livewire;

use App\Models\Notificacao;
use Illuminate\Support\Facades\Auth;
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
                $q->where('usuario_id', Auth::id())
                  ->orWhereNull('usuario_id');
            })
            ->update(['lida' => true]);
    }

    public function marcarTodasLidas(): void
    {
        $usuarioId = Auth::id();

        // Marca as individuais do usuário
        Notificacao::where('usuario_id', $usuarioId)
            ->where('lida', false)
            ->update(['lida' => true]);

        // Marca as globais (usuario_id null) criando cópia marcada como lida
        // Solução simples: atualiza globais também (elas ficam lidas para todos)
        Notificacao::whereNull('usuario_id')
            ->where('lida', false)
            ->update(['lida' => true]);
    }

    public function render(): \Illuminate\View\View
    {
        $usuarioId = Auth::id();

        $notificacoes = Notificacao::paraUsuario($usuarioId)
            ->orderBy('lida')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $naoLidas = $notificacoes->where('lida', false)->count();

        return view('livewire.notificacoes-bell', compact('notificacoes', 'naoLidas'));
    }
}
