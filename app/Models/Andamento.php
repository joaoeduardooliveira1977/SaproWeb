<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class Andamento extends Model {
    use BelongsToTenant;
    protected $table = "andamentos";
    protected $fillable = ["processo_id", "data", "descricao", "interno", "usuario_id"];
    protected $casts = ["data" => "date", "interno" => "boolean"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }

    public function scopePublico($query) { return $query->where('interno', false); }
}