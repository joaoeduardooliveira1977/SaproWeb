<?php

namespace App\Mail;

use App\Models\Pessoa;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PortalNovaMensagem extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Pessoa $cliente,
        public readonly string $mensagem,
        public readonly string $remetente = 'Escritório',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '💬 Nova mensagem no seu portal — ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.portal-nova-mensagem',
            with: [
                'clienteNome'   => $this->cliente->nome,
                'mensagem'      => $this->mensagem,
                'remetente'     => $this->remetente,
                'portalUrl'     => route('portal.login'),
                'escritorioNome'=> config('app.name'),
                'dataEnvio'     => now()->format('d/m/Y H:i'),
            ],
        );
    }
}
