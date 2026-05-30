<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comunicado extends Model
{
    protected $fillable = ['titulo', 'mensagem', 'tipo', 'ativo', 'expira_em', 'criado_por'];

    protected $casts = [
        'ativo'     => 'boolean',
        'expira_em' => 'datetime',
    ];

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)
                     ->where(fn($q) => $q->whereNull('expira_em')->orWhere('expira_em', '>', now()));
    }
}
