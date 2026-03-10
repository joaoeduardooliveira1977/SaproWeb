<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apontamento extends Model {
    protected $table = "apontamentos";
    protected $fillable = ["processo_id","advogado_id","data","descricao","horas","valor","usuario_id"];
    protected $casts = ["data"=>"date","horas"=>"decimal:2","valor"=>"decimal:2"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function advogado(): BelongsTo { return $this->belongsTo(Pessoa::class, "advogado_id"); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }


public static function totaisPorProcesso(int $processoId): array
{
    $rows = static::where('processo_id', $processoId)->get();
    return [
        'total_horas' => $rows->sum('horas'),
        'total_valor' => $rows->sum('valor'),
    ];
}



}