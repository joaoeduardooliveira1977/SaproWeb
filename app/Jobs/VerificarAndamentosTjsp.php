<?php

namespace App\Jobs;

use App\Models\Processo;
use App\Models\Andamento;
use App\Models\TjspVerificacao;
use App\Services\TjspService;
use Illuminate\Bus\Queueable;
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

        // Se não vieram IDs específicos, busca todos ativos
        $query = Processo::with('cliente');
        if (!empty($this->processoIds)) {
            $query->whereIn('id', $this->processoIds);
        } else {
            $query->where('status', 'Ativo');
        }

        $processos = $query->get();
        $verificacao->update(['total' => $processos->count()]);

        $service         = new TjspService();
        $novosAndamentos = [];
        $novosTotal      = 0;
        $processado      = 0;

        foreach ($processos as $processo) {
            $processado++;
            $verificacao->update([
                'processado'     => $processado,
                'processo_atual' => $processo->numero,
            ]);

            $resultado = $service->consultarProcesso($processo->numero);

            if (!$resultado['sucesso'] || empty($resultado['andamentos'])) {
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
                    Andamento::create([
                        'processo_id' => $processo->id,
                        'data'        => $a['data'],
                        'descricao'   => $a['descricao'],
                    ]);
                    $novos[] = $a;
                    $novosTotal++;
                }
            }

            if (count($novos) > 0) {
                $novosAndamentos[] = [
                    'numero'     => $processo->numero,
                    'cliente'    => $processo->cliente?->nome ?? '—',
                    'andamentos' => $novos,
                ];

                $verificacao->update([
                    'novos_total'      => $novosTotal,
                    'novos_andamentos' => $novosAndamentos,
                ]);
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
            'concluido_em'     => now(),
        ]);
    }

    public function failed(\Throwable $e): void
    {
        TjspVerificacao::where('id', $this->verificacaoId)
            ->update(['status' => 'erro']);
    }
}
