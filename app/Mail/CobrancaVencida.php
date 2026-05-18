<?php

namespace App\Mail;

use App\Models\Mensalidade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CobrancaVencida extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Mensalidade $mensalidade
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Parcela em atraso — {$this->mensalidade->competenciaFormatada}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cobranca.vencida',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
