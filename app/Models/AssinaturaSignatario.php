<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssinaturaSignatario extends Model
{
    protected $table = 'assinatura_signatarios';

    protected $fillable = [
        'assinatura_id', 'nome', 'email', 'cpf', 'celular',
        'papel', 'auth', 'clicksign_signer_key',
        'status', 'assinado_em', 'ordem',
    ];

    protected $casts = [
        'assinado_em' => 'datetime',
    ];

    public function assinatura(): BelongsTo
    {
        return $this->belongsTo(Assinatura::class, 'assinatura_id');
    }

    public static function papeisLabel(): array
    {
        return [
            'assinar'                 => 'Assinar',
            'assinar_como_parte'      => 'Assinar como parte',
            'assinar_como_testemunha' => 'Testemunha',
            'aprovar'                 => 'Aprovar',
            'reconhecer'              => 'Reconhecer',
            'rubricar'                => 'Rubricar',
        ];
    }

    public static function authsLabel(): array
    {
        return [
            'email'    => 'E-mail',
            'sms'      => 'SMS',
            'whatsapp' => 'WhatsApp',
            'pix'      => 'PIX (CPF)',
        ];
    }

    public function statusCor(): string
    {
        return match($this->status) {
            'pendente'  => 'bg-gray-100 text-gray-500',
            'enviado'   => 'bg-blue-100 text-blue-700',
            'assinado'  => 'bg-green-100 text-green-700',
            'recusado'  => 'bg-red-100 text-red-700',
            default     => 'bg-gray-100 text-gray-500',
        };
    }
}
