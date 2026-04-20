<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContratoServico extends Model
{
    protected $table = 'contrato_servicos';

    protected $fillable = [
        'contrato_id', 'processo_id', 'descricao', 'tipo',
        'valor', 'percentual', 'status', 'observacoes',
    ];

    protected $casts = [
        'valor'      => 'decimal:2',
        'percentual' => 'decimal:2',
    ];

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public static function tiposLabels(): array
    {
        return [
            'honorario'   => 'Parcela / Honorário Fixo',
            'consultoria' => 'Mensalidade / Assessoria',
            'exito'       => 'Êxito (%)',
            'avulso'      => 'Serviço Avulso',
            'repasse'     => 'Repasse',
            'outro'       => 'Outro Ajuste',
        ];
    }
}
