<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TribunalService
{
    const BASE_URL = 'https://api-publica.datajud.cnj.jus.br';
    const TRIBUNAIS = [
        '8.26' => ['nome' => 'TJSP',  'endpoint' => 'api_publica_tjsp'],
        '8.19' => ['nome' => 'TJMG',  'endpoint' => 'api_publica_tjmg'],
        '5.02' => ['nome' => 'TRT2',  'endpoint' => 'api_publica_trt2'],
        '5.15' => ['nome' => 'TRT15', 'endpoint' => 'api_publica_trt15'],
        '4.03' => ['nome' => 'TRF3',  'endpoint' => 'api_publica_trf3'],
        '4.02' => ['nome' => 'TRF2',  'endpoint' => 'api_publica_trf2'],
        '4.01' => ['nome' => 'TRF1',  'endpoint' => 'api_publica_trf1'],
        '3.00' => ['nome' => 'STJ',   'endpoint' => 'api_publica_stj'],
        '1.00' => ['nome' => 'STF',   'endpoint' => 'api_publica_stf'],
        '6.00' => ['nome' => 'TST',   'endpoint' => 'api_publica_tst'],
    ];

    /**
     * Extrai o código J.TT do número CNJ e retorna os dados do tribunal.
     * Suporta formato mascarado (NNNNNNN-DD.AAAA.J.TT.OOOO) e somente dígitos.
     */
    public function detectarTribunal(string $numero): ?array
    {
        // Formato mascarado: 0001234-56.2023.8.26.0001
        if (preg_match('/\d{7}-\d{2}\.\d{4}\.(\d)\.(\d{1,2})\.\d/', $numero, $m)) {
            $codigo = $m[1] . '.' . str_pad($m[2], 2, '0', STR_PAD_LEFT);
            return self::TRIBUNAIS[$codigo] ?? null;
        }

        // Somente dígitos (20 chars): posições 14 = J, 15-16 = TT (base-0: índices 13, 14-15)
        $limpo = preg_replace('/[^0-9]/', '', $numero);
        if (strlen($limpo) >= 16) {
            $j      = $limpo[13];
            $tt     = substr($limpo, 14, 2);
            $codigo = $j . '.' . $tt;
            return self::TRIBUNAIS[$codigo] ?? null;
        }

        return null;
    }

    /**
     * Consulta andamentos de um processo no DATAJUD/CNJ.
     * Detecta automaticamente o tribunal pelo código J.TT do número CNJ.
     */
    public function consultarProcesso(string $numero): array
    {
        try {
            $apiKey = config('services.datajud.key');
            if (!$apiKey) {
                return ['sucesso' => false, 'erro' => 'DATAJUD_API_KEY nao configurada.'];
            }
            if (!str_starts_with($apiKey, 'APIKey ')) {
                $apiKey = 'APIKey ' . $apiKey;
            }

            $numeroLimpo = preg_replace('/[^0-9]/', '', $numero);

            if (strlen($numeroLimpo) < 15) {
                return ['sucesso' => false, 'erro' => 'Número de processo inválido: ' . $numero];
            }

            $tribunal = $this->detectarTribunal($numero);

            if (!$tribunal) {
                return ['sucesso' => false, 'erro' => 'Tribunal não reconhecido para o processo: ' . $numero];
            }

            $url = self::BASE_URL . '/' . $tribunal['endpoint'] . '/_search';

            Log::debug('TribunalService: consultando', [
                'tribunal' => $tribunal['nome'],
                'url'      => $url,
                'numero'   => $numeroLimpo,
            ]);

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => $apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post($url, [
                        'query' => [
                            'match' => [
                                'numeroProcesso' => $numeroLimpo,
                            ],
                        ],
                    ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning('TribunalService: timeout/conexão', [
                    'tribunal' => $tribunal['nome'],
                    'url'      => $url,
                    'erro'     => $e->getMessage(),
                ]);
                return [
                    'sucesso'  => false,
                    'tribunal' => $tribunal['nome'],
                    'erro'     => "O tribunal {$tribunal['nome']} não respondeu no tempo limite (15s). Tente novamente mais tarde.",
                ];
            }

            if (!$response->successful()) {
                return [
                    'sucesso'  => false,
                    'tribunal' => $tribunal['nome'],
                    'erro'     => 'Erro ao acessar DATAJUD: HTTP ' . $response->status(),
                ];
            }

            $data = $response->json();
            $hits = $data['hits']['hits'] ?? [];

            if (empty($hits)) {
                return [
                    'sucesso'    => false,
                    'tribunal'   => $tribunal['nome'],
                    'erro'       => 'Processo não encontrado no DATAJUD.',
                    'andamentos' => [],
                ];
            }

            $processo   = $hits[0]['_source'] ?? [];
            $movimentos = $processo['movimentos'] ?? [];

            $andamentos = [];
            foreach ($movimentos as $mov) {
                $dataHora = $mov['dataHora'] ?? null;
                $nome     = $mov['nome'] ?? ($mov['complementosTabelados'][0]['descricao'] ?? null);

                if (!$dataHora || !$nome) continue;

                $andamentos[] = [
                    'data'      => substr($dataHora, 0, 10), // yyyy-mm-dd
                    'descricao' => $nome,
                ];
            }

            usort($andamentos, fn($a, $b) => strcmp($b['data'], $a['data']));

            return [
                'sucesso'    => true,
                'numero'     => $numero,
                'tribunal'   => $tribunal['nome'],
                'andamentos' => $andamentos,
                'total'      => count($andamentos),
                'classe'     => $processo['classe']['nome'] ?? '',
                'assunto'    => $processo['assuntos'][0]['nome'] ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('TribunalService erro: ' . $e->getMessage());
            return ['sucesso' => false, 'erro' => 'Erro na consulta: ' . $e->getMessage()];
        }
    }
}
