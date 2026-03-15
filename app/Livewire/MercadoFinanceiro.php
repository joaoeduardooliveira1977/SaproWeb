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
            $res = Http::timeout(8)->get('https://economia.awesomeapi.com.br/json/last/USD-BRL,EUR-BRL,BTC-BRL');
            if (!$res->ok()) return [];
            $json = $res->json();
        } catch (\Throwable) {
            return [];
        }

        return [
            [
                'sigla'    => 'USD',
                'nome'     => 'Dólar',
                'icone'    => '🇺🇸',
                'bid'      => (float) ($json['USDBRL']['bid']       ?? 0),
                'variacao' => (float) ($json['USDBRL']['pctChange'] ?? 0),
                'max'      => (float) ($json['USDBRL']['high']      ?? 0),
                'min'      => (float) ($json['USDBRL']['low']       ?? 0),
            ],
            [
                'sigla'    => 'EUR',
                'nome'     => 'Euro',
                'icone'    => '🇪🇺',
                'bid'      => (float) ($json['EURBRL']['bid']       ?? 0),
                'variacao' => (float) ($json['EURBRL']['pctChange'] ?? 0),
                'max'      => (float) ($json['EURBRL']['high']      ?? 0),
                'min'      => (float) ($json['EURBRL']['low']       ?? 0),
            ],
            [
                'sigla'    => 'BTC',
                'nome'     => 'Bitcoin',
                'icone'    => '₿',
                'bid'      => (float) ($json['BTCBRL']['bid']       ?? 0),
                'variacao' => (float) ($json['BTCBRL']['pctChange'] ?? 0),
                'max'      => (float) ($json['BTCBRL']['high']      ?? 0),
                'min'      => (float) ($json['BTCBRL']['low']       ?? 0),
            ],
        ];
    }

    private function buscarBovespa(): array
    {
        try {
            $res = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->get('https://query1.finance.yahoo.com/v8/finance/chart/%5EBVSP');
            if (!$res->ok()) return [];
            $meta = $res->json('chart.result.0.meta') ?? [];
        } catch (\Throwable) {
            return [];
        }

        if (empty($meta['regularMarketPrice'])) return [];

        $preco    = (float) $meta['regularMarketPrice'];
        $anterior = (float) ($meta['previousClose'] ?? $meta['chartPreviousClose'] ?? $preco);
        $variacao = $anterior > 0 ? round(($preco - $anterior) / $anterior * 100, 2) : 0;

        return [
            'valor'    => $preco,
            'variacao' => $variacao,
            'max'      => (float) ($meta['regularMarketDayHigh'] ?? 0),
            'min'      => (float) ($meta['regularMarketDayLow']  ?? 0),
        ];
    }

    public function render()
    {
        return view('livewire.mercado-financeiro');
    }
}
