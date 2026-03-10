<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    // Laravel espera 'password' para autenticação automática
    protected $fillable = [
        'pessoa_id', 'login', 'password', 'perfil', 'ativo', 'ultimo_acesso',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'ativo'         => 'boolean',
        'ultimo_acesso' => 'datetime',
    ];

    /**
     * Laravel usa 'email' como username por padrão.
     * Sobrescrevemos para usar 'login'.
     */
    

public function getAuthIdentifierName(): string
{
    return 'id';
}

public function getAuthPassword(): string
{
    return $this->password;
}

public function username(): string
{
    return 'login';
}


    // ── Relacionamentos ────────────────────────────
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    // ── Helpers de perfil ──────────────────────────
    public function isAdmin(): bool
    {
        return $this->perfil === 'admin';
    }

    public function isAdvogado(): bool
    {
        return in_array($this->perfil, ['admin', 'advogado']);
    }

    public function getNomeAttribute(): string
    {
        return $this->pessoa?->nome ?? $this->login;
    }

    // ── Scope ──────────────────────────────────────
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // ── Auditoria ──────────────────────────────────
    public function registrarAuditoria(string $acao, string $tabela = null, int $registroId = null, array $dadosAntes = null, array $dadosApos = null): void
    {
        \App\Models\Auditoria::create([
            'usuario_id'  => $this->id,
            'login'       => $this->login,
            'acao'        => $acao,
            'tabela'      => $tabela,
            'registro_id' => $registroId,
            'dados_antes' => $dadosAntes,
            'dados_apos'  => $dadosApos,
            'ip'          => request()->ip(),
        ]);
    }
}
