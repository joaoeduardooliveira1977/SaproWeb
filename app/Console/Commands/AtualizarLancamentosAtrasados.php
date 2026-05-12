<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AtualizarLancamentosAtrasados extends Command
{
    protected $signature   = 'financeiro:atualizar-atrasados';
    protected $description = 'Marca como atrasado os lançamentos vencidos e não recebidos';

    public function handle(): int
    {
        $total = DB::table('financeiro_lancamentos')
            ->where('status', 'previsto')
            ->where('vencimento', '<', today())
            ->update([
                'status'     => 'atrasado',
                'updated_at' => now(),
            ]);

        $this->info("✓ {$total} lançamento(s) marcado(s) como atrasado.");

        return self::SUCCESS;
    }
}
