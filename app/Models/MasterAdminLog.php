<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MasterAdminLog extends Model
{
    public $timestamps   = false;
    const CREATED_AT     = 'created_at';
    protected $table     = 'master_admin_logs';

    protected $fillable  = [
        'admin_id', 'admin_nome', 'acao', 'contexto',
        'tenant_id', 'tenant_nome', 'detalhes', 'ip', 'user_agent',
    ];

    // ── Relacionamentos ───────────────────────────────────────────

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'admin_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id')->withTrashed();
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopePorPeriodo($query, ?string $inicio, ?string $fim)
    {
        return $query
            ->when($inicio, fn($q) => $q->where('created_at', '>=', $inicio))
            ->when($fim,    fn($q) => $q->where('created_at', '<=', $fim . ' 23:59:59'));
    }

    public function scopePorAcao($query, string $acao)
    {
        return $query->where('acao', $acao);
    }

    // ── Helper principal ──────────────────────────────────────────

    public static function registrar(
        string  $acao,
        ?int    $tenantId   = null,
        ?string $tenantNome = null,
        ?string $detalhes   = null,
        ?string $contexto   = null
    ): void {
        $admin = auth('usuarios')->user();

        static::create([
            'admin_id'    => $admin?->id,
            'admin_nome'  => $admin?->nome ?? 'Sistema',
            'acao'        => $acao,
            'contexto'    => $contexto,
            'tenant_id'   => $tenantId,
            'tenant_nome' => $tenantNome,
            'detalhes'    => $detalhes,
            'ip'          => request()->ip(),
            'user_agent'  => substr(request()->userAgent() ?? '', 0, 300),
        ]);
    }

    // ── Cor do badge por ação ─────────────────────────────────────

    public static function badgeClass(string $acao): string
    {
        return match(true) {
            str_contains($acao, 'suspenso')   => 'badge-red',
            str_contains($acao, 'excluido')   => 'badge-red',
            str_contains($acao, 'bloqueio')   => 'badge-red',
            str_contains($acao, 'falha')      => 'badge-red',
            str_contains($acao, 'ativado')    => 'badge-green',
            str_contains($acao, 'restaurado') => 'badge-green',
            str_contains($acao, 'criado')     => 'badge-green',
            str_contains($acao, 'login')      => 'badge-blue',
            str_contains($acao, '2fa')        => 'badge-purple',
            str_contains($acao, 'senha')      => 'badge-yellow',
            str_contains($acao, 'impersonat') => 'badge-orange',
            str_contains($acao, 'limite')     => 'badge-yellow',
            default                           => 'badge-gray',
        };
    }
}
