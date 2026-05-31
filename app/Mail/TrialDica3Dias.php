<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialDica3Dias extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Tenant $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Dica: como cadastrar seu primeiro processo no Software Jurídico',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial.dica-3dias',
            with: [
                'nomeResponsavel' => $this->tenant->responsavel_nome ?? $this->tenant->nome,
                'linkAcesso'      => 'https://' . ($this->tenant->dominio ?? $this->tenant->slug . '.kmd-ia.com.br'),
                'diasRestantes'   => $this->tenant->diasRestantesTrial(),
            ],
        );
    }
}
