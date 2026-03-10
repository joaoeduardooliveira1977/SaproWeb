<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model {
    protected $table = "pagamentos";
    protected $fillable = ["processo_id","fornecedor_id","data","numero_doc","documento","descricao","valor","valor_pago","data_vencimento","data_pagamento","pago","usuario_id"];
    protected $casts = ["data"=>"date","data_vencimento"=>"date","data_pagamento"=>"date","valor"=>"decimal:2","valor_pago"=>"decimal:2","pago"=>"boolean"];
    public function processo(): BelongsTo   { return $this->belongsTo(Processo::class); }
    public function fornecedor(): BelongsTo { return $this->belongsTo(Fornecedor::class, "fornecedor_id"); }
    public function usuario(): BelongsTo    { return $this->belongsTo(Usuario::class, "usuario_id"); }



public static function totaisPorProcesso(int $processoId): array
{
    $rows = static::where('processo_id', $processoId)->get();
    return [
        'total'    => $rows->sum('valor'),
        'pago'     => $rows->where('pago', true)->sum('valor_pago'),
        'pendente' => $rows->where('pago', false)->sum('valor'),
    ];
}


}


