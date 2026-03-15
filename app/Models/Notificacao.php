<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'usuario_id', 'tipo', 'titulo', 'mensagem',
        'referencia_tipo', 'referencia_id', 'link', 'lida',
    ];

    protected $casts = [
        'lida' => 'boolean',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    // ── Scopes ──────────────────────────────────────────────────

    /** Notificações visíveis para um usuário (dele + globais) */
    public function scopeParaUsuario($query, int $usuarioId)
    {
        return $query->where(function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId)
              ->orWhereNull('usuario_id');
        });
    }

    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Evita duplicatas: verifica se já existe notificação do mesmo tipo
     * para a mesma referência criada hoje.
     */
    public static function jaExiste(string $tipo, string $refTipo, int $refId): bool
    {
        return static::where('tipo', $tipo)
            ->where('referencia_tipo', $refTipo)
            ->where('referencia_id', $refId)
            ->whereDate('created_at', today())
            ->exists();
    }

    /** Ícone por tipo */
    public function icone(): string
    {
        return match ($this->tipo) {
            'prazo_fatal'           => '🚨',
            'prazo_vencendo'        => '⏳',
            'prazo_vencido'         => '❌',
            'honorario_atrasado'    => '💸',
            'processo_sem_andamento'=> '📋',
            default                 => '🔔',
        };
    }

    /** Cor de fundo por tipo */
    public function cor(): string
    {
        return match ($this->tipo) {
            'prazo_fatal'           => '#fce7f3',
            'prazo_vencendo'        => '#fef9c3',
            'prazo_vencido'         => '#fee2e2',
            'honorario_atrasado'    => '#ede9fe',
            'processo_sem_andamento'=> '#f0fdf4',
            default                 => '#f1f5f9',
        };
    }
}
