<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialExpiracao15Dias extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Tenant $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Seu acesso ao Software Jurídico expirou',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial.expiracao-15dias',
            with: [
                'nomeResponsavel' => $this->tenant->responsavel_nome ?? $this->tenant->nome,
                'nomeEscritorio'  => $this->tenant->nome,
                'linkWhatsapp'    => $this->whatsappLink("Olá! Meu trial do Software Jurídico expirou e gostaria de reativar minha conta."),
                'emailSuporte'    => env('EMAIL_SUPORTE', 'suporte@softwarejuridico.com.br'),
            ],
        );
    }

    private function whatsappLink(string $msg): string
    {
        $numero = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
        return "https://wa.me/{$numero}?text=" . rawurlencode($msg);
    }
}
