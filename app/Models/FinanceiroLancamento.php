<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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

    public function servico(): BelongsTo
    {
        return $this->belongsTo(ContratoServico::class, 'contrato_servico_id');
    }

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class, 'processo_id');
    }

    public function isAtrasado(): bool
    {
        return $this->status === 'previsto' && $this->vencimento->isPast();
    }

    public static function atualizarAtrasados(): void
    {
        static::where('status', 'previsto')
            ->where('vencimento', '<', now()->toDateString())
            ->update(['status' => 'atrasado', 'updated_at' => now()]);
    }

    public static function gerarDoContrato(Contrato $contrato): void
    {
        $contrato->loadMissing('servicos');

        if ($contrato->servicos->isNotEmpty()) {
            static::gerarDosServicos($contrato);
            return;
        }

        $base = [
            'tenant_id'   => $contrato->tenant_id,
            'cliente_id'  => $contrato->cliente_id,
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

    public static function sincronizarServico(ContratoServico $servico): void
    {
        $servico->loadMissing('contrato');

        DB::table('financeiro_lancamentos')
            ->where('contrato_servico_id', $servico->id)
            ->whereIn('status', ['previsto', 'atrasado'])
            ->delete();

        if (!static::servicoGeraFinanceiro($servico) || !$servico->contrato) {
            return;
        }

        static::gerarLancamentosDoServico($servico, $servico->contrato);
    }

    private static function gerarDosServicos(Contrato $contrato): void
    {
        DB::table('financeiro_lancamentos')
            ->where('contrato_id', $contrato->id)
            ->whereNull('contrato_servico_id')
            ->where('tipo', 'receita')
            ->whereIn('status', ['previsto', 'atrasado'])
            ->delete();

        foreach ($contrato->servicos as $servico) {
            static::sincronizarServico($servico);
        }
    }

    private static function servicoGeraFinanceiro(ContratoServico $servico): bool
    {
        if (in_array($servico->tipo, ['exito', 'repasse'], true)) {
            return false;
        }

        return (float) $servico->valor > 0 && !empty($servico->vencimento);
    }

    private static function gerarLancamentosDoServico(ContratoServico $servico, Contrato $contrato): void
    {
        $parcelas   = max(1, (int) ($servico->numero_parcelas ?? 1));
        $valorBase  = round((float) $servico->valor, 2);
        $valorParc  = round($valorBase / $parcelas, 2);
        $vencimento = \Carbon\Carbon::parse($servico->vencimento);

        for ($i = 1; $i <= $parcelas; $i++) {
            $valorAtual = $i === $parcelas
                ? round($valorBase - ($valorParc * ($parcelas - 1)), 2)
                : $valorParc;

            DB::table('financeiro_lancamentos')->insert([
                'tenant_id'           => $contrato->tenant_id,
                'cliente_id'          => $contrato->cliente_id,
                'contrato_id'         => $contrato->id,
                'contrato_servico_id' => $servico->id,
                'processo_id'         => $servico->processo_id,
                'tipo'                => 'receita',
                'descricao'           => $parcelas > 1
                    ? "{$servico->descricao} ({$i}/{$parcelas})"
                    : $servico->descricao,
                'valor'               => $valorAtual,
                'vencimento'          => $vencimento->copy()->addMonths($i - 1)->format('Y-m-d'),
                'status'              => 'previsto',
                'numero_parcela'      => $parcelas > 1 ? $i : null,
                'total_parcelas'      => $parcelas > 1 ? $parcelas : null,
                'observacoes'         => $servico->observacoes,
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        }
    }

    private static function gerarMensais(Contrato $contrato, array $base): void
    {
        $dia    = $contrato->dia_vencimento ?? 10;
        $inicio = $contrato->data_inicio->copy()->day($dia);
        $fim    = $contrato->data_fim ?? $inicio->copy()->addMonths(11);
        $mes    = $inicio->copy();
        $n      = 1;

        while ($mes->lte($fim)) {
            DB::table('financeiro_lancamentos')->insert(array_merge($base, [
                'descricao'      => $contrato->descricao . ' - ' . $mes->format('m/Y'),
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
        $total        = max(1, $contrato->servicos()->count() ?: 1);
        $valorParcela = round($contrato->valor / $total, 2);
        $vencimento   = $contrato->data_inicio->copy();

        for ($i = 1; $i <= $total; $i++) {
            $valorAtual = $i === $total
                ? round($contrato->valor - ($valorParcela * ($total - 1)), 2)
                : $valorParcela;

            DB::table('financeiro_lancamentos')->insert(array_merge($base, [
                'descricao'      => $contrato->descricao . " - Parcela {$i}/{$total}",
                'valor'          => $valorAtual,
                'vencimento'     => $vencimento->format('Y-m-d'),
                'status'         => 'previsto',
                'numero_parcela' => $i,
                'total_parcelas' => $total,
            ]));

            $vencimento->addMonthNoOverflow();
        }
    }

    private static function gerarUnico(Contrato $contrato, array $base): void
    {
        DB::table('financeiro_lancamentos')->insert(array_merge($base, [
            'descricao'  => $contrato->descricao,
            'valor'      => $contrato->valor,
            'vencimento' => $contrato->data_inicio->format('Y-m-d'),
            'status'     => 'previsto',
        ]));
    }
}
