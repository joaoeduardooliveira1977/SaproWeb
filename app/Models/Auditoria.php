<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model {
    protected $table = "auditorias";
    protected $fillable = ["usuario_id","login","acao","tabela","registro_id","dados_antes","dados_apos","ip"];
    protected $casts = ["dados_antes"=>"array","dados_apos"=>"array"];
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class, "usuario_id"); }
}