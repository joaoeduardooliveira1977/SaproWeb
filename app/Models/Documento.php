<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class Documento extends Model
{
    use BelongsToTenant;

    protected $table = 'documentos';

    protected $fillable = [
        'processo_id', 'cliente_id', 'tipo', 'titulo', 'descricao',
        'arquivo', 'arquivo_original', 'mime_type', 'tamanho',
        'data_documento', 'uploaded_by',
    ];

    protected $casts = [
        'data_documento' => 'date',
        'tamanho'        => 'integer',
    ];

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }
}
