<?php

namespace App\Console\Commands;

use App\Jobs\VerificarAndamentosTjsp;
use App\Models\{Processo, TjspVerificacao};
use Illuminate\Console\Command;

class VerificarDatajud extends Command
{
    protected $signature   = 'datajud:verificar
                                {--processos= : IDs separados por vírgula (ex: 1,2,3)}
                                {--status=Ativo : Filtrar processos por status}
                                {--dry-run : Mostra o que seria processado sem disparar o job}';

    protected $description = 'Verifica novos andamentos no DataJud/TJSP para todos os processos ativos';

    public function handle(): int
    {
        $this->info('DataJud — Verificação de andamentos');

        // Se ainda há uma verificação rodando, não dispara outra
        $emAndamento = TjspVerificacao::whereIn('status', ['pendente', 'rodando'])->first();
        if ($emAndamento) {
            $this->warn("Já existe uma verificação em andamento (ID {$emAndamento->id}, status: {$emAndamento->status}). Aguarde a conclusão.");
            return self::FAILURE;
        }

        // Monta query
        $query = Processo::where('status', $this->option('status'));

        if ($ids = $this->option('processos')) {
            $query->whereIn('id', explode(',', $ids));
        }

        $processos = $query->get();

        if ($processos->isEmpty()) {
            $this->warn('Nenhum processo encontrado com os critérios informados.');
            return self::SUCCESS;
        }

        $this->line("  Processos selecionados: <comment>{$processos->count()}</comment>");

        if ($this->option('dry-run')) {
            $this->info('Dry-run: nenhuma verificação disparada.');
            $processos->each(fn($p) => $this->line("  · {$p->numero}"));
            return self::SUCCESS;
        }

        $processoIds = $processos->pluck('id')->toArray();

        $verificacao = TjspVerificacao::create([
            'status'      => 'pendente',
            'total'       => count($processoIds),
            'processado'  => 0,
            'iniciado_em' => now(),
            'filtros'     => json_encode($processoIds),
        ]);

        VerificarAndamentosTjsp::dispatch($verificacao->id, $processoIds);

        $this->info("Verificação #{$verificacao->id} disparada para {$processos->count()} processo(s).");
        $this->line('  Acompanhe em: <comment>/tjsp</comment>');

        return self::SUCCESS;
    }
}
