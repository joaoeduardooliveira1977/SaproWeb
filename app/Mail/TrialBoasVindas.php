<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrialBoasVindas extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Tenant $tenant) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo ao Software Jurídico — seu trial começou! 🎉',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.trial.boas-vindas',
            with: [
                'nomeResponsavel' => $this->tenant->responsavel_nome ?? $this->tenant->nome,
                'nomeEscritorio'  => $this->tenant->nome,
                'linkAcesso'      => 'https://' . ($this->tenant->dominio ?? $this->tenant->slug . '.kmd-ia.com.br'),
                'diasTrial'       => 15,
                'emailAcesso'     => $this->tenant->email,
            ],
        );
    }
}
