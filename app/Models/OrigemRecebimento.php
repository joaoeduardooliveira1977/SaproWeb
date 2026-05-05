<?php
namespace App\Models;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrigemRecebimento extends Model {
    use BelongsToTenant;
    protected $table = "origens_recebimento";
    protected $fillable = ["tenant_id", "descricao", "ativo"];
    protected $casts = ["ativo" => "boolean"];
    public function recebimentos(): HasMany { return $this->hasMany(Recebimento::class, "origem_id"); }
}