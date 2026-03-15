<?php

namespace App\Livewire;

use App\Models\IndiceMonetario;
use Carbon\Carbon;
use Livewire\Component;

class CalculadoraJuridica extends Component
{
    // ── Entradas ─────────────────────────────────────────────────
    public string $valorOriginal      = '';
    public string $dataInicio         = '';
    public string $dataFim            = '';
    public string $indiceCorrecao     = 'IPCA';   // IPCA, IGPM, TR, SELIC, nenhum
    public string $tipoJuros          = 'mensal'; // mensal, selic, nenhum
    public string $percentualJuros    = '1';
    public string $percentualMulta    = '0';
    public string $percentualHonorarios = '0';
    public string $processoRef        = '';
    public bool   $mostrarDetalhes    = false;

    // ── Resultados ───────────────────────────────────────────────
    public bool    $calculado         = false;
    public array   $resultado         = [];
    public array   $detalhes          = [];
    public string  $erroCalculo       = '';

    // ── Índices disponíveis no banco ─────────────────────────────
    public array $indicesDisponiveis  = [];

    public function mount(): void
    {
        $this->dataInicio = now()->subYear()->format('Y-m-d');
        $this->dataFim    = now()->format('Y-m-d');
        $this->carregarIndicesDisponiveis();
    }

    private function carregarIndicesDisponiveis(): void
    {
        $rows = IndiceMonetario::selectRaw('sigla, MIN(mes_ref) as de, MAX(mes_ref) as ate')
            ->groupBy('sigla')
            ->orderBy('sigla')
            ->get();

        $this->indicesDisponiveis = $rows->map(fn($r) => [
            'sigla' => $r->sigla,
            'de'    => Carbon::parse($r->de)->format('m/Y'),
            'ate'   => Carbon::parse($r->ate)->format('m/Y'),
        ])->toArray();
    }

    public function calcular(): void
    {
        $this->erroCalculo = '';
        $this->resultado   = [];
        $this->detalhes    = [];

        // Validações básicas
        $valor = str_replace(['.', ','], ['', '.'], $this->valorOriginal);
        $valor = (float) $valor;
        if ($valor <= 0) {
            $this->erroCalculo = 'Informe um valor original válido.';
            return;
        }
        if (!$this->dataInicio || !$this->dataFim) {
            $this->erroCalculo = 'Informe as datas de início e fim.';
            return;
        }

        $dataIni = Carbon::parse($this->dataInicio)->startOfMonth();
        $dataFim = Carbon::parse($this->dataFim)->startOfMonth();

        if ($dataFim <= $dataIni) {
            $this->erroCalculo = 'A data final deve ser posterior à data inicial.';
            return;
        }

        $meses = $dataIni->diffInMonths($dataFim);

        // ── Correção monetária ───────────────────────────────────
        $fatorCorrecao  = 1.0;
        $indicesUsados  = 0;

        if ($this->indiceCorrecao !== 'nenhum') {
            $indices = IndiceMonetario::where('sigla', $this->indiceCorrecao)
                ->where('mes_ref', '>=', $dataIni)
                ->where('mes_ref', '<=', $dataFim)
                ->orderBy('mes_ref')
                ->get();

            $acumulado = 1.0;
            foreach ($indices as $idx) {
                $acumulado *= (1 + $idx->percentual / 100);
                $this->detalhes[] = [
                    'mes'        => Carbon::parse($idx->mes_ref)->format('m/Y'),
                    'sigla'      => $idx->sigla,
                    'percentual' => $idx->percentual,
                    'fator_acum' => round($acumulado, 8),
                ];
                $indicesUsados++;
            }

            $fatorCorrecao = $acumulado;

            if ($indicesUsados === 0) {
                $this->erroCalculo = "Nenhum índice {$this->indiceCorrecao} encontrado para o período. "
                    . "Verifique os índices cadastrados em Administração → Índices.";
                return;
            }
        }

        $valorCorrigido = $valor * $fatorCorrecao;
        $correcaoReais  = $valorCorrigido - $valor;
        $correcaoPct    = ($fatorCorrecao - 1) * 100;

        // ── Juros moratórios ─────────────────────────────────────
        $jurosReais = 0.0;
        $jurosPct   = 0.0;
        $jurosDesc  = '';

        if ($this->tipoJuros === 'mensal') {
            $taxa       = (float) str_replace(',', '.', $this->percentualJuros) / 100;
            $jurosReais = $valorCorrigido * $taxa * $meses;
            $jurosPct   = $taxa * $meses * 100;
            $jurosDesc  = number_format((float) $this->percentualJuros, 2, ',', '.') . "% a.m. × {$meses} meses (simples)";

        } elseif ($this->tipoJuros === 'selic') {
            $selicRows = IndiceMonetario::where('sigla', 'SELIC')
                ->where('mes_ref', '>=', $dataIni)
                ->where('mes_ref', '<=', $dataFim)
                ->orderBy('mes_ref')
                ->get();

            $fatorSelic = 1.0;
            foreach ($selicRows as $s) {
                $fatorSelic *= (1 + $s->percentual / 100);
            }
            $jurosReais = $valorCorrigido * ($fatorSelic - 1);
            $jurosPct   = ($fatorSelic - 1) * 100;
            $jurosDesc  = 'SELIC acumulada (' . $selicRows->count() . ' meses)';

            if ($selicRows->isEmpty()) {
                $jurosDesc = 'SELIC — nenhum índice cadastrado para o período';
            }
        }

        // ── Multa ────────────────────────────────────────────────
        $pctMulta   = (float) str_replace(',', '.', $this->percentualMulta);
        $multaReais = $valor * ($pctMulta / 100); // multa sobre valor original

        // ── Honorários advocatícios ──────────────────────────────
        $pctHonorarios = (float) str_replace(',', '.', $this->percentualHonorarios);
        $subtotal      = $valorCorrigido + $jurosReais + $multaReais;
        $honorariosReais = $subtotal * ($pctHonorarios / 100);

        $total = $subtotal + $honorariosReais;

        // ── Montar resultado ─────────────────────────────────────
        $this->resultado = [
            'valor_original'    => $valor,
            'periodo_meses'     => $meses,
            'data_ini_fmt'      => Carbon::parse($this->dataInicio)->format('d/m/Y'),
            'data_fim_fmt'      => Carbon::parse($this->dataFim)->format('d/m/Y'),
            'indice'            => $this->indiceCorrecao !== 'nenhum' ? $this->indiceCorrecao : 'Sem correção',
            'indices_usados'    => $indicesUsados,
            'fator_correcao'    => $fatorCorrecao,
            'correcao_reais'    => $correcaoReais,
            'correcao_pct'      => $correcaoPct,
            'valor_corrigido'   => $valorCorrigido,
            'juros_reais'       => $jurosReais,
            'juros_pct'         => $jurosPct,
            'juros_desc'        => $jurosDesc,
            'multa_reais'       => $multaReais,
            'multa_pct'         => $pctMulta,
            'subtotal'          => $subtotal,
            'honorarios_reais'  => $honorariosReais,
            'honorarios_pct'    => $pctHonorarios,
            'total'             => $total,
            'processo_ref'      => $this->processoRef,
            'gerado_em'         => now()->format('d/m/Y H:i'),
        ];

        $this->calculado = true;
    }

    public function limpar(): void
    {
        $this->calculado          = false;
        $this->resultado          = [];
        $this->detalhes           = [];
        $this->erroCalculo        = '';
        $this->valorOriginal      = '';
        $this->processoRef        = '';
        $this->percentualMulta    = '0';
        $this->percentualHonorarios = '0';
        $this->dataInicio         = now()->subYear()->format('Y-m-d');
        $this->dataFim            = now()->format('Y-m-d');
        $this->indiceCorrecao     = 'IPCA';
        $this->tipoJuros          = 'mensal';
        $this->percentualJuros    = '1';
        $this->mostrarDetalhes    = false;
    }

    public function render()
    {
        return view('livewire.calculadora-juridica');
    }
}
