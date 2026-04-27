<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Traits\BelongsToTenant;

class Indicador extends Model
{
    use BelongsToTenant;

    protected $table = 'indicadores';

    protected $fillable = [
        'nome', 'email', 'celular', 'cpf', 'percentual_comissao', 'observacoes', 'ativo',
    ];

    protected $casts = [
        'percentual_comissao' => 'decimal:2',
        'ativo'               => 'boolean',
    ];

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'indicador_id');
    }

    public function comissoes(): HasMany
    {
        return $this->hasMany(Comissao::class, 'indicador_id');
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeBusca($query, string $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'ilike', "%{$termo}%")
              ->orWhere('email', 'ilike', "%{$termo}%")
              ->orWhere('cpf', 'ilike', "%{$termo}%");
        });
    }
}
