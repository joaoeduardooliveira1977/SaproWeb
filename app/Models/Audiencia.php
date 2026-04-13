<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class Audiencia extends Model
{
    use BelongsToTenant;

    protected $table = 'audiencias';

    protected $fillable = [
        'processo_id', 'juiz_id', 'advogado_id', 'criado_por',
        'data_hora', 'tipo', 'sala', 'local', 'preposto', 'pauta',
        'status', 'resultado', 'resultado_descricao', 'proximo_passo', 'data_proximo',
    ];

    protected $casts = [
        'data_hora'    => 'datetime',
        'data_proximo' => 'date',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function juiz(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'juiz_id');
    }

    public function advogado(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'advogado_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    // ── Labels ────────────────────────────────────────────────

    public static function tiposLabel(): array
    {
        return [
            'conciliacao'           => 'Conciliação',
            'instrucao'             => 'Instrução',
            'instrucao_julgamento'  => 'Instrução e Julgamento',
            'julgamento'            => 'Julgamento',
            'una'                   => 'Una',
            'outra'                 => 'Outra',
        ];
    }

    public static function resultadosLabel(): array
    {
        return [
            'acordo'        => 'Acordo',
            'condenacao'    => 'Condenação',
            'improcedente'  => 'Improcedente',
            'extincao'      => 'Extinção',
            'nao_realizada' => 'Não Realizada',
            'outra'         => 'Outra',
        ];
    }

    public function tipoLabel(): string
    {
        return self::tiposLabel()[$this->tipo] ?? $this->tipo;
    }

    public function resultadoLabel(): string
    {
        return $this->resultado ? (self::resultadosLabel()[$this->resultado] ?? $this->resultado) : '—';
    }

    public function statusCor(): string
    {
        return match ($this->status) {
            'agendada'    => '#2563a8',
            'realizada'   => '#16a34a',
            'cancelada'   => '#dc2626',
            'redesignada' => '#d97706',
            default       => '#64748b',
        };
    }

    public function statusBg(): string
    {
        return match ($this->status) {
            'agendada'    => '#dbeafe',
            'realizada'   => '#dcfce7',
            'cancelada'   => '#fee2e2',
            'redesignada' => '#fef3c7',
            default       => '#f1f5f9',
        };
    }
}
