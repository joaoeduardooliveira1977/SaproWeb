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

    public int $tries         = 3;
    public int $timeout       = 90;
    public int $maxExceptions = 3;

    public function __construct(
        public string $processoNumero,
        public int    $loteId
    ) {}

    public function backoff(): array
    {
        return [60, 300, 900]; // 1min, 5min, 15min
    }

    public function handle(): void
    {
        $lote = LoteVerificacao::find($this->loteId);
        if (!$lote) {
            Log::warning('DATAJUD lote não encontrado', ['lote_id' => $this->loteId]);
            return;
        }

        $lote->update(['status' => 'verificando']);

        $novosAndamentos = 0;
        $numeroLimpo     = preg_replace('/\D/', '', $this->processoNumero);
        $tribunal        = $this->detectarTribunal($this->processoNumero);
        $endpoint        = "https://api-publica.datajud.cnj.jus.br/api_publica_{$tribunal}/_search";
        $apiKey          = config('services.datajud.key');

        if (!$apiKey) {
            throw new \RuntimeException('DATAJUD_API_KEY não configurada.');
        }
        if (!str_starts_with($apiKey, 'APIKey ')) {
            $apiKey = 'APIKey ' . $apiKey;
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => $apiKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post($endpoint, [
                    'query' => ['match' => ['numeroProcesso' => $numeroLimpo]],
                ]);

            if ($response->status() === 429) {
                Log::warning('DATAJUD rate limit atingido', ['numero' => $this->processoNumero]);
                $this->release(300);
                return;
            }

            if ($response->status() === 503 || $response->serverError()) {
                Log::warning('DATAJUD indisponível', [
                    'status' => $response->status(),
                    'numero' => $this->processoNumero,
                ]);
                throw new \RuntimeException('DATAJUD retornou ' . $response->status());
            }

            if (!$response->successful()) {
                Log::error('DATAJUD erro inesperado', [
                    'status' => $response->status(),
                    'numero' => $this->processoNumero,
                ]);
                throw new \RuntimeException('DATAJUD erro HTTP ' . $response->status());
            }

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('DATAJUD timeout/conexão', [
                'numero' => $this->processoNumero,
                'erro'   => $e->getMessage(),
            ]);
            throw $e;
        }

        $hits = $response->json('hits.hits') ?? [];

        if (empty($hits)) {
            $lote->update(['status' => 'verificado']);
            Log::info('DATAJUD verificação concluída', [
                'numero'           => $this->processoNumero,
                'novos_andamentos' => 0,
                'tentativa'        => $this->attempts(),
            ]);
            return;
        }

        $dadosProcesso = $hits[0]['_source'] ?? [];
        $movimentos    = $dadosProcesso['movimentos'] ?? [];

        $processo = Processo::whereRaw("regexp_replace(numero, '[^0-9]', '', 'g') = ?", [$numeroLimpo])->first();

        if ($processo && !empty($movimentos)) {
            foreach (array_slice(array_reverse($movimentos), 0, 5) as $mov) {
                $descricao     = $mov['nome'] ?? $mov['complementosTabelados'][0]['descricao'] ?? 'Andamento registrado';
                $data          = $mov['dataHora'] ?? now();
                $dataFormatada = substr($data, 0, 10);

                $jaExiste = Andamento::where('processo_id', $processo->id)
                    ->whereDate('data', $dataFormatada)
                    ->where('descricao', $descricao)
                    ->exists();

                if (!$jaExiste) {
                    Andamento::create([
                        'processo_id' => $processo->id,
                        'data'        => $dataFormatada,
                        'descricao'   => $descricao,
                        'usuario_id'  => null,
                        'tenant_id'   => $processo->tenant_id ?? null,
                    ]);

                    Log::info('DATAJUD andamento novo importado', [
                        'processo_id' => $processo->id,
                        'numero'      => $this->processoNumero,
                        'data'        => $dataFormatada,
                        'descricao'   => $descricao,
                    ]);

                    $score = $this->detectarScore($descricao);

                    Notificacao::create([
                        'tipo'        => $score === 'critico' ? 'decisao' : 'andamento_novo',
                        'titulo'      => 'Novo andamento: ' . $descricao,
                        'mensagem'    => 'Processo ' . $processo->numero . ' — ' . $descricao . ' em ' . $dataFormatada,
                        'processo_id' => $processo->id,
                        'usuario_id'  => $lote->user_id ?? null,
                        'user_id'     => $lote->user_id ?? null,
                        'tenant_id'   => $processo->tenant_id ?? null,
                        'lida'        => false,
                    ]);

                    $processo->update([
                        'score'                      => $score,
                        'ultima_verificacao_datajud' => now(),
                    ]);

                    $novosAndamentos++;
                }
            }

            $processo->update(['ultima_verificacao_datajud' => now()]);
        }

        $lote->update(['status' => 'verificado']);

        Log::info('DATAJUD verificação concluída', [
            'numero'           => $this->processoNumero,
            'novos_andamentos' => $novosAndamentos,
            'tentativa'        => $this->attempts(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('DATAJUD falha permanente após todas as tentativas', [
            'numero'  => $this->processoNumero,
            'lote_id' => $this->loteId,
            'erro'    => $exception->getMessage(),
        ]);

        $lote = LoteVerificacao::find($this->loteId);
        if ($lote) {
            $lote->update([
                'status'        => 'erro',
                'erro_mensagem' => $exception->getMessage(),
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
