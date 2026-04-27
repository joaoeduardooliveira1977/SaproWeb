<?php

namespace App\Services\Financeiro;

use App\Models\{Comissao, Recebimento, HonorarioParcela, Pessoa};
use Carbon\Carbon;

class ComissaoService
{
    public function gerarParaRecebimento(Recebimento $recebimento): void
    {
        if (!$recebimento->recebido || !$recebimento->processo_id) {
            return;
        }

        $processo = $recebimento->processo;
        if (!$processo) return;

        $pessoa = Pessoa::find($processo->cliente_id);
        if (!$pessoa?->indicador_id) return;

        $indicador = $pessoa->indicador;
        if (!$indicador?->ativo || $indicador->percentual_comissao <= 0) return;

        $base = (float) ($recebimento->valor_recebido ?: $recebimento->valor);
        if ($base <= 0) return;

        Comissao::firstOrCreate(
            ['origem_tipo' => 'recebimento', 'origem_id' => $recebimento->id],
            [
                'tenant_id'      => $pessoa->tenant_id,
                'indicador_id'   => $indicador->id,
                'pessoa_id'      => $pessoa->id,
                'valor_base'     => $base,
                'percentual'     => $indicador->percentual_comissao,
                'valor_comissao' => round($base * $indicador->percentual_comissao / 100, 2),
                'competencia'    => Carbon::parse($recebimento->data_recebimento ?? $recebimento->data)->startOfMonth(),
                'status'         => 'pendente',
            ]
        );
    }

    public function gerarParaHonorario(HonorarioParcela $parcela): void
    {
        if ($parcela->status !== 'pago') return;

        $honorario = $parcela->honorario;
        if (!$honorario?->cliente_id) return;

        $pessoa = Pessoa::find($honorario->cliente_id);
        if (!$pessoa?->indicador_id) return;

        $indicador = $pessoa->indicador;
        if (!$indicador?->ativo || $indicador->percentual_comissao <= 0) return;

        $base = (float) ($parcela->valor_pago ?: $parcela->valor);
        if ($base <= 0) return;

        Comissao::firstOrCreate(
            ['origem_tipo' => 'honorario_parcela', 'origem_id' => $parcela->id],
            [
                'tenant_id'      => $pessoa->tenant_id,
                'indicador_id'   => $indicador->id,
                'pessoa_id'      => $pessoa->id,
                'valor_base'     => $base,
                'percentual'     => $indicador->percentual_comissao,
                'valor_comissao' => round($base * $indicador->percentual_comissao / 100, 2),
                'competencia'    => Carbon::parse($parcela->data_pagamento ?? now())->startOfMonth(),
                'status'         => 'pendente',
            ]
        );
    }

    public function marcarPago(int $comissaoId, string $dataPagamento): void
    {
        Comissao::where('id', $comissaoId)->update([
            'status'         => 'pago',
            'data_pagamento' => $dataPagamento,
        ]);
    }

    public function marcarPagoEmLote(array $ids, string $dataPagamento): void
    {
        Comissao::whereIn('id', $ids)->update([
            'status'         => 'pago',
            'data_pagamento' => $dataPagamento,
        ]);
    }
}
