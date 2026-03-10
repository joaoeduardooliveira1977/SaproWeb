<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoAcao extends Model {
    protected $table = "tipos_acao";
    protected $fillable = ["codigo", "descricao", "ativo"];
}