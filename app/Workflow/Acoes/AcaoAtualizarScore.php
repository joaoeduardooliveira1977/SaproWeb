<?php

namespace App\Workflow\Acoes;

use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Atualiza o campo `score` do processo.
 *
 * Config JSON esperada:
 * {
 *   "score": "critico"   // "critico" | "atencao" | "normal"
 * }
 *
 * Ou com regra automática baseada no conteúdo do andamento:
 * {
 *   "score": "auto"
 * }
 * Quando "auto":
 *   - Andamento contém sentença/acórdão/decisão urgente → critico
 *   - Andamento contém prazo/intimação/citação          → atencao
 *   - Caso contrário                                     → mantém score atual
 */
class AcaoAtualizarScore implements AcaoInterface
{
    private const SCORES_VALIDOS = ['critico', 'atencao', 'normal'];

    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg        = $acao->config ?? [];
        $scoreConfig = $cfg['score'] ?? 'auto';

        $scoreAnterior = $processo->score;
        $scoreNovo     = $scoreConfig === 'auto'
            ? $this->calcularScoreAuto($payload, $processo)
            : $scoreConfig;

        // Valida e não altera se score calculado for inválido ou igual ao atual
        if (!in_array($scoreNovo, self::SCORES_VALIDOS, true) || $scoreNovo === $scoreAnterior) {
            return ['status' => 'ignorado', 'score_atual' => $scoreAnterior];
        }

        $processo->update(['score' => $scoreNovo]);

        return [
            'score_anterior' => $scoreAnterior,
            'score_novo'     => $scoreNovo,
        ];
    }

    private function calcularScoreAuto(array $payload, Processo $processo): string
    {
        $desc = mb_strtolower($payload['andamento_descricao'] ?? '');

        // Palavras que indicam criticidade alta
        $critico = ['sentença', 'acórdão', 'decisão', 'prazo fatal', 'extinção', 'improcedente', 'procedente'];
        foreach ($critico as $palavra) {
            if (str_contains($desc, $palavra)) {
                return 'critico';
            }
        }

        // Palavras que indicam atenção
        $atencao = ['intimação', 'citação', 'prazo', 'audiência', 'notificação', 'mandado'];
        foreach ($atencao as $palavra) {
            if (str_contains($desc, $palavra)) {
                return 'atencao';
            }
        }

        // Mantém score atual se não houver correspondência
        return $processo->score ?? 'normal';
    }
}
