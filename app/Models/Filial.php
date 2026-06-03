<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filial extends Model
{
    use BelongsToTenant;

    protected $table    = 'filiais';
    protected $fillable = ['tenant_id', 'nome', 'ativo'];
    protected $casts    = ['ativo' => 'boolean'];

    public function pessoas(): HasMany
    {
        return $this->hasMany(Pessoa::class, 'filial_id');
    }
}
