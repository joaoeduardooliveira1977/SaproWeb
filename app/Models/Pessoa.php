<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};

class Pessoa extends Model
{
    protected $table = 'pessoas';

    protected $fillable = [
        'nome', 'cpf_cnpj', 'rg', 'data_nascimento',
        'telefone', 'celular', 'email',
        'logradouro', 'cidade', 'estado', 'cep',
        'oab', 'observacoes', 'ativo', 'administradora_id',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'ativo'           => 'boolean',
    ];

    // ── Tipos de pessoa (multi-tipo) ───────────────
    public function tipos(): HasMany
{
    return $this->hasMany(\App\Models\PessoaTipo::class, 'pessoa_id');
}

    /**
     * Retorna os tipos como array simples.
     * Uso: $pessoa->listaTipos()  => ['Cliente', 'Advogado']
     */
    public function listaTipos(): array
    {
        return \Illuminate\Support\Facades\DB::table('pessoa_tipos')
            ->where('pessoa_id', $this->id)
            ->pluck('tipo')
            ->toArray();
    }

    /**
     * Sincroniza os tipos da pessoa.
     * Uso: $pessoa->sincronizarTipos(['Cliente', 'Advogado'])
     */
    public function sincronizarTipos(array $tipos): void
    {
        \Illuminate\Support\Facades\DB::table('pessoa_tipos')
            ->where('pessoa_id', $this->id)
            ->delete();

        foreach ($tipos as $tipo) {
            \Illuminate\Support\Facades\DB::table('pessoa_tipos')->insert([
                'pessoa_id' => $this->id,
                'tipo'      => $tipo,
            ]);
        }
    }

    // ── Relacionamentos ────────────────────────────
    public function processosComoCliente(): HasMany
    {
        return $this->hasMany(Processo::class, 'cliente_id');
    }

    public function processosComoAdvogado(): HasMany
    {
        return $this->hasMany(Processo::class, 'advogado_id');
    }

    public function usuario(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Usuario::class, 'pessoa_id');
    }

    public function administradora(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Administradora::class);
    }

    // ── Scopes ─────────────────────────────────────
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeDoTipo($query, string $tipo)
    {
        return $query->whereExists(function ($q) use ($tipo) {
            $q->from('pessoa_tipos')
              ->whereColumn('pessoa_id', 'pessoas.id')
              ->where('tipo', $tipo);
        });
    }

    public function scopeBusca($query, string $termo)
    {
        return $query->where(function ($q) use ($termo) {
            $q->where('nome', 'ilike', "%{$termo}%")
              ->orWhere('cpf_cnpj', 'ilike', "%{$termo}%")
              ->orWhere('email', 'ilike', "%{$termo}%");
        });
    }
}
