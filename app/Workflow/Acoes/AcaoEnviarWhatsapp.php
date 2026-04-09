<?php

namespace App\Workflow\Acoes;

use App\Models\Processo;
use App\Models\Usuario;
use App\Models\WorkflowAcao;
use App\Services\WhatsAppSmsService;
use App\Workflow\Contrato\AcaoInterface;

/**
 * Envia mensagem WhatsApp/SMS para o advogado ou número fixo.
 *
 * Config JSON esperada:
 * {
 *   "destinatario": "advogado_processo",    // "advogado_processo" | user_id numérico | telefone direto
 *   "mensagem":     "Novo andamento no processo {numero}: {andamento_descricao}",
 *   "canal":        "whatsapp",             // "whatsapp" | "sms" (default: canal padrão do .env)
 *   "tipo":         "workflow_prazo"        // tipo para registro (livre)
 * }
 */
class AcaoEnviarWhatsapp implements AcaoInterface
{
    public function __construct(private readonly WhatsAppSmsService $whatsapp) {}

    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array
    {
        $cfg = $acao->config ?? [];

        $mensagem = $this->interpolar(
            $cfg['mensagem'] ?? 'Atualização no processo {numero}.',
            $processo,
            $payload
        );
        $canal = $cfg['canal'] ?? '';
        $tipo  = $cfg['tipo']  ?? 'workflow';

        [$telefone, $nome] = $this->resolverDestinatario(
            $cfg['destinatario'] ?? 'advogado_processo',
            $processo
        );

        if (!$telefone) {
            return ['status' => 'ignorado', 'motivo' => 'Telefone não encontrado'];
        }

        $enviado = $this->whatsapp->enviar(
            telefone:        $telefone,
            mensagem:        $mensagem,
            destinatarioNome:$nome,
            tipo:            $tipo,
            canal:           $canal,
            referenciaTipo:  'processo',
            referenciaId:    $processo->id,
        );

        return ['status' => $enviado ? 'enviado' : 'falha', 'telefone' => $telefone];
    }

    /**
     * Retorna [telefone, nome] do destinatário.
     * Aceita: "advogado_processo" | user_id numérico | telefone direto (+5511...)
     */
    private function resolverDestinatario(string|int $destinatario, Processo $processo): array
    {
        // Telefone direto
        if (is_string($destinatario) && str_starts_with($destinatario, '+')) {
            return [$destinatario, 'Destinatário'];
        }

        $userId = is_numeric($destinatario)
            ? (int) $destinatario
            : ($destinatario === 'advogado_processo' ? $processo->advogado_id : null);

        if (!$userId) {
            return [null, ''];
        }

        $usuario = Usuario::find($userId);

        return [$usuario?->telefone, $usuario?->nome ?? ''];
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
