<?php
namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Assunto extends Model {
    use BelongsToTenant;

    protected $table = "assuntos";
    protected $fillable = ["tenant_id", "codigo", "descricao", "ativo"];
}
