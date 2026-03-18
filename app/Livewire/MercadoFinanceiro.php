<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\{Http, Cache};

class MercadoFinanceiro extends Component
{
    public array  $cotacoes  = [];
    public array  $bovespa   = [];
    public ?string $atualizadoEm = null;
    public bool   $erro      = false;

    public function mount(): void
    {
        $this->carregar();
    }

    public function carregar(): void
    {
        $this->erro = false;

        $dados = Cache::remember('mercado_financeiro', 600, function () {
            $moedas  = $this->buscarMoedas();
            $bovespa = $this->buscarBovespa();
            Cache::put('mercado_financeiro_ts', now()->format('H:i'), 660);
            return compact('moedas', 'bovespa');
        });

        $this->cotacoes     = $dados['moedas'];
        $this->bovespa      = $dados['bovespa'];
        $this->atualizadoEm = Cache::get('mercado_financeiro_ts');

        if (empty($this->cotacoes) && empty($this->bovespa)) {
            $this->erro = true;
        }
    }

    public function refresh(): void
    {
        Cache::forget('mercado_financeiro');
        Cache::forget('mercado_financeiro_ts');
        $this->carregar();
    }

    // ── APIs ──────────────────────────────────────────────────────

   
private function buscarMoedas(): array
{
    try {
        $hoje  = now()->format('m-d-Y');
        $ontem = now()->subDays(2)->format('m-d-Y');

        $buscar = function ($moeda) use ($hoje, $ontem) {
            $res = Http::timeout(8)->get(
                "https://olinda.bcb.gov.br/olinda/servico/PTAX/versao/v1/odata/" .
                "CotacaoMoedaPeriodo(moeda=@moeda,dataInicial=@di,dataFinalCotacao=@df)" .
                "?@moeda='{$moeda}'&@di='{$ontem}'&@df='{$hoje}'" .
                "&\$top=1&\$orderby=dataHoraCotacao%20desc&\$format=json"
            );
            return $res->json('value.0') ?? [];
        };

        $usd = $buscar('USD');
        $eur = $buscar('EUR');
        $gbp = $buscar('GBP');

        return [
            [
                'sigla'    => 'USD',
                'nome'     => 'Dólar',
                'icone'    => '🇺🇸',
                'bid'      => (float) ($usd['cotacaoVenda']   ?? 0),
                'variacao' => 0,
                'max'      => (float) ($usd['cotacaoVenda']   ?? 0),
                'min'      => (float) ($usd['cotacaoCompra']  ?? 0),
            ],
            [
                'sigla'    => 'EUR',
                'nome'     => 'Euro',
                'icone'    => '🇪🇺',
                'bid'      => (float) ($eur['cotacaoVenda']   ?? 0),
                'variacao' => 0,
                'max'      => (float) ($eur['cotacaoVenda']   ?? 0),
                'min'      => (float) ($eur['cotacaoCompra']  ?? 0),
            ],
            [
                'sigla'    => 'GBP',
                'nome'     => 'Libra',
                'icone'    => '🇬🇧',
                'bid'      => (float) ($gbp['cotacaoVenda']   ?? 0),
                'variacao' => 0,
                'max'      => (float) ($gbp['cotacaoVenda']   ?? 0),
                'min'      => (float) ($gbp['cotacaoCompra']  ?? 0),
            ],
        ];
    } catch (\Throwable) {
        return [];
    }
}



private function buscarBovespa(): array
    {
        $res = Http::timeout(8)->get('https://brapi.dev/api/quote/%5EBVSP');

        if (!$res->ok()) return [];

        $q = $res->json('results.0') ?? [];

        return [
            'valor'    => (float) ($q['regularMarketPrice']            ?? 0),
            'variacao' => (float) ($q['regularMarketChangePercent']    ?? 0),
            'max'      => (float) ($q['regularMarketDayHigh']          ?? 0),
            'min'      => (float) ($q['regularMarketDayLow']           ?? 0),
        ];
    }




    public function render()
    {
        return view('livewire.mercado-financeiro');
    }
}
