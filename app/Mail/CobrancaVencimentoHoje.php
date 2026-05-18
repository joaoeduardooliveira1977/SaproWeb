<?php

namespace App\Mail;

use App\Models\Mensalidade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CobrancaVencimentoHoje extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Mensalidade $mensalidade
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Vencimento hoje — {$this->mensalidade->competenciaFormatada}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cobranca.vencimento-hoje',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
