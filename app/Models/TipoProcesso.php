<?php
namespace App\Models;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class TipoProcesso extends Model {
    use BelongsToTenant;
    protected $table = "tipos_processo";
    protected $fillable = ["tenant_id", "codigo", "descricao", "ativo"];
}