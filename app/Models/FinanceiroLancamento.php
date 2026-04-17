<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\BelongsToTenant;

class FinanceiroLancamento extends Model
{
    use BelongsToTenant;

    protected $table = 'financeiro_lancamentos';

    protected $fillable = [
        'tenant_id', 'cliente_id', 'contrato_id', 'contrato_servico_id', 'processo_id',
        'tipo', 'descricao', 'valor', 'vencimento', 'data_pagamento', 'valor_pago',
        'status', 'forma_pagamento', 'observacoes', 'numero_parcela', 'total_parcelas',
    ];

    protected $casts = [
        'vencimento'     => 'date',
        'data_pagamento' => 'date',
        'valor'          => 'decimal:2',
        'valor_pago'     => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'contrato_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    // ── Helpers ────────────────────────────────────────────────

    public function isAtrasado(): bool
    {
        return $this->status === 'previsto' && $this->vencimento->isPast();
    }

    /** Atualiza status para "atrasado" se vencido e ainda previsto */
    public static function atualizarAtrasados(): void
    {
        static::where('status', 'previsto')
            ->where('vencimento', '<', now()->toDateString())
            ->update(['status' => 'atrasado', 'updated_at' => now()]);
    }

    /** Gera lançamentos a partir de um contrato */
    public static function gerarDoContrato(Contrato $contrato): void
    {
        $tenantId  = $contrato->tenant_id;
        $clienteId = $contrato->cliente_id;
        $base = [
            'tenant_id'   => $tenantId,
            'cliente_id'  => $clienteId,
            'contrato_id' => $contrato->id,
            'tipo'        => 'receita',
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        match ($contrato->forma_cobranca) {
            'mensal_recorrente' => static::gerarMensais($contrato, $base),
            'parcelado'         => static::gerarParcelas($contrato, $base),
            'avulso', 'exito'   => static::gerarUnico($contrato, $base),
            default             => null,
        };
    }

    private static function gerarMensais(Contrato $contrato, array $base): void
    {
        // Gera 12 meses a partir do início (ou até data_fim)
        $dia   = $contrato->dia_vencimento ?? 10;
        $inicio = $contrato->data_inicio->copy()->day($dia);
        $fim    = $contrato->data_fim ?? $inicio->copy()->addMonths(11);
        $mes    = $inicio->copy();
        $n      = 1;

        while ($mes->lte($fim)) {
            \Illuminate\Support\Facades\DB::table('financeiro_lancamentos')->insert(array_merge($base, [
                'descricao'      => $contrato->descricao . " — {$mes->format('m/Y')}",
                'valor'          => $contrato->valor,
                'vencimento'     => $mes->format('Y-m-d'),
                'status'         => 'previsto',
                'numero_parcela' => $n++,
            ]));
            $mes->addMonthNoOverflow();
        }
    }

    private static function gerarParcelas(Contrato $contrato, array $base): void
    {
        // Usa total_parcelas dos serviços ou padrão 1
        $total = max(1, $contrato->servicos()->count() ?: 1);
        $valorParcela = round($contrato->valor / $total, 2);
        $venc = $contrato->data_inicio->copy();

        for ($i = 1; $i <= $total; $i++) {
            \Illuminate\Support\Facades\DB::table('financeiro_lancamentos')->insert(array_merge($base, [
                'descricao'      => $contrato->descricao . " — Parcela {$i}/{$total}",
                'valor'          => $valorParcela,
                'vencimento'     => $venc->format('Y-m-d'),
                'status'         => 'previsto',
                'numero_parcela' => $i,
                'total_parcelas' => $total,
            ]));
            $venc->addMonthNoOverflow();
        }
    }

    private static function gerarUnico(Contrato $contrato, array $base): void
    {
        \Illuminate\Support\Facades\DB::table('financeiro_lancamentos')->insert(array_merge($base, [
            'descricao'  => $contrato->descricao,
            'valor'      => $contrato->valor,
            'vencimento' => $contrato->data_inicio->format('Y-m-d'),
            'status'     => 'previsto',
        ]));
    }
}
