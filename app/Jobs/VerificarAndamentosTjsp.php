<?php

namespace App\Jobs;

use App\Models\Processo;
use App\Models\Andamento;
use App\Models\TjspVerificacao;
use App\Services\PrazoAutoService;
use App\Services\TribunalService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerificarAndamentosTjsp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 3600;
    public int $tries   = 1;

    public function __construct(
        public int   $verificacaoId,
        public array $processoIds = []
    ) {}

    public function handle(): void
    {
        $verificacao = TjspVerificacao::findOrFail($this->verificacaoId);
        $verificacao->update([
            'status'      => 'rodando',
            'iniciado_em' => now(),
        ]);

        $novosAndamentos = [];
        $novosTotal      = 0;
        $prazosCriados   = 0;
        $processado      = 0;

        try {
            // Se não vieram IDs específicos, busca todos ativos
            $query = Processo::with('cliente');
            if (!empty($this->processoIds)) {
                $query->whereIn('id', $this->processoIds);
            } else {
                $query->where('status', 'Ativo');
            }

            $processos = $query->get();
            $verificacao->update(['total' => $processos->count()]);

            $service      = new TribunalService();
            $prazoService = new PrazoAutoService();

            foreach ($processos as $processo) {
                $processado++;
                $verificacao->update([
                    'processado'     => $processado,
                    'processo_atual' => $processo->numero,
                ]);
                $verificacao->appendLog('consultando', $processo->numero, 'Consultando no DATAJUD...');

                try {
                    $resultado = $service->consultarProcesso($processo->numero);
                } catch (\Throwable $e) {
                    Log::error('VerificarAndamentosTjsp: falha ao consultar processo', [
                        'processo' => $processo->numero,
                        'erro'     => $e->getMessage(),
                    ]);
                    $verificacao->appendLog('erro', $processo->numero, 'Falha: ' . $e->getMessage());
                    usleep(300000);
                    continue;
                }

                if (!$resultado['sucesso']) {
                    $verificacao->appendLog('erro', $processo->numero, $resultado['erro'] ?? 'Não encontrado', $resultado['tribunal'] ?? null);
                    usleep(300000);
                    continue;
                }

                if (empty($resultado['andamentos'])) {
                    $verificacao->appendLog('sem_novos', $processo->numero, 'Sem andamentos no DATAJUD', $resultado['tribunal'] ?? null);
                    usleep(300000);
                    continue;
                }

                $novos = [];
                foreach ($resultado['andamentos'] as $a) {
                    if (!$a['data']) continue;

                    $existe = Andamento::where('processo_id', $processo->id)
                        ->whereDate('data', $a['data'])
                        ->where('descricao', $a['descricao'])
                        ->exists();

                    if (!$existe) {
                        $andamento = Andamento::create([
                            'processo_id' => $processo->id,
                            'data'        => $a['data'],
                            'descricao'   => $a['descricao'],
                        ]);
                        $novos[] = $a;
                        $novosTotal++;

                        $prazosCriados += $prazoService->processar($andamento, $processo);
                    }
                }

                if (count($novos) > 0) {
                    $novosAndamentos[] = [
                        'numero'     => $processo->numero,
                        'cliente'    => $processo->cliente?->nome ?? '—',
                        'tribunal'   => $resultado['tribunal'] ?? '',
                        'andamentos' => $novos,
                    ];

                    $verificacao->update([
                        'novos_total'      => $novosTotal,
                        'novos_andamentos' => $novosAndamentos,
                    ]);

                    $verificacao->appendLog('ok', $processo->numero,
                        count($novos) . ' andamento(s) novo(s) importado(s)',
                        $resultado['tribunal'] ?? null,
                        count($novos)
                    );
                } else {
                    $verificacao->appendLog('sem_novos', $processo->numero,
                        'Já atualizado — nenhum andamento novo',
                        $resultado['tribunal'] ?? null
                    );
                }

                $processo->update(['tjsp_ultima_consulta' => now()]);
                usleep(500000);
            }

            $verificacao->update([
                'status'           => 'concluido',
                'processado'       => $processado,
                'processo_atual'   => null,
                'novos_total'      => $novosTotal,
                'novos_andamentos' => $novosAndamentos,
                'prazos_criados'   => $prazosCriados,
                'concluido_em'     => now(),
            ]);

        } catch (\Throwable $e) {
            Log::error('VerificarAndamentosTjsp: erro fatal no job', [
                'verificacao_id' => $this->verificacaoId,
                'erro'           => $e->getMessage(),
            ]);
            throw $e; // re-throw para acionar failed()
        } finally {
            // Garante que a verificação nunca fica travada em pendente/rodando
            $verificacao->refresh();
            if ($verificacao->emAndamento()) {
                $verificacao->update([
                    'status'       => 'erro',
                    'concluido_em' => now(),
                ]);
            }
        }
    }

    public function failed(\Throwable $e): void
    {
        TjspVerificacao::where('id', $this->verificacaoId)
            ->update(['status' => 'erro']);
    }
}
