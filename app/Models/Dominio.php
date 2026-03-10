<?php
// ──────────────────────────────────────────────
// Models de Tabelas de Domínio
// ──────────────────────────────────────────────
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany, BelongsToMany};

// ─── Fase ──────────────────────────────────────
class Fase extends Model {
    protected $table = 'fases';
    protected $fillable = ['codigo', 'descricao', 'ordem', 'ativo'];
    public function processos(): HasMany { return $this->hasMany(Processo::class, 'fase_id'); }
}

// ─── GrauRisco ─────────────────────────────────
class GrauRisco extends Model {
    protected $table = 'graus_risco';
    protected $fillable = ['codigo', 'descricao', 'cor_hex'];
    public function processos(): HasMany { return $this->hasMany(Processo::class, 'risco_id'); }
}

// ─── TipoAcao ──────────────────────────────────
class TipoAcao extends Model {
    protected $table = 'tipos_acao';
    protected $fillable = ['codigo', 'descricao', 'ativo'];
}

// ─── TipoProcesso ──────────────────────────────
class TipoProcesso extends Model {
    protected $table = 'tipos_processo';
    protected $fillable = ['codigo', 'descricao', 'ativo'];
}

// ─── Assunto ───────────────────────────────────
class Assunto extends Model {
    protected $table = 'assuntos';
    protected $fillable = ['codigo', 'descricao', 'ativo'];
}

// ─── Reparticao ────────────────────────────────
class Reparticao extends Model {
    protected $table = 'reparticoes';
    protected $fillable = ['codigo', 'descricao', 'ativo'];
}

// ─── Secretaria ────────────────────────────────
class Secretaria extends Model {
    protected $table = 'secretarias';
    protected $fillable = ['codigo', 'descricao', 'ativo'];
}

// ─── IndiceMonetario ───────────────────────────
class IndiceMonetario extends Model {
    protected $table = 'indices_monetarios';
    protected $fillable = ['nome', 'sigla', 'mes_ref', 'percentual'];
    protected $casts = ['mes_ref' => 'date'];
}
