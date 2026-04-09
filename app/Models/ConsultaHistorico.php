<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class ConsultaHistorico extends Model
{
    use BelongsToTenant;

    protected $table = 'consultas_historico';

    protected $fillable = [
        'tenant_id', 'numero_processo', 'tribunal',
        'usuario_id', 'resultado_count',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }
}
