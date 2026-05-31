<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialLembrete7Dias extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Tenant $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Como está sua experiência com o Software Jurídico?',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial.lembrete-7dias',
            with: [
                'nomeResponsavel' => $this->tenant->responsavel_nome ?? $this->tenant->nome,
                'linkAcesso'      => 'https://' . ($this->tenant->dominio ?? $this->tenant->slug . '.kmd-ia.com.br'),
                'diasRestantes'   => $this->tenant->diasRestantesTrial(),
                'linkWhatsapp'    => $this->whatsappLink('Olá! Estou usando o Software Jurídico e gostaria de falar sobre minha experiência.'),
            ],
        );
    }

    private function whatsappLink(string $msg): string
    {
        $numero = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
        return "https://wa.me/{$numero}?text=" . rawurlencode($msg);
    }
}
