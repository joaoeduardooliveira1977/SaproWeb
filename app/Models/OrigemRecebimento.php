<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrigemRecebimento extends Model {
    protected $table = "origens_recebimento";
    protected $fillable = ["descricao", "ativo"];
    protected $casts = ["ativo" => "boolean"];
    public function recebimentos(): HasMany { return $this->hasMany(Recebimento::class, "origem_id"); }
}