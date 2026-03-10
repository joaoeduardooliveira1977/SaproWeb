<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Custa extends Model {
    protected $table = "custas";
    protected $fillable = ["processo_id","data","descricao","valor","pago","data_pagamento","usuario_id"];
    protected $casts = ["data"=>"date","data_pagamento"=>"date","pago"=>"boolean","valor"=>"decimal:2"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
}