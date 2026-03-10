<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoProcesso extends Model {
    protected $table = "tipos_processo";
    protected $fillable = ["codigo", "descricao", "ativo"];
}