<?php
// Salve este arquivo como C:\projetos\saproweb-base\corrigir_models.php
// Execute com: php corrigir_models.php

$models = [

'Fase.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Fase extends Model {
    protected $table = "fases";
    protected $fillable = ["codigo", "descricao", "ordem", "ativo"];
}',

'GrauRisco.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class GrauRisco extends Model {
    protected $table = "graus_risco";
    protected $fillable = ["codigo", "descricao", "cor_hex"];
}',



'TipoAcao.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoAcao extends Model {
    protected $table = "tipos_acao";
    protected $fillable = ["codigo", "descricao", "ativo"];
}',

'TipoProcesso.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TipoProcesso extends Model {
    protected $table = "tipos_processo";
    protected $fillable = ["codigo", "descricao", "ativo"];
}',

'Assunto.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Assunto extends Model {
    protected $table = "assuntos";
    protected $fillable = ["codigo", "descricao", "ativo"];
}',

'Reparticao.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Reparticao extends Model {
    protected $table = "reparticoes";
    protected $fillable = ["codigo", "descricao", "ativo"];
}',

'Secretaria.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Secretaria extends Model {
    protected $table = "secretarias";
    protected $fillable = ["codigo", "descricao", "ativo"];
}',

'IndiceMonetario.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class IndiceMonetario extends Model {
    protected $table = "indices_monetarios";
    protected $fillable = ["nome", "sigla", "mes_ref", "percentual"];
    protected $casts = ["mes_ref" => "date"];
}',

'Andamento.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Andamento extends Model {
    protected $table = "andamentos";
    protected $fillable = ["processo_id", "data", "descricao", "usuario_id"];
    protected $casts = ["data" => "date"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',

'Agenda.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model {
    protected $table = "agenda";
    protected $fillable = ["titulo","data_hora","local","tipo","urgente","processo_id","responsavel_id","concluido","observacoes"];
    protected $casts = ["data_hora"=>"datetime","urgente"=>"boolean","concluido"=>"boolean"];
    public function processo(): BelongsTo    { return $this->belongsTo(Processo::class); }
    public function responsavel(): BelongsTo { return $this->belongsTo(Usuario::class, "responsavel_id"); }
}',

'Custa.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Custa extends Model {
    protected $table = "custas";
    protected $fillable = ["processo_id","data","descricao","valor","pago","data_pagamento","usuario_id"];
    protected $casts = ["data"=>"date","data_pagamento"=>"date","pago"=>"boolean","valor"=>"decimal:2"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',

'Auditoria.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model {
    protected $table = "auditorias";
    protected $fillable = ["usuario_id","login","acao","tabela","registro_id","dados_antes","dados_apos","ip"];
    protected $casts = ["dados_antes"=>"array","dados_apos"=>"array"];
    public function usuario(): BelongsTo { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',


'Fornecedor.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fornecedor extends Model {
    protected $table = "fornecedores";
    protected $fillable = ["nome", "cnpj_cpf", "telefone", "email", "observacoes", "ativo"];
    protected $casts = ["ativo" => "boolean"];
    public function pagamentos(): HasMany { return $this->hasMany(Pagamento::class, "fornecedor_id"); }
    public function scopeAtivos($query) { return $query->where("ativo", true); }
}',

'OrigemRecebimento.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrigemRecebimento extends Model {
    protected $table = "origens_recebimento";
    protected $fillable = ["descricao", "ativo"];
    protected $casts = ["ativo" => "boolean"];
    public function recebimentos(): HasMany { return $this->hasMany(Recebimento::class, "origem_id"); }
}',

'Apontamento.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Apontamento extends Model {
    protected $table = "apontamentos";
    protected $fillable = ["processo_id","advogado_id","data","descricao","horas","valor","usuario_id"];
    protected $casts = ["data"=>"date","horas"=>"decimal:2","valor"=>"decimal:2"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function advogado(): BelongsTo { return $this->belongsTo(Pessoa::class, "advogado_id"); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',

'Pagamento.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model {
    protected $table = "pagamentos";
    protected $fillable = ["processo_id","fornecedor_id","data","numero_doc","documento","descricao","valor","valor_pago","data_vencimento","data_pagamento","pago","usuario_id"];
    protected $casts = ["data"=>"date","data_vencimento"=>"date","data_pagamento"=>"date","valor"=>"decimal:2","valor_pago"=>"decimal:2","pago"=>"boolean"];
    public function processo(): BelongsTo   { return $this->belongsTo(Processo::class); }
    public function fornecedor(): BelongsTo { return $this->belongsTo(Fornecedor::class, "fornecedor_id"); }
    public function usuario(): BelongsTo    { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',

'Recebimento.php' => '<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recebimento extends Model {
    protected $table = "recebimentos";
    protected $fillable = ["processo_id","origem_id","data","numero_doc","documento","descricao","valor","valor_recebido","data_recebimento","recebido","usuario_id"];
    protected $casts = ["data"=>"date","data_recebimento"=>"date","valor"=>"decimal:2","valor_recebido"=>"decimal:2","recebido"=>"boolean"];
    public function processo(): BelongsTo { return $this->belongsTo(Processo::class); }
    public function origem(): BelongsTo   { return $this->belongsTo(OrigemRecebimento::class, "origem_id"); }
    public function usuario(): BelongsTo  { return $this->belongsTo(Usuario::class, "usuario_id"); }
}',


];

$dir = __DIR__ . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR;

foreach ($models as $filename => $content) {
    $path = $dir . $filename;
    file_put_contents($path, $content);
    echo "✅ Criado: $filename\n";
}

echo "\nTodos os models corrigidos! Rode agora: composer dump-autoload\n";
