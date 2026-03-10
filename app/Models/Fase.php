<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model {
    protected $table = "fases";
    protected $fillable = ["codigo", "descricao", "ordem", "ativo"];
}