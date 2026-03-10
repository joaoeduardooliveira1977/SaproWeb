<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model {
    protected $table = "fornecedores";
    protected $fillable = ["nome", "cnpj_cpf", "telefone", "email", "observacoes", "ativo"];
    protected $casts = ["ativo" => "boolean"];
    public function pagamentos(): HasMany { return $this->hasMany(Pagamento::class, "fornecedor_id"); }
    public function scopeAtivos($query) { return $query->where("ativo", true); }
}