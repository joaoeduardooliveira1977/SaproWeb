<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AtualizarIndicesMonetarios extends Command
{
    protected $signature = 'indices:atualizar
                            {--sigla= : Atualizar somente uma sigla (IPCA, IGPM, SELIC, TR)}
                            {--desde= : Ano de início da importação (ex: 2000)}
                            {--force : Reimporta mesmo que o registro já exista}';

    protected $description = 'Importa/atualiza índices monetários via API do Banco Central do Brasil (SGS)';

    /**
     * Séries do SGS/BACEN
     * https://www.bcb.gov.br/estabilidadefinanceira/historicocotacoes
     */
    private const SERIES = [
        'IPCA'  => ['codigo' => 433,  'nome' => 'IPCA - IBGE'],
        'IGPM'  => ['codigo' => 189,  'nome' => 'IGP-M - FGV'],
        'SELIC' => ['codigo' => 4390, 'nome' => 'Taxa SELIC Mensal'],
        'TR'    => ['codigo' => 253,  'nome' => 'TR - Taxa Referencial', 'anos_max' => 5],
    ];

    public function handle(): int
    {
        $siglaFiltro = $this->option('sigla') ? strtoupper($this->option('sigla')) : null;
        $desde       = $this->option('desde') ? (int) $this->option('desde') : 2000;
        $force       = $this->option('force');

        if ($siglaFiltro && ! array_key_exists($siglaFiltro, self::SERIES)) {
            $this->error("Sigla inválida: {$siglaFiltro}. Use: " . implode(', ', array_keys(self::SERIES)));
            return 1;
        }

        $series = $siglaFiltro
            ? [$siglaFiltro => self::SERIES[$siglaFiltro]]
            : self::SERIES;

        $dataInicial = "01/01/{$desde}";
        $dataFinal   = now()->format('d/m/Y');

        $totalInseridos  = 0;
        $totalIgnorados  = 0;
        $erros           = 0;

        foreach ($series as $sigla => $cfg) {
            // Algumas séries (ex: TR) rejeitam intervalos muito longos — limitar se necessário
            $anoInicio = isset($cfg['anos_max'])
                ? max($desde, (int) now()->subYears($cfg['anos_max'])->format('Y'))
                : $desde;
            $dataInicialSerie = "01/01/{$anoInicio}";

            $this->info("Buscando {$sigla} ({$cfg['nome']}) desde {$dataInicialSerie}...");

            try {
                $dados = $this->buscarBacen($cfg['codigo'], $dataInicialSerie, $dataFinal);

                if (empty($dados)) {
                    $this->warn("  ⚠ Nenhum dado retornado para {$sigla}.");
                    continue;
                }

                $inseridos = 0;
                $ignorados = 0;

                foreach ($dados as $item) {
                    // Formato da data retornada pelo BACEN: "dd/MM/yyyy"
                    try {
                        $mesRef = Carbon::createFromFormat('d/m/Y', $item['data'])->startOfMonth()->toDateString();
                    } catch (\Exception $e) {
                        continue; // data inválida
                    }

                    $percentual = str_replace(',', '.', $item['valor']);
                    if (! is_numeric($percentual)) continue;

                    $existe = DB::table('indices_monetarios')
                        ->where('sigla', $sigla)
                        ->where('mes_ref', $mesRef)
                        ->exists();

                    if ($existe && ! $force) {
                        $ignorados++;
                        continue;
                    }

                    if ($existe) {
                        DB::table('indices_monetarios')
                            ->where('sigla', $sigla)
                            ->where('mes_ref', $mesRef)
                            ->update([
                                'percentual'  => (float) $percentual,
                                'updated_at'  => now(),
                            ]);
                    } else {
                        DB::table('indices_monetarios')->insert([
                            'nome'       => $cfg['nome'],
                            'sigla'      => $sigla,
                            'mes_ref'    => $mesRef,
                            'percentual' => (float) $percentual,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    $inseridos++;
                }

                $totalInseridos += $inseridos;
                $totalIgnorados += $ignorados;

                $ultimo = Carbon::createFromFormat('d/m/Y', end($dados)['data'])->format('m/Y');
                $this->info("  ✔ {$inseridos} registro(s) inseridos/atualizados, {$ignorados} já existentes. Último: {$ultimo}");

            } catch (\Exception $e) {
                $this->error("  ✗ Erro ao buscar {$sigla}: " . $e->getMessage());
                $erros++;
            }
        }

        $this->newLine();
        $this->info("Concluído: {$totalInseridos} inserido(s), {$totalIgnorados} ignorado(s), {$erros} erro(s).");

        return $erros > 0 ? 1 : 0;
    }

    /**
     * Consulta a API SGS do Banco Central do Brasil.
     * Tenta primeiro com Accept: application/json; se receber 406 tenta sem o header.
     */
    private function buscarBacen(int $codigo, string $dataInicial, string $dataFinal): array
    {
        $url = "https://api.bcb.gov.br/dados/serie/bcdata.sgs.{$codigo}/dados"
             . "?formato=json"
             . "&dataInicial=" . urlencode($dataInicial)
             . "&dataFinal="   . urlencode($dataFinal);

        $response = Http::timeout(30)
            ->withHeaders(['Accept' => 'application/json'])
            ->get($url);

        // Alguns endpoints rejeitam o header Accept — tenta sem ele
        if ($response->status() === 406) {
            $response = Http::timeout(30)->get($url);
        }

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()} ao acessar série {$codigo}");
        }

        $dados = $response->json();

        if (! is_array($dados)) {
            throw new \RuntimeException("Resposta inválida da API BACEN para série {$codigo}");
        }

        return $dados;
    }
}
