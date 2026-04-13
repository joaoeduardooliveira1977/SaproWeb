<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Notificacao extends Model
{
    protected $table = 'notificacoes';
    protected $fillable = [
        'tipo',
        'titulo',
        'mensagem',
        'processo_id',
        'usuario_id',
        'user_id',
        'referencia_tipo',
        'referencia_id',
        'lida',
        'link',
    ];
    protected $casts = [
        'lida' => 'boolean',
    ];
    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function scopeParaUsuario($query, int $usuarioId)
    {
        return $query->where(function ($q) use ($usuarioId) {
            $q->where('usuario_id', $usuarioId)
              ->orWhere('user_id', $usuarioId)
              ->orWhere(function ($global) {
                  $global->whereNull('usuario_id')
                      ->whereNull('user_id');
              });
        });
    }

    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    public static function jaExiste(string $tipo, string $referenciaTipo, int $referenciaId, ?string $mensagemContem = null): bool
    {
        return static::where('tipo', $tipo)
            ->where('referencia_tipo', $referenciaTipo)
            ->where('referencia_id', $referenciaId)
            ->when($mensagemContem, fn ($query) => $query->where(function ($q) use ($mensagemContem) {
                $q->where('mensagem', 'like', "%{$mensagemContem}%")
                    ->orWhere('link', 'like', "%{$mensagemContem}%");
            }))
            ->exists();
    }

    public function cor(): string
    {
        return match($this->tipo) {
            'prazo_fatal'             => '#fff1f2',
            'prazo_vencendo'         => '#fefce8',
            'prazo_vencido'          => '#fef2f2',
            'honorario_atrasado'     => '#f5f3ff',
            'processo_sem_andamento' => '#f0fdf4',
            default                  => '#eff6ff',
        };
    }
}
