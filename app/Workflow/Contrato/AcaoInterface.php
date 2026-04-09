<?php

namespace App\Workflow\Contrato;

use App\Models\Processo;
use App\Models\WorkflowAcao;

interface AcaoInterface
{
    /**
     * Executa a ação.
     *
     * @param  WorkflowAcao  $acao     A ação com sua config JSON
     * @param  Processo      $processo O processo alvo
     * @param  array         $payload  Dados do evento que disparou (ex: andamento_id)
     *
     * @return array  Resultado registrado em workflow_execucoes.resultado
     *                Ex: ['prazo_id' => 42] | ['notificacao_id' => 7]
     *
     * @throws \Throwable  Qualquer exceção é capturada pelo WorkflowEngine e
     *                     registrada como status='erro' na execução.
     */
    public function executar(WorkflowAcao $acao, Processo $processo, array $payload): array;
}
