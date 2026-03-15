<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ProdutividadeAdvogado extends Component
{
    public string $periodo    = 'mes';      // mes, mes_anterior, trimestre, semestre, ano, custom
    public string $dataIni    = '';
    public string $dataFim    = '';

    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 4, 'blocks' => 2, 'blockHeight' => 320]);
    }

    public function mount(): void
    {
        $this->dataIni = now()->startOfMonth()->format('Y-m-d');
        $this->dataFim = now()->format('Y-m-d');
    }

    public function updatedPeriodo(): void
    {
        if ($this->periodo !== 'custom') {
            [$this->dataIni, $this->dataFim] = $this->calcularPeriodo();
        }
    }

    private function calcularPeriodo(): array
    {
        return match ($this->periodo) {
            'mes'           => [now()->startOfMonth()->format('Y-m-d'),         now()->format('Y-m-d')],
            'mes_anterior'  => [now()->subMonth()->startOfMonth()->format('Y-m-d'), now()->subMonth()->endOfMonth()->format('Y-m-d')],
            'trimestre'     => [now()->subMonths(3)->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
            'semestre'      => [now()->subMonths(6)->startOfMonth()->format('Y-m-d'), now()->format('Y-m-d')],
            'ano'           => [now()->startOfYear()->format('Y-m-d'),           now()->format('Y-m-d')],
            default         => [$this->dataIni, $this->dataFim],
        };
    }

    private function dados(): array
    {
        [$ini, $fim] = $this->periodo === 'custom'
            ? [$this->dataIni, $this->dataFim]
            : $this->calcularPeriodo();

        // Métricas por advogado (subqueries para evitar inflação de contagens)
        $advogados = DB::select("
            SELECT
                pe.id,
                pe.nome,
                pe.oab,
                pe.email,

                -- Processos
                (SELECT COUNT(*) FROM processos WHERE advogado_id = pe.id AND status = 'Ativo') AS processos_ativos,
                (SELECT COUNT(*) FROM processos WHERE advogado_id = pe.id) AS processos_total,

                -- Apontamentos no período
                (SELECT COUNT(*) FROM apontamentos
                 WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_apontamentos,
                (SELECT COALESCE(SUM(horas), 0) FROM apontamentos
                 WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_horas,
                (SELECT COALESCE(SUM(valor), 0) FROM apontamentos
                 WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_valor,

                -- Andamentos lançados no período (pelo usuário vinculado)
                (SELECT COUNT(*) FROM andamentos an
                 JOIN usuarios u ON u.id = an.usuario_id
                 WHERE u.pessoa_id = pe.id AND an.data BETWEEN ? AND ?) AS total_andamentos,

                -- Prazos (todos os tempos, pelo usuário vinculado)
                (SELECT COUNT(*) FROM prazos pz
                 JOIN usuarios u ON u.id = pz.responsavel_id
                 WHERE u.pessoa_id = pe.id) AS prazos_total,
                (SELECT COUNT(*) FROM prazos pz
                 JOIN usuarios u ON u.id = pz.responsavel_id
                 WHERE u.pessoa_id = pe.id AND pz.status = 'cumprido') AS prazos_cumpridos,
                (SELECT COUNT(*) FROM prazos pz
                 JOIN usuarios u ON u.id = pz.responsavel_id
                 WHERE u.pessoa_id = pe.id AND pz.status = 'perdido') AS prazos_perdidos,
                (SELECT COUNT(*) FROM prazos pz
                 JOIN usuarios u ON u.id = pz.responsavel_id
                 WHERE u.pessoa_id = pe.id AND pz.status = 'aberto'
                   AND pz.data_prazo < CURRENT_DATE) AS prazos_vencidos

            FROM pessoas pe
            JOIN pessoa_tipos pt ON pt.pessoa_id = pe.id AND pt.tipo = 'Advogado'
            WHERE pe.ativo = true
            ORDER BY pe.nome
        ", [$ini, $fim, $ini, $fim, $ini, $fim, $ini, $fim]);

        // Totais consolidados
        $totais = [
            'processos_ativos'   => array_sum(array_column($advogados, 'processos_ativos')),
            'total_horas'        => array_sum(array_column($advogados, 'total_horas')),
            'total_valor'        => array_sum(array_column($advogados, 'total_valor')),
            'total_andamentos'   => array_sum(array_column($advogados, 'total_andamentos')),
            'prazos_cumpridos'   => array_sum(array_column($advogados, 'prazos_cumpridos')),
            'prazos_perdidos'    => array_sum(array_column($advogados, 'prazos_perdidos')),
        ];

        return compact('advogados', 'totais', 'ini', 'fim');
    }

    public function render()
    {
        $dados = $this->dados();

        return view('livewire.produtividade-advogado', [
            'advogados' => $dados['advogados'],
            'totais'    => $dados['totais'],
            'dataIniFmt'=> Carbon::parse($dados['ini'])->format('d/m/Y'),
            'dataFimFmt'=> Carbon::parse($dados['fim'])->format('d/m/Y'),
        ]);
    }
}
