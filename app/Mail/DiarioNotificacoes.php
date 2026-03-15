<?php

namespace App\Mail;

use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DiarioNotificacoes extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Usuario    $usuario,
        public readonly Collection $notificacoes,
    ) {}

    public function envelope(): Envelope
    {
        $total   = $this->notificacoes->count();
        $dataFmt = now()->format('d/m/Y');

        return new Envelope(
            subject: "🔔 {$total} notificação(ões) — {$dataFmt}",
        );
    }

    public function content(): Content
    {
        $ordemTipo = [
            'prazo_fatal'            => 0,
            'prazo_vencido'          => 1,
            'prazo_vencendo'         => 2,
            'honorario_atrasado'     => 3,
            'processo_sem_andamento' => 4,
        ];

        $grupos = $this->notificacoes
            ->sortBy(fn($n) => $ordemTipo[$n->tipo] ?? 99)
            ->groupBy('tipo');

        return new Content(
            view: 'emails.diario-notificacoes',
            with: [
                'usuario'  => $this->usuario,
                'grupos'   => $grupos,
                'total'    => $this->notificacoes->count(),
                'fatais'   => $this->notificacoes->where('tipo', 'prazo_fatal')->count(),
                'vencidos' => $this->notificacoes->where('tipo', 'prazo_vencido')->count(),
                'vencendo' => $this->notificacoes->where('tipo', 'prazo_vencendo')->count(),
                'geradoEm' => now()->format('d/m/Y H:i'),
                'dataFmt'  => now()->format('d/m/Y'),
            ],
        );
    }
}
