<?php

namespace App\Console\Commands;

use App\Jobs\VerificarAndamentosTjsp;
use App\Models\{Processo, TjspVerificacao};
use Illuminate\Console\Command;

class ConsultarDatajud extends Command
{
    protected $signature   = 'datajud:consultar
                                {--force : Forçar mesmo se já houver verificação em andamento}
                                {--processos= : IDs separados por vírgula (ex: 1,2,3)}';

    protected $description = 'Consulta andamentos no DATAJUD/CNJ e grava no banco (alias manual de datajud:verificar)';

    public function handle(): int
    {
        if (! $this->option('force')) {
            $emAndamento = TjspVerificacao::whereIn('status', ['pendente', 'rodando'])->first();
            if ($emAndamento) {
                $this->warn("Já existe uma verificação em andamento (ID {$emAndamento->id}, status: {$emAndamento->status}).");
                $this->line('Use <comment>--force</comment> para forçar uma nova consulta.');
                return self::FAILURE;
            }
        }

        $query = Processo::where('status', 'Ativo')
            ->whereNotNull('numero')
            ->where('numero', '!=', '');

        if ($ids = $this->option('processos')) {
            $query->whereIn('id', explode(',', $ids));
        }

        $processoIds = $query->pluck('id')->toArray();

        if (empty($processoIds)) {
            $this->warn('Nenhum processo ativo com número CNJ encontrado.');
            return self::FAILURE;
        }

        $count = count($processoIds);
        $this->info("Iniciando consulta para {$count} processo(s)...");

        $verificacao = TjspVerificacao::create([
            'status'      => 'pendente',
            'total'       => $count,
            'processado'  => 0,
            'iniciado_em' => now(),
            'filtros'     => json_encode($processoIds),
        ]);

        VerificarAndamentosTjsp::dispatch($verificacao->id, $processoIds);

        $this->info("Verificação #{$verificacao->id} iniciada com {$count} processo(s).");
        $this->line('Acompanhe em: <comment>Ferramentas → Consulta Judicial</comment>');

        return self::SUCCESS;
    }
}
