<?php

namespace App\Workflow\Acoes;

use App\Models\Agenda;
use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Cria um compromisso na Agenda vinculado ao processo.
 *
 * Config JSON esperada:
 * {
 *   "titulo":        "Prazo: Resposta à Intimação — {numero}",  // suporta variáveis
 *   "tipo":          "Prazo",                                    // livre (Audiência, Reunião, etc.)
 *   "dias_a_partir": 1,                                         // dias a partir de hoje (default: 1)
 *   "hora":          "09:00",                                   // formato HH:MM (default: 09:00)
 *   "urgente":       true,                                      // default: false
 *   "local":         "",                                        // opcional
 *   "observacoes":   "",                                        // opcional
 *   "responsavel":   "advogado_processo"                       // "advogado_processo" | user_id numérico
 * }
 */
class AcaoCriarAgenda implements AcaoInterface
{
    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg = $acao->config ?? [];

        $titulo      = $this->interpolar($cfg['titulo'] ?? 'Compromisso — {numero}', $processo, $payload);
        $tipo        = $cfg['tipo']          ?? 'Prazo';
        $diasAPartir = (int) ($cfg['dias_a_partir'] ?? 1);
        $hora        = $cfg['hora']          ?? '09:00';
        $urgente     = (bool) ($cfg['urgente'] ?? false);
        $local       = $cfg['local']         ?? null;
        $observacoes = $cfg['observacoes']   ?? null;

        $dataHora = now()->addDays($diasAPartir)->setTimeFromTimeString($hora);

        $responsavelId = $this->resolverResponsavel(
            $cfg['responsavel'] ?? 'advogado_processo',
            $processo
        );

        $evento = Agenda::create([
            'tenant_id'     => $processo->tenant_id,
            'titulo'        => $titulo,
            'data_hora'     => $dataHora,
            'tipo'          => $tipo,
            'urgente'       => $urgente,
            'processo_id'   => $processo->id,
            'responsavel_id'=> $responsavelId,
            'local'         => $local,
            'observacoes'   => $observacoes,
            'concluido'     => false,
        ]);

        return ['agenda_id' => $evento->id, 'data_hora' => $dataHora->toDateTimeString()];
    }

    private function resolverResponsavel(string|int $responsavel, Processo $processo): ?int
    {
        if (is_numeric($responsavel)) {
            return (int) $responsavel;
        }
        return $responsavel === 'advogado_processo' ? $processo->advogado_id : null;
    }

    private function interpolar(string $texto, Processo $processo, array $payload): string
    {
        return str_replace(
            ['{numero}', '{cliente}', '{andamento_descricao}'],
            [$processo->numero ?? '', $processo->cliente?->nome ?? '', $payload['andamento_descricao'] ?? ''],
            $texto
        );
    }
}
