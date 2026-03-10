<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reparticao extends Model {
    protected $table = "reparticoes";
    protected $fillable = ["codigo", "descricao", "ativo"];
}