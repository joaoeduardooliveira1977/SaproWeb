<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administradora extends Model
{
    protected $table = 'administradoras';

    protected $fillable = [
        'nome', 'cnpj', 'telefone', 'email', 'contato', 'observacoes', 'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function clientes(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Pessoa::class, 'administradora_id');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }
}
