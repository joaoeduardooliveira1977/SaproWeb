<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TjspService
{
    const API_URL  = 'https://api-publica.datajud.cnj.jus.br/api_publica_tjsp/_search';
    /**
     * Consulta andamentos de um processo no DATAJUD/CNJ
     */
    public function consultarProcesso(string $numero): array
    {
        try {
            $apiKey = config('services.datajud.key');
            if (!$apiKey) {
                return [
                    'sucesso' => false,
                    'erro'    => 'DATAJUD_API_KEY nao configurada.',
                ];
            }
            if (!str_starts_with($apiKey, 'APIKey ')) {
                $apiKey = 'APIKey ' . $apiKey;
            }

            // Limpar número do processo
            $numeroLimpo = preg_replace('/[^0-9]/', '', $numero);

            if (strlen($numeroLimpo) < 15) {
                return [
                    'sucesso' => false,
                    'erro'    => 'Número de processo inválido: ' . $numero,
                ];
            }

            Log::debug('TjspService: consultando', [
                'url'    => self::API_URL,
                'numero' => $numeroLimpo,
            ]);

            try {
                $response = Http::timeout(15)
                    ->withHeaders([
                        'Authorization' => $apiKey,
                        'Content-Type'  => 'application/json',
                    ])
                    ->post(self::API_URL, [
                        'query' => [
                            'match' => [
                                'numeroProcesso' => $numeroLimpo,
                            ],
                        ],
                    ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::warning('TjspService: timeout/conexão', [
                    'url'  => self::API_URL,
                    'erro' => $e->getMessage(),
                ]);
                return [
                    'sucesso' => false,
                    'erro'    => 'O DATAJUD/TJSP não respondeu no tempo limite (15s). Tente novamente mais tarde.',
                ];
            }

            if (!$response->successful()) {
                return [
                    'sucesso' => false,
                    'erro'    => 'Erro ao acessar DATAJUD: HTTP ' . $response->status(),
                ];
            }

            $data = $response->json();
            $hits = $data['hits']['hits'] ?? [];

            if (empty($hits)) {
                return [
                    'sucesso'    => false,
                    'erro'       => 'Processo não encontrado no DATAJUD.',
                    'andamentos' => [],
                ];
            }

            $processo   = $hits[0]['_source'] ?? [];
            $movimentos = $processo['movimentos'] ?? [];

            $andamentos = [];
            foreach ($movimentos as $mov) {
                $data = $mov['dataHora'] ?? null;
                $nome = $mov['nome'] ?? ($mov['complementosTabelados'][0]['descricao'] ?? null);

                if (!$data || !$nome) continue;

                $andamentos[] = [
                    'data'      => substr($data, 0, 10), // yyyy-mm-dd
                    'descricao' => $nome,
                ];
            }

            // Ordenar por data decrescente
            usort($andamentos, fn($a, $b) => strcmp($b['data'], $a['data']));

            return [
                'sucesso'    => true,
                'numero'     => $numero,
                'andamentos' => $andamentos,
                'total'      => count($andamentos),
                'classe'     => $processo['classe']['nome'] ?? '',
                'assunto'    => $processo['assuntos'][0]['nome'] ?? '',
                'tribunal'   => $processo['tribunal'] ?? 'TJSP',
            ];

        } catch (\Exception $e) {
            Log::error('TjspService erro: ' . $e->getMessage());
            return [
                'sucesso' => false,
                'erro'    => 'Erro na consulta: ' . $e->getMessage(),
            ];
        }
    }
}
