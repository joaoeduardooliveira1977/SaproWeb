<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use App\Models\Traits\BelongsToTenant;

class Contrato extends Model
{
    use BelongsToTenant;

    protected $table = 'contratos';

    protected $fillable = [
        'tenant_id', 'cliente_id', 'tipo', 'descricao', 'observacoes',
        'advogado_responsavel_id', 'processo_id',
        'forma_cobranca', 'valor', 'percentual_exito', 'dia_vencimento',
        'data_inicio', 'data_fim', 'status',
        'arquivo', 'arquivo_original',
        'validado', 'validado_em', 'validado_por',
    ];

    protected $casts = [
        'data_inicio'     => 'date',
        'data_fim'        => 'date',
        'validado'        => 'boolean',
        'validado_em'     => 'datetime',
        'valor'           => 'decimal:2',
        'percentual_exito'=> 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function advogadoResponsavel(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'advogado_responsavel_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function servicos(): HasMany
    {
        return $this->hasMany(ContratoServico::class, 'contrato_id');
    }

    public function repasses(): HasMany
    {
        return $this->hasMany(ContratoRepasse::class, 'contrato_id');
    }

    // Labels legíveis
    public static function tiposLabels(): array
    {
        return [
            'honorario_processo' => 'Honorário de Processo',
            'consultivo'         => 'Consultivo (Mensal)',
            'avulso'             => 'Serviço Avulso',
        ];
    }

    public static function formasLabels(): array
    {
        return [
            'parcelado'         => 'Parcelado',
            'mensal_recorrente' => 'Mensal Recorrente',
            'exito'             => 'Êxito (%)',
            'avulso'            => 'Avulso (único)',
        ];
    }

    public function getTipoLabelAttribute(): string
    {
        return self::tiposLabels()[$this->tipo] ?? $this->tipo;
    }

    public function getFormaLabelAttribute(): string
    {
        return self::formasLabels()[$this->forma_cobranca] ?? $this->forma_cobranca;
    }
}
