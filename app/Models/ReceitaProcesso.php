<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceitaProcesso extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'receitas_processos';

    protected $fillable = [
        'tenant_id', 'cliente_id', 'valor_unitario',
        'dia_vencimento', 'data_inicio', 'status', 'observacoes',
    ];

    protected $casts = [
        'data_inicio'   => 'date',
        'valor_unitario' => 'decimal:2',
        'dia_vencimento' => 'integer',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function calcularValorMes(): float
    {
        $quantidade = Processo::where('cliente_id', $this->cliente_id)
            ->where('tenant_id', $this->tenant_id)
            ->where('status', 'Ativo')
            ->count();

        return (float) $this->valor_unitario * $quantidade;
    }

    public function gerarMensalidadeDoMes(): ?FinanceiroLancamento
    {
        $valor = $this->calcularValorMes();

        if ($valor <= 0) {
            return null;
        }

        $hoje   = now();
        $dia    = min($this->dia_vencimento, $hoje->daysInMonth);
        $mesAno = $hoje->format('m/Y');

        return FinanceiroLancamento::create([
            'tenant_id'           => $this->tenant_id,
            'cliente_id'          => $this->cliente_id,
            'receita_processo_id' => $this->id,
            'tipo'                => 'receita',
            'origem_tipo'         => 'processo',
            'descricao'           => "Honorários por Processos — {$mesAno}",
            'valor'               => $valor,
            'vencimento'          => $hoje->copy()->setDay($dia)->toDateString(),
            'status'              => 'previsto',
        ]);
    }
}
