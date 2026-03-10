<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model {
    protected $table = "agenda";
    protected $fillable = ["titulo","data_hora","local","tipo","urgente","processo_id","responsavel_id","concluido","observacoes"];
    protected $casts = ["data_hora"=>"datetime","urgente"=>"boolean","concluido"=>"boolean"];
    public function processo(): BelongsTo    { return $this->belongsTo(Processo::class); }
    public function responsavel(): BelongsTo { return $this->belongsTo(Usuario::class, "responsavel_id"); }
}