<?php

namespace App\Services;

use App\Models\Andamento;
use App\Models\Prazo;
use App\Models\Processo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PrazoAutoService
{
    /**
     * Regras de criação automática de prazos baseadas nas movimentações do CNJ.
     * Cada regra define palavras-chave (OR) e parâmetros do prazo a criar.
     *
     * Referências: CPC — Lei 13.105/2015
     */
    const REGRAS = [
        [
            'palavras'      => ['citação', 'citado', 'carta citatória'],
            'titulo'        => 'Contestação',
            'tipo'          => 'Prazo',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => true,
            'descricao'     => 'Prazo para apresentar contestação — CPC art. 335',
        ],
        [
            'palavras'      => ['sentença'],
            'excluir'       => ['homologatória', 'parcial'],   // não dispara nesses casos
            'titulo'        => 'Apelação',
            'tipo'          => 'Recurso',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => true,
            'descricao'     => 'Prazo para interpor apelação — CPC art. 1003 §5º',
        ],
        [
            'palavras'      => ['acórdão', 'acordão'],
            'titulo'        => 'Recurso (pós-acórdão)',
            'tipo'          => 'Recurso',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => false,
            'descricao'     => 'Prazo para interposição de recurso especial/extraordinário — CPC art. 1003',
        ],
        [
            'palavras'      => ['embargos de declaração', 'embargo de declaração'],
            'titulo'        => 'Embargos de Declaração',
            'tipo'          => 'Recurso',
            'dias'          => 5,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => false,
            'descricao'     => 'Prazo para oposição de embargos de declaração — CPC art. 1023',
        ],
        [
            'palavras'      => ['agravo de instrumento'],
            'titulo'        => 'Contrarrazões (agravo de instrumento)',
            'tipo'          => 'Recurso',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => false,
            'descricao'     => 'Prazo para contrarrazões ao agravo de instrumento — CPC art. 1019 II',
        ],
        [
            'palavras'      => ['intimação para cumprimento', 'intimado para cumprir'],
            'titulo'        => 'Cumprimento de intimação',
            'tipo'          => 'Prazo',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => false,
            'descricao'     => 'Prazo para cumprimento de intimação judicial',
        ],
        [
            'palavras'      => ['recurso especial', 'recurso extraordinário'],
            'titulo'        => 'Contrarrazões (REsp/RE)',
            'tipo'          => 'Recurso',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => false,
            'descricao'     => 'Prazo para contrarrazões — CPC art. 1030',
        ],
        [
            'palavras'      => ['julgamento antecipado', 'julgamento antecipado do mérito'],
            'titulo'        => 'Apelação (julgamento antecipado)',
            'tipo'          => 'Recurso',
            'dias'          => 15,
            'tipo_contagem' => 'uteis',
            'prazo_fatal'   => true,
            'descricao'     => 'Prazo para interpor apelação — CPC art. 1003 §5º',
        ],
    ];

    /**
     * Processa um andamento recém-criado e cria os prazos automáticos pertinentes.
     * Retorna o número de prazos criados.
     */
    public function processar(Andamento $andamento, Processo $processo): int
    {
        $descricao  = mb_strtolower($andamento->descricao ?? '');
        $criados    = 0;
        $dataInicio = $andamento->data instanceof Carbon
            ? $andamento->data
            : Carbon::parse($andamento->data);

        foreach (self::REGRAS as $regra) {
            // Verificar se a descrição contém alguma palavra-chave
            $match = false;
            foreach ($regra['palavras'] as $palavra) {
                if (str_contains($descricao, mb_strtolower($palavra))) {
                    $match = true;
                    break;
                }
            }

            if (!$match) continue;

            // Verificar palavras de exclusão (ex: "sentença homologatória")
            if (!empty($regra['excluir'])) {
                foreach ($regra['excluir'] as $excluir) {
                    if (str_contains($descricao, mb_strtolower($excluir))) {
                        $match = false;
                        break;
                    }
                }
            }

            if (!$match) continue;

            // Evitar duplicata: mesmo processo + título + data_inicio nos últimos 3 dias
            $jaExiste = Prazo::where('processo_id', $processo->id)
                ->where('titulo', $regra['titulo'])
                ->whereBetween('data_inicio', [
                    $dataInicio->copy()->subDays(3),
                    $dataInicio->copy()->addDays(3),
                ])
                ->exists();

            if ($jaExiste) continue;

            // Calcular data do prazo
            $dataPrazo = Prazo::calcularData(
                $dataInicio->format('Y-m-d'),
                $regra['dias'],
                $regra['tipo_contagem']
            );

            try {
                Prazo::create([
                    'processo_id'    => $processo->id,
                    'responsavel_id' => $processo->advogado_id ?? null,
                    'criado_por'     => null,
                    'titulo'         => $regra['titulo'],
                    'descricao'      => $regra['descricao'],
                    'tipo'           => $regra['tipo'],
                    'data_inicio'    => $dataInicio->format('Y-m-d'),
                    'tipo_contagem'  => $regra['tipo_contagem'],
                    'dias'           => $regra['dias'],
                    'data_prazo'     => $dataPrazo->format('Y-m-d'),
                    'prazo_fatal'    => $regra['prazo_fatal'],
                    'status'         => 'aberto',
                    'observacoes'    => "Criado automaticamente a partir do andamento: \"{$andamento->descricao}\" em {$dataInicio->format('d/m/Y')}",
                ]);

                $criados++;

                Log::info('PrazoAutoService: prazo criado', [
                    'processo' => $processo->numero,
                    'titulo'   => $regra['titulo'],
                    'data_prazo' => $dataPrazo->format('Y-m-d'),
                ]);

            } catch (\Throwable $e) {
                Log::error('PrazoAutoService: erro ao criar prazo', [
                    'processo' => $processo->numero,
                    'regra'    => $regra['titulo'],
                    'erro'     => $e->getMessage(),
                ]);
            }
        }

        return $criados;
    }
}
