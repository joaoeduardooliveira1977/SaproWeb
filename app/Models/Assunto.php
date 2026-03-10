<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Assunto extends Model {
    protected $table = "assuntos";
    protected $fillable = ["codigo", "descricao", "ativo"];
}