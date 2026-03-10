@echo off
echo Criando arquivos de model individuais...

:: Fase.php
echo ^<?php > "app\Models\Fase.php"
echo namespace App\Models; >> "app\Models\Fase.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\Fase.php"
echo use Illuminate\Database\Eloquent\Relations\HasMany; >> "app\Models\Fase.php"
echo class Fase extends Model { >> "app\Models\Fase.php"
echo     protected $table = 'fases'; >> "app\Models\Fase.php"
echo     protected $fillable = ['codigo', 'descricao', 'ordem', 'ativo']; >> "app\Models\Fase.php"
echo     public function processos(): HasMany { return $this->hasMany(Processo::class, 'fase_id'); } >> "app\Models\Fase.php"
echo } >> "app\Models\Fase.php"

:: GrauRisco.php
echo ^<?php > "app\Models\GrauRisco.php"
echo namespace App\Models; >> "app\Models\GrauRisco.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\GrauRisco.php"
echo use Illuminate\Database\Eloquent\Relations\HasMany; >> "app\Models\GrauRisco.php"
echo class GrauRisco extends Model { >> "app\Models\GrauRisco.php"
echo     protected $table = 'graus_risco'; >> "app\Models\GrauRisco.php"
echo     protected $fillable = ['codigo', 'descricao', 'cor_hex']; >> "app\Models\GrauRisco.php"
echo     public function processos(): HasMany { return $this->hasMany(Processo::class, 'risco_id'); } >> "app\Models\GrauRisco.php"
echo } >> "app\Models\GrauRisco.php"

:: TipoAcao.php
echo ^<?php > "app\Models\TipoAcao.php"
echo namespace App\Models; >> "app\Models\TipoAcao.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\TipoAcao.php"
echo class TipoAcao extends Model { >> "app\Models\TipoAcao.php"
echo     protected $table = 'tipos_acao'; >> "app\Models\TipoAcao.php"
echo     protected $fillable = ['codigo', 'descricao', 'ativo']; >> "app\Models\TipoAcao.php"
echo } >> "app\Models\TipoAcao.php"

:: TipoProcesso.php
echo ^<?php > "app\Models\TipoProcesso.php"
echo namespace App\Models; >> "app\Models\TipoProcesso.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\TipoProcesso.php"
echo class TipoProcesso extends Model { >> "app\Models\TipoProcesso.php"
echo     protected $table = 'tipos_processo'; >> "app\Models\TipoProcesso.php"
echo     protected $fillable = ['codigo', 'descricao', 'ativo']; >> "app\Models\TipoProcesso.php"
echo } >> "app\Models\TipoProcesso.php"

:: Assunto.php
echo ^<?php > "app\Models\Assunto.php"
echo namespace App\Models; >> "app\Models\Assunto.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\Assunto.php"
echo class Assunto extends Model { >> "app\Models\Assunto.php"
echo     protected $table = 'assuntos'; >> "app\Models\Assunto.php"
echo     protected $fillable = ['codigo', 'descricao', 'ativo']; >> "app\Models\Assunto.php"
echo } >> "app\Models\Assunto.php"

:: Reparticao.php
echo ^<?php > "app\Models\Reparticao.php"
echo namespace App\Models; >> "app\Models\Reparticao.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\Reparticao.php"
echo class Reparticao extends Model { >> "app\Models\Reparticao.php"
echo     protected $table = 'reparticoes'; >> "app\Models\Reparticao.php"
echo     protected $fillable = ['codigo', 'descricao', 'ativo']; >> "app\Models\Reparticao.php"
echo } >> "app\Models\Reparticao.php"

:: Secretaria.php
echo ^<?php > "app\Models\Secretaria.php"
echo namespace App\Models; >> "app\Models\Secretaria.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\Secretaria.php"
echo class Secretaria extends Model { >> "app\Models\Secretaria.php"
echo     protected $table = 'secretarias'; >> "app\Models\Secretaria.php"
echo     protected $fillable = ['codigo', 'descricao', 'ativo']; >> "app\Models\Secretaria.php"
echo } >> "app\Models\Secretaria.php"

:: IndiceMonetario.php
echo ^<?php > "app\Models\IndiceMonetario.php"
echo namespace App\Models; >> "app\Models\IndiceMonetario.php"
echo use Illuminate\Database\Eloquent\Model; >> "app\Models\IndiceMonetario.php"
echo class IndiceMonetario extends Model { >> "app\Models\IndiceMonetario.php"
echo     protected $table = 'indices_monetarios'; >> "app\Models\IndiceMonetario.php"
echo     protected $fillable = ['nome', 'sigla', 'mes_ref', 'percentual']; >> "app\Models\IndiceMonetario.php"
echo     protected $casts = ['mes_ref' =^> 'date']; >> "app\Models\IndiceMonetario.php"
echo } >> "app\Models\IndiceMonetario.php"

echo Pronto! Rodando composer dump-autoload...
composer dump-autoload
echo Concluido!
