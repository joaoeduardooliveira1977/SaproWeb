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
        'tenant_id', 'pessoa_id', 'nome', 'login', 'email', 'password', 'perfil', 'telefone', 'ativo', 'ultimo_acesso',
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

    /** Verifica acesso a um módulo (usado em Livewire) */
    public function can($abilities, $arguments = []): bool
    {
        // Compatibilidade com o sistema de gate do Laravel
        if (is_string($abilities) && !str_contains($abilities, '.')) {
            return $this->temModulo($abilities);
        }
        return parent::can($abilities, $arguments);
    }

    public function temModulo(string $modulo): bool
    {
        if ($this->perfil === 'admin') return true;
        $permissoes = \App\Http\Middleware\VerificarPerfil::PERMISSOES[$this->perfil] ?? [];
        return in_array($modulo, $permissoes);
    }

    public function temAcao(string $acao): bool
    {
        if ($this->perfil === 'admin') return true;
        $acoes = \App\Http\Middleware\VerificarPerfil::ACOES[$this->perfil] ?? [];
        return in_array($acao, $acoes);
    }

    public function getNomeAttribute(): string
    {
        return $this->attributes['nome'] ?? $this->pessoa?->nome ?? $this->login;
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
