<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GrauRisco extends Model {
    protected $table = "graus_risco";
    protected $fillable = ["codigo", "descricao", "cor_hex"];
}