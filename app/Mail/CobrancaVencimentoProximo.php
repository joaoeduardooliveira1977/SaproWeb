<?php

namespace App\Mail;

use App\Models\Mensalidade;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CobrancaVencimentoProximo extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Mensalidade $mensalidade,
        public int $diasRestantes
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Lembrete de vencimento — {$this->mensalidade->competenciaFormatada}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.cobranca.vencimento-proximo',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
