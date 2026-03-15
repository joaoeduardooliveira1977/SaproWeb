<?php

namespace App\Mail;

use App\Models\Prazo;
use App\Models\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrazoLembrete extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Prazo   $prazo,
        public readonly Usuario $responsavel,
    ) {}

    public function envelope(): Envelope
    {
        $prefixo = $this->prazo->prazo_fatal ? '🚨 PRAZO FATAL' : '⏳ Lembrete de Prazo';
        $dias    = $this->prazo->diasRestantes();

        $assunto = $dias === 0
            ? "{$prefixo}: {$this->prazo->titulo} — vence HOJE"
            : "{$prefixo}: {$this->prazo->titulo} — {$dias} dia(s)";

        return new Envelope(subject: $assunto);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prazo-lembrete',
            with: [
                'prazo'       => $this->prazo,
                'responsavel' => $this->responsavel,
                'dias'        => $this->prazo->diasRestantes(),
                'geradoEm'    => now()->format('d/m/Y H:i'),
            ],
        );
    }
}
