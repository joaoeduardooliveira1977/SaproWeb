<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    protected $fillable = ['nome', 'mensagem', 'canal', 'ativo'];

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
