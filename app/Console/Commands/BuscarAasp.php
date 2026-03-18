<?php

namespace App\Console\Commands;

use App\Models\{AaspAdvogado, AaspConfig, AaspPublicacao, Processo};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class BuscarAasp extends Command
{
    protected $signature   = 'aasp:buscar {--data= : Data no formato Y-m-d (padrão: hoje)}';
    protected $description = 'Busca publicações AASP do dia para todos os advogados ativos';

    public function handle(): int
    {
        $config = AaspConfig::first();
        if ($config && ! $config->ativo) {
            $this->info('Busca AASP desativada nas configurações.');
            return self::SUCCESS;
        }

        $data          = $this->option('data') ? Carbon::parse($this->option('data')) : today();
        $dataFormatada = $data->format('d/m/Y');
        $advogados     = AaspAdvogado::where('ativo', true)->get();

        if ($advogados->isEmpty()) {
            $this->warn('Nenhum advogado AASP ativo cadastrado.');
            return self::SUCCESS;
        }

        $this->info("AASP — Buscando publicações de {$dataFormatada}");

        $total = 0;
        foreach ($advogados as $adv) {
            try {
                $response = Http::timeout(30)
                    ->withoutVerifying()
                    ->get('https://intimacaoapi.aasp.org.br/api/Associado/intimacao/json', [
                        'chave'       => $adv->chave_aasp,
                        'data'        => $dataFormatada,
                        'diferencial' => 'false',
                    ]);

                if (! $response->successful()) {
                    $this->warn("  {$adv->nome}: HTTP {$response->status()}");
                    continue;
                }

                $payload = $response->json();
                $pubs    = $payload['intimacoes'] ?? $payload['value'] ?? (isset($payload[0]) ? $payload : []);

                $count = 0;
                foreach ($pubs as $pub) {
                    $numPub = $pub['numeroPublicacao'] ?? $pub['numero_publicacao'] ?? $pub['NumeroPublicacao'] ?? null;
                    if ($numPub && AaspPublicacao::where('numero_publicacao', $numPub)->exists()) continue;

                    try { $dataPub = Carbon::parse($pub['data'] ?? $pub['dataPublicacao'] ?? $data)->format('Y-m-d'); }
                    catch (\Exception) { $dataPub = $data->format('Y-m-d'); }

                    $jornalRaw = $pub['jornal'] ?? null;
                    $jornal    = is_array($jornalRaw) ? ($jornalRaw['nomeJornal'] ?? '') : ($jornalRaw ?? '');

                    $numProcesso    = $pub['numeroUnicoProcesso'] ?? $pub['numeroProcesso'] ?? $pub['numero_processo'] ?? '';
                    $processoId     = $numProcesso
                        ? Processo::where('numero', $numProcesso)->value('id')
                        : null;

                    AaspPublicacao::create([
                        'codigo_aasp'       => $adv->codigo_aasp,
                        'processo_id'       => $processoId,
                        'data'              => $dataPub,
                        'jornal'            => $jornal,
                        'numero_processo'   => $numProcesso,
                        'titulo'            => $pub['titulo'] ?? $pub['Titulo'] ?? '',
                        'texto'             => $pub['textoPublicacao'] ?? $pub['texto'] ?? $pub['conteudo'] ?? '',
                        'numero_publicacao' => $numPub,
                    ]);
                    $count++;
                    $total++;
                }
                $this->line("  <info>{$adv->nome}</info>: {$count} nova(s)");
            } catch (\Throwable $e) {
                $this->warn("  {$adv->nome}: {$e->getMessage()}");
            }
        }

        $this->info("Concluído — {$total} publicação(ões) importada(s).");
        return self::SUCCESS;
    }
}
