<?php

namespace App\Jobs;

use App\Models\Andamento;
use App\Models\LoteVerificacao;
use App\Models\Notificacao;
use App\Models\Processo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VerificarProcessoDatajud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 60;
    public int $tries   = 3;

    public function __construct(
        public string $processoNumero,
        public int    $loteId
    ) {}

    public function handle(): void
    {
        $lote = LoteVerificacao::find($this->loteId);
        if (!$lote) {
            Log::warning("VerificarProcessoDatajud: lote_id={$this->loteId} não encontrado.");
            return;
        }

        $lote->update(['status' => 'verificando']);

        try {

	Log::info("DATAJUD iniciando", ['numero' => $this->processoNumero]);

            $tribunal = $this->detectarTribunal($this->processoNumero);
            $endpoint = "https://api-publica.datajud.cnj.jus.br/api_publica_{$tribunal}/_search";
            $apiKey   = 'APIKey ' . config('services.datajud.key');

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type'  => 'application/json',
            ])->post($endpoint, [
                'query' => [
                    'match' => [
                        'numeroProcesso' => $this->processoNumero,
                    ],
                ],
            ]);

            if (!$response->successful()) {
                throw new \Exception("HTTP {$response->status()}: {$response->body()}");
            }

            $hits = $response->json('hits.hits') ?? [];

            if (empty($hits)) {
                $lote->update(['status' => 'verificado']);
                return;
            }

            $dadosProcesso = $hits[0]['_source'] ?? [];
            $movimentos    = $dadosProcesso['movimentos'] ?? [];

            $processo = Processo::where('numero', 'like', '%' . preg_replace('/\D/', '', $this->processoNumero) . '%')->first();


Log::info("DATAJUD DEBUG", [
    'numero'     => $this->processoNumero,
    'processo'   => $processo?->id,
    'movimentos' => count($movimentos),
    'hits'       => count($hits),
]);


            if ($processo && !empty($movimentos)) {
               foreach (array_slice(array_reverse($movimentos), 0, 5) as $mov) {
                    $descricao      = $mov['nome'] ?? $mov['complementosTabelados'][0]['descricao'] ?? 'Andamento registrado';
                    $data           = $mov['dataHora'] ?? now();
                    $dataFormatada  = substr($data, 0, 10);

                    $jaExiste = Andamento::where('processo_id', $processo->id)
                        ->whereDate('data', $dataFormatada)
                        ->where('descricao', $descricao)
                        ->exists();



		Log::info("DATAJUD JAEXISTE", [
    		'descricao' => $descricao,
    		'data'      => $dataFormatada,
    		'ja_existe' => $jaExiste,
		]);


		Log::info("DATAJUD MOV", [
        		'descricao' => $descricao,
        		'data'      => $dataFormatada,
    		]);



                    if (!$jaExiste) {
                        Andamento::create([
                            'processo_id' => $processo->id,
                            'data'        => $dataFormatada,
                            'descricao'   => $descricao,
                            'usuario_id'  => null,
                            'tenant_id'   => $processo->tenant_id ?? null,
                        ]);

                        $score = $this->detectarScore($descricao);

                        Notificacao::create([
                            'tipo'        => $score === 'critico' ? 'decisao' : 'andamento',
                            'titulo'      => $score === 'critico' ? 'Decisão importante detectada' : 'Novo andamento',
                            'mensagem'    => $descricao,
                            'processo_id' => $processo->id,
                            'usuario_id'  => null,
                            'lida'        => false,
                        ]);

                        $processo->update([
                            'score'                      => $score,
                            'ultima_verificacao_datajud' => now(),
                        ]);
                    }
                }

                $processo->update(['ultima_verificacao_datajud' => now()]);
            }

            $lote->update(['status' => 'verificado']);

        } catch (\Throwable $e) {
            Log::error("VerificarProcessoDatajud erro [{$this->processoNumero}]: " . $e->getMessage());
            $lote->update([
                'status'        => 'erro',
                'erro_mensagem' => $e->getMessage(),
            ]);
        }
    }

    private function detectarTribunal(string $numero): string
    {
        $codigo = substr(preg_replace('/\D/', '', $numero), 13, 4);

        $tribunais = [
            '8260' => 'tjsp', '8190' => 'tjrj', '8210' => 'tjrs',
            '8130' => 'tjmg', '8240' => 'tjsc', '8060' => 'tjce',
            '8150' => 'tjpa', '8090' => 'tjgo', '8070' => 'tjdf',
            '8140' => 'tjmt', '8160' => 'tjpe', '8170' => 'tjpr',
            '8180' => 'tjrn', '8200' => 'tjro', '8220' => 'tjse',
            '8050' => 'tjba', '8080' => 'tjes', '8100' => 'tjma',
            '8110' => 'tjms', '8120' => 'tjpi', '8230' => 'tjto',
            '8010' => 'tjac', '8020' => 'tjal', '8030' => 'tjam',
            '8040' => 'tjap', '8250' => 'tjrr', '5010' => 'trf1',
            '5020' => 'trf2', '5030' => 'trf3', '5040' => 'trf4',
            '5050' => 'trf5', '4010' => 'tst',
        ];

        return $tribunais[$codigo] ?? 'tjsp';
    }

    private function detectarScore(string $descricao): string
    {
        $desc = mb_strtolower($descricao);

        $critico = ['sentença', 'acórdão', 'condenação', 'improcedente', 'procedente',
                    'bloqueio', 'penhora', 'arresto', 'liminar', 'tutela', 'despejo'];

        $atencao = ['prazo', 'intimação', 'citação', 'audiência', 'perícia', 'recurso'];

        foreach ($critico as $termo) {
            if (str_contains($desc, $termo)) return 'critico';
        }
        foreach ($atencao as $termo) {
            if (str_contains($desc, $termo)) return 'atencao';
        }

        return 'normal';
    }
}