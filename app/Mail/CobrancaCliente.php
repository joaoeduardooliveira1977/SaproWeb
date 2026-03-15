<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CobrancaCliente extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $clienteNome,
        public readonly string $clienteEmail,
        public readonly array  $parcelas,   // [['numero'=>1,'vencimento'=>'...','valor'=>100,'dias'=>5], ...]
        public readonly float  $totalDevido,
        public readonly string $escritorioNome,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            to:      [$this->clienteEmail],
            subject: "Aviso de honorários em aberto — {$this->escritorioNome}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cobranca-cliente',
            with: [
                'clienteNome'    => $this->clienteNome,
                'parcelas'       => $this->parcelas,
                'totalDevido'    => $this->totalDevido,
                'escritorioNome' => $this->escritorioNome,
                'dataEnvio'      => now()->format('d/m/Y'),
            ],
        );
    }
}
