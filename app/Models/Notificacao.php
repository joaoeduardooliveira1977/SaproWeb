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