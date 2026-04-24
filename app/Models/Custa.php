<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Custa extends Model {
    protected $table = "custas";
    protected $fillable = [
        "processo_id","data","descricao","valor","pago","data_pagamento","usuario_id",
        "reembolsavel","cobranca_lancamento_id","cobrado_em","cobrado_por"
    ];
    protected $casts = [
        "data"=>"date","data_pagamento"=>"date","pago"=>"boolean","valor"=>"decimal:2",
        "reembolsavel"=>"boolean","cobrado_em"=>"datetime"
    ];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
    public function cobrancaLancamento(): BelongsTo { return $this->belongsTo(FinanceiroLancamento::class, "cobranca_lancamento_id"); }
}
