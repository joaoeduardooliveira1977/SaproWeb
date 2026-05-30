<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Comunicado extends Model
{
    protected $fillable = [
        'titulo', 'mensagem', 'tipo', 'prioridade', 'destino',
        'tenant_id', 'plano', 'data_inicio', 'data_fim', 'ativo', 'criado_por',
    ];

    protected $casts = [
        'ativo'       => 'boolean',
        'data_inicio' => 'datetime',
        'data_fim'    => 'datetime',
    ];

    // ── Tipo helpers ────────────────────────────────────────────

    public static array $tipos = [
        'manutencao_emergencial' => ['label' => 'Manutenção Emergencial', 'icon' => '🔴', 'cor' => '#dc2626', 'bg' => '#fee2e2', 'border' => '#fca5a5'],
        'manutencao_programada'  => ['label' => 'Manutenção Programada',  'icon' => '🔧', 'cor' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fcd34d'],
        'sistema_restaurado'     => ['label' => 'Sistema Restaurado',     'icon' => '✅', 'cor' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac'],
        'atualizacao'            => ['label' => 'Atualização',            'icon' => '🚀', 'cor' => '#2563a8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
        'trial_expirando'        => ['label' => 'Trial Expirando',        'icon' => '⏰', 'cor' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fcd34d'],
        'pagamento_pendente'     => ['label' => 'Pagamento Pendente',     'icon' => '💳', 'cor' => '#dc2626', 'bg' => '#fee2e2', 'border' => '#fca5a5'],
        'nova_funcionalidade'    => ['label' => 'Nova Funcionalidade',    'icon' => '✨', 'cor' => '#16a34a', 'bg' => '#f0fdf4', 'border' => '#86efac'],
        'informativo'            => ['label' => 'Informativo',            'icon' => 'ℹ️', 'cor' => '#2563a8', 'bg' => '#eff6ff', 'border' => '#bfdbfe'],
    ];

    public function tipoInfo(): array
    {
        return static::$tipos[$this->tipo] ?? static::$tipos['informativo'];
    }

    // ── Scopes ─────────────────────────────────────────────────

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true)
                     ->where('data_inicio', '<=', now())
                     ->where(fn($q) => $q->whereNull('data_fim')->orWhere('data_fim', '>=', now()));
    }

    public function scopeParaTenant($query, int $tenantId, ?string $plano = null)
    {
        return $query->where(function ($q) use ($tenantId, $plano) {
            $q->where('destino', 'todos')
              ->orWhere(fn($q) => $q->where('destino', 'tenant_especifico')->where('tenant_id', $tenantId))
              ->orWhere(fn($q) => $q->where('destino', 'plano_especifico')->where('plano', $plano));
        });
    }

    public function scopePrioridade($query, string $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    // ── Relationships ────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(ComunicadoLeitura::class);
    }

    // ── Helpers ──────────────────────────────────────────────────

    public function foiLidoPor(int $usuarioId): bool
    {
        return $this->leituras()->where('usuario_id', $usuarioId)->exists();
    }

    public function marcarLidoPor(int $usuarioId, int $tenantId): void
    {
        $this->leituras()->firstOrCreate(
            ['usuario_id' => $usuarioId],
            ['tenant_id'  => $tenantId, 'lido_em' => now()]
        );
    }

    public function taxaLeitura(): float
    {
        $total = $this->leituras()->count();
        if (!$total) return 0;
        // Aproximação: divide pelo número de usuários impactados
        $usuarios = Usuario::when($this->destino === 'tenant_especifico' && $this->tenant_id,
            fn($q) => $q->where('tenant_id', $this->tenant_id)
        )->count();
        return $usuarios ? round(($total / $usuarios) * 100, 1) : 0;
    }
}
