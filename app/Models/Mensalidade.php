<?php

namespace App\Models;

use App\Mail\CobrancaRecebidoConfirmacao;
use App\Models\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Mensalidade extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'mensalidades';

    protected $fillable = [
        'tenant_id', 'contrato_mensal_id', 'cliente_id',
        'financeiro_lancamento_id',
        'competencia', 'vencimento', 'valor', 'valor_recebido', 'status',
        'data_recebimento', 'forma_pagamento',
        'notificado_5dias', 'notificado_3dias', 'notificado_vencimento', 'notificado_atraso',
        'observacoes',
    ];

    protected $casts = [
        'vencimento'          => 'date',
        'data_recebimento'    => 'date',
        'valor'               => 'decimal:2',
        'valor_recebido'      => 'decimal:2',
        'notificado_5dias'    => 'boolean',
        'notificado_3dias'    => 'boolean',
        'notificado_vencimento' => 'boolean',
        'notificado_atraso'   => 'boolean',
    ];

    public function contratoMensal(): BelongsTo
    {
        return $this->belongsTo(ContratoMensal::class, 'contrato_mensal_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function lancamento(): BelongsTo
    {
        return $this->belongsTo(FinanceiroLancamento::class, 'financeiro_lancamento_id');
    }

    public function getCompetenciaFormatadaAttribute(): string
    {
        if (!$this->competencia) {
            return '';
        }
        [$ano, $mes] = explode('-', $this->competencia);
        $meses = ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
                       'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        return ($meses[(int) $mes] ?? $mes) . '/' . $ano;
    }

    public function marcarRecebido(
        float  $valor,
        string $forma,
        Carbon $data,
        float  $juros    = 0,
        float  $multa    = 0,
        float  $desconto = 0
    ): void {
        $status = $valor >= (float) $this->valor ? 'recebido' : 'parcial';

        $this->update([
            'valor_recebido'  => $valor,
            'data_recebimento' => $data->toDateString(),
            'forma_pagamento' => $forma,
            'status'          => $status,
        ]);

        // Confirmação por e-mail se o contrato tiver envio automático ativado
        $this->loadMissing(['cliente', 'contratoMensal']);
        if (!empty($this->cliente?->email) && $this->contratoMensal?->envio_automatico) {
            try {
                Mail::to($this->cliente->email)
                    ->send(new CobrancaRecebidoConfirmacao($this));
            } catch (\Exception $e) {
                Log::error("Falha e-mail confirmação mensalidade #{$this->id}: {$e->getMessage()}");
            }
        }

        // Atualiza o lançamento financeiro vinculado
        if ($this->financeiro_lancamento_id) {
            FinanceiroLancamento::withoutGlobalScopes()
                ->where('id', $this->financeiro_lancamento_id)
                ->update([
                    'valor_pago'     => $valor,
                    'data_pagamento' => $data->toDateString(),
                    'juros'          => $juros,
                    'multa'          => $multa,
                    'desconto'       => $desconto,
                    'valor_liquido'  => $valor + $juros + $multa - $desconto,
                    'status'         => $status,
                    'forma_pagamento' => $forma,
                    'updated_at'     => now(),
                ]);
        }
    }
}
