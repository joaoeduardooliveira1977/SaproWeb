<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Orcamento extends Model
{
    use BelongsToTenant;

    protected $table = 'orcamentos';

    protected $fillable = [
        'tenant_id', 'numero', 'oportunidade_id', 'pessoa_id',
        'nome_cliente', 'email_cliente', 'telefone_cliente',
        'titulo', 'area_direito', 'descricao', 'observacoes',
        'tipo_honorario', 'valor', 'parcelas', 'valor_parcela',
        'percentual_exito', 'valor_hora',
        'validade', 'status', 'data_resposta', 'motivo_recusa',
        'usuario_id',
    ];

    protected $casts = [
        'valor'            => 'float',
        'valor_parcela'    => 'float',
        'percentual_exito' => 'float',
        'valor_hora'       => 'float',
        'validade'         => 'date',
        'data_resposta'    => 'date',
    ];

    public static array $tiposHonorario = [
        'fixo'       => 'Honorário Fixo',
        'percentual' => 'Percentual do Êxito',
        'hora'       => 'Por Hora',
        'sucesso'    => 'Êxito + Fixo',
    ];

    public static array $statusLabels = [
        'rascunho' => 'Rascunho',
        'enviado'  => 'Enviado',
        'aceito'   => 'Aceito',
        'recusado' => 'Recusado',
        'expirado' => 'Expirado',
    ];

    public static array $statusCores = [
        'rascunho' => '#64748b',
        'enviado'  => '#2563a8',
        'aceito'   => '#16a34a',
        'recusado' => '#dc2626',
        'expirado' => '#9ca3af',
    ];

    public function oportunidade()
    {
        return $this->belongsTo(CrmOportunidade::class);
    }

    public function pessoa()
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public static function proximoNumero(int $tenantId): string
    {
        $ano    = now()->year;
        $ultimo = static::where('tenant_id', $tenantId)
            ->whereYear('created_at', $ano)
            ->max('numero');

        $seq = $ultimo ? ((int) substr($ultimo, -3)) + 1 : 1;
        return 'ORC-' . $ano . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function isExpirado(): bool
    {
        return $this->validade && $this->validade->isPast() && $this->status === 'enviado';
    }
}
