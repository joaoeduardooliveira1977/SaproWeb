<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialAviso12Dias extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Tenant $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '⚠️ Seu trial do Software Jurídico expira em 3 dias',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial.aviso-12dias',
            with: [
                'nomeResponsavel' => $this->tenant->responsavel_nome ?? $this->tenant->nome,
                'linkAcesso'      => 'https://' . ($this->tenant->dominio ?? $this->tenant->slug . '.kmd-ia.com.br'),
                'diasRestantes'   => $this->tenant->diasRestantesTrial(),
                'linkWhatsapp'    => $this->whatsappLink("Olá! Meu trial do Software Jurídico vence em 3 dias e quero continuar usando. Podemos conversar sobre os planos?"),
            ],
        );
    }

    private function whatsappLink(string $msg): string
    {
        $numero = preg_replace('/\D/', '', env('WHATSAPP_SUPORTE', '5511999999999'));
        return "https://wa.me/{$numero}?text=" . rawurlencode($msg);
    }
}
