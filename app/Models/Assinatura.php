<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assinatura extends Model
{
    protected $table = 'assinaturas';

    protected $fillable = [
        'documento_id', 'processo_id', 'criado_por',
        'titulo', 'descricao', 'arquivo_path', 'arquivo_nome',
        'clicksign_document_key', 'clicksign_list_key',
        'status', 'deadline_at', 'enviado_em', 'concluido_em', 'erro_mensagem',
    ];

    protected $casts = [
        'deadline_at'  => 'datetime',
        'enviado_em'   => 'datetime',
        'concluido_em' => 'datetime',
    ];

    public function documento(): BelongsTo
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'criado_por');
    }

    public function signatarios(): HasMany
    {
        return $this->hasMany(AssinaturaSignatario::class, 'assinatura_id')->orderBy('ordem');
    }

    public static function statusLabel(): array
    {
        return [
            'rascunho'  => 'Rascunho',
            'enviado'   => 'Enviado',
            'assinando' => 'Aguardando Assinaturas',
            'concluido' => 'Concluído',
            'recusado'  => 'Recusado',
            'cancelado' => 'Cancelado',
            'erro'      => 'Erro',
        ];
    }

    public function statusCor(): string
    {
        return match($this->status) {
            'rascunho'  => 'bg-gray-100 text-gray-600 border-gray-200',
            'enviado'   => 'bg-blue-100 text-blue-700 border-blue-200',
            'assinando' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
            'concluido' => 'bg-green-100 text-green-700 border-green-200',
            'recusado'  => 'bg-red-100 text-red-700 border-red-200',
            'cancelado' => 'bg-gray-100 text-gray-400 border-gray-200',
            'erro'      => 'bg-red-100 text-red-800 border-red-300',
            default     => 'bg-gray-100 text-gray-500 border-gray-200',
        };
    }

    public function podeEnviar(): bool
    {
        return $this->status === 'rascunho' && $this->signatarios()->exists();
    }

    public function podeCancelar(): bool
    {
        return in_array($this->status, ['enviado', 'assinando'])
            && !empty($this->clicksign_list_key);
    }

    public function totalAssinado(): int
    {
        return $this->signatarios()->where('status', 'assinado')->count();
    }
}
