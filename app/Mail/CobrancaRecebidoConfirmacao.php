<?php

namespace App\Mail;

use App\Models\Mensalidade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CobrancaRecebidoConfirmacao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Mensalidade $mensalidade
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Confirmação de recebimento — {$this->mensalidade->competenciaFormatada}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cobranca.recebido',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
