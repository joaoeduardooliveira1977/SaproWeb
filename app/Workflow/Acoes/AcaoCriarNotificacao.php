<?php

namespace App\Workflow\Acoes;

use App\Models\Notificacao;
use App\Models\Processo;
use App\Models\WorkflowAcao;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Cria uma Notificação interna para o advogado responsável ou usuário específico.
 *
 * Config JSON esperada:
 * {
 *   "tipo":         "prazo",                                    // tipo da notificação (livre)
 *   "titulo":       "Nova intimação no processo {numero}",      // suporta variáveis
 *   "mensagem":     "Andamento: {andamento_descricao}",         // suporta variáveis
 *   "destinatario": "advogado_processo"                        // "advogado_processo" | user_id numérico
 * }
 *
 * Variáveis disponíveis: {numero}, {cliente}, {andamento_descricao}
 */
class AcaoCriarNotificacao implements AcaoInterface
{
    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg = $acao->config ?? [];

        $tipo        = $cfg['tipo']        ?? 'workflow';
        $titulo      = $this->interpolar($cfg['titulo']   ?? 'Alerta do processo {numero}', $processo, $payload);
        $mensagem    = $this->interpolar($cfg['mensagem'] ?? '', $processo, $payload);
        $userId      = $this->resolverDestinatario($cfg['destinatario'] ?? 'advogado_processo', $processo);

        $notificacao = Notificacao::create([
            'tipo'        => $tipo,
            'titulo'      => $titulo,
            'mensagem'    => $mensagem,
            'processo_id' => $processo->id,
            'user_id'     => $userId,
            'usuario_id'  => $userId,
            'lida'        => false,
            'link'        => route('processos.show', $processo->id),
        ]);

        return ['notificacao_id' => $notificacao->id];
    }

    private function resolverDestinatario(string|int $destinatario, Processo $processo): ?int
    {
        if (is_numeric($destinatario)) {
            return (int) $destinatario;
        }

        if ($destinatario === 'advogado_processo') {
            return $processo->advogado_id;
        }

        return null;
    }

    private function interpolar(string $texto, Processo $processo, array $payload): string
    {
        $andamentoDescricao = $payload['andamento_descricao'] ?? '';

        return str_replace(
            ['{numero}', '{cliente}', '{andamento_descricao}'],
            [$processo->numero ?? '', $processo->cliente?->nome ?? '', $andamentoDescricao],
            $texto
        );
    }
}
