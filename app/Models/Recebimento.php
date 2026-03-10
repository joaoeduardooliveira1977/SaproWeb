<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recebimento extends Model {
    protected $table = "recebimentos";
    protected $fillable = ["processo_id","origem_id","data","numero_doc","documento","descricao","valor","valor_recebido","data_recebimento","recebido","usuario_id"];
    protected $casts = ["data"=>"date","data_recebimento"=>"date","valor"=>"decimal:2","valor_recebido"=>"decimal:2","recebido"=>"boolean"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function origem(): BelongsTo   { return $this->belongsTo(OrigemRecebimento::class, "origem_id"); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }


public static function totaisPorProcesso(int $processoId): array
{
    $rows = static::where('processo_id', $processoId)->get();
    return [
        'total'     => $rows->sum('valor'),
        'recebido'  => $rows->where('recebido', true)->sum('valor_recebido'),
        'pendente'  => $rows->where('recebido', false)->sum('valor'),
    ];
}



}