<?php
namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Reparticao extends Model {
    use BelongsToTenant;

    protected $table = "reparticoes";
    protected $fillable = ["tenant_id", "codigo", "descricao", "ativo"];
}
