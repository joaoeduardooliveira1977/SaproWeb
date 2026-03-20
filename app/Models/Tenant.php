<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'plano',
        'ativo',
    ];

    // relacionamento reverso (opcional mas importante)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}