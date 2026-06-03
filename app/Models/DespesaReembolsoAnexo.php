<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DespesaReembolsoAnexo extends Model
{
    use BelongsToTenant;

    protected $table = 'despesas_reembolsos_anexos';

    protected $fillable = [
        'despesa_reembolso_id', 'tenant_id', 'nome_original',
        'caminho', 'tipo_documento', 'tamanho', 'mime_type', 'criado_por',
    ];

    public function despesa(): BelongsTo
    {
        return $this->belongsTo(DespesaReembolso::class, 'despesa_reembolso_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function getLabelTipoDocumentoAttribute(): string
    {
        return match ($this->tipo_documento) {
            'guia'        => 'Guia',
            'comprovante' => 'Comprovante',
            'nota_fiscal' => 'Nota Fiscal',
            'darf'        => 'DARF',
            'gru'         => 'GRU',
            'recibo'      => 'Recibo',
            default       => 'Outro',
        };
    }

    public function getTamanhoFormatadoAttribute(): string
    {
        $bytes = $this->tamanho ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
