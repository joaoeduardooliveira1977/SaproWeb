<?php
namespace App\Models;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TipoAcao extends Model {
    use BelongsToTenant;
    protected $table = "tipos_acao";
    protected $fillable = ["tenant_id", "codigo", "descricao", "ativo"];
}