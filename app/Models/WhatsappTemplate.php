<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    use BelongsToTenant;

    protected $table = 'whatsapp_templates';

    protected $fillable = ['tenant_id', 'nome', 'mensagem', 'canal', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeParaCanal($query, string $canal)
    {
        return $query->where(fn($q) => $q->where('canal', $canal)->orWhere('canal', 'ambos'));
    }
}
