<?php

namespace App\Console\Commands;

use App\Models\FinanceiroLancamento;
use Illuminate\Console\Command;

class AtualizarLancamentosAtrasados extends Command
{
    protected $signature   = 'financeiro:atualizar-atrasados';
    protected $description = 'Marca como atrasados os lançamentos vencidos ainda com status previsto';

    public function handle(): int
    {
        $atualizados = FinanceiroLancamento::where('status', 'previsto')
            ->where('vencimento', '<', now()->toDateString())
            ->update(['status' => 'atrasado', 'updated_at' => now()]);

        $this->info("Lançamentos atualizados: {$atualizados}");

        return self::SUCCESS;
    }
}
