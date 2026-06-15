<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanoModulo extends Model
{
    protected $fillable = ['plano', 'modulo_id'];

    public function modulo(): BelongsTo
    {
        return $this->belongsTo(Modulo::class);
    }
}
