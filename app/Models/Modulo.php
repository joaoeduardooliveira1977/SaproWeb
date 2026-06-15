<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Modulo extends Model
{
    protected $fillable = ['chave', 'nome', 'descricao', 'icone', 'ativo', 'ordem'];

    protected $casts = ['ativo' => 'boolean'];

    public function tenantModulos(): HasMany
    {
        return $this->hasMany(TenantModulo::class);
    }

    public function planoModulos(): HasMany
    {
        return $this->hasMany(PlanoModulo::class);
    }
}
