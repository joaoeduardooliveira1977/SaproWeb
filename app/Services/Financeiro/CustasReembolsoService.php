<?php

namespace App\Services\Financeiro;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustasReembolsoService
{
    public function listar(
        ?int   $processoId = null,
        string $situacao   = 'todos',
        int    $limite     = 50,
    ): Collection {
        $query = DB::table('vw_custas_reembolsaveis');

        if ($processoId) {
            $query->where('processo_id', $processoId);
        }
        if ($situacao !== 'todos') {
            $query->where('situacao_reembolso', $situacao);
        }

        return $query->orderByDesc('data_pagamento')->limit($limite)->get();
    }

    public function totalPorProcesso(int $processoId): float
    {
        return (float) DB::table('vw_custas_reembolsaveis')
            ->where('processo_id', $processoId)
            ->where('situacao_reembolso', 'pendente_cobranca')
            ->sum('valor_pago');
    }

    public function gerarReembolso(int $pagamentoId, ?string $observacao = null): int
    {
        return DB::transaction(function () use ($pagamentoId, $observacao) {
            $pagamento = DB::table('pagamentos')
                ->where('id', $pagamentoId)
                ->where('reembolsavel', true)
                ->where('reembolso_gerado', false)
                ->first();

            if (! $pagamento) {
                throw new \RuntimeException('Custa não encontrada ou reembolso já gerado.');
            }

            $origemId = DB::table('origens_recebimento')
                ->where('descricao', 'Reembolso de Custas')
                ->value('id');

            if (! $origemId) {
                $origemId = DB::table('origens_recebimento')->insertGetId([
                    'descricao'  => 'Reembolso de Custas',
                    'ativo'      => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $descricao = 'Reembolso: ' . $pagamento->descricao;
            if ($observacao) {
                $descricao .= " — {$observacao}";
            }

            $recebimentoId = DB::table('recebimentos')->insertGetId([
                'processo_id'    => $pagamento->processo_id,
                'origem_id'      => $origemId,
                'data'           => now()->toDateString(),
                'descricao'      => $descricao,
                'valor'          => $pagamento->valor_pago ?: $pagamento->valor,
                'valor_recebido' => 0,
                'recebido'       => false,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            DB::table('pagamentos')->where('id', $pagamentoId)->update([
                'reembolso_gerado'         => true,
                'recebimento_reembolso_id' => $recebimentoId,
                'updated_at'               => now(),
            ]);

            return $recebimentoId;
        });
    }

    public function gerarReembolsoCompleto(int $processoId): int
    {
        $pendentes = $this->listar($processoId, 'pendente_cobranca');
        $count = 0;
        foreach ($pendentes as $custa) {
            $this->gerarReembolso($custa->id);
            $count++;
        }
        return $count;
    }
}
