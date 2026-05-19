<?php
namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model {
    use BelongsToTenant;

    protected $table = "secretarias";
    protected $fillable = ["tenant_id", "codigo", "descricao", "ativo"];
}
