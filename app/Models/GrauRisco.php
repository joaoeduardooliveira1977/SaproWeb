<?php
namespace App\Models;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class GrauRisco extends Model {
    use BelongsToTenant;
    protected $table = "graus_risco";
    protected $fillable = ["tenant_id", "codigo", "descricao", "cor_hex"];
}