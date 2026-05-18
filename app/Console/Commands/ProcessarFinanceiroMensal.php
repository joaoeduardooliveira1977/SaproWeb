<?php

namespace App\Console\Commands;

use App\Models\ReceitaProcesso;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessarFinanceiroMensal extends Command
{
    protected $signature   = 'app:processar-financeiro-mensal';
    protected $description = 'Gera lançamentos de receita por processos para o mês atual (roda dia 1 de cada mês)';

    public function handle(): int
    {
        $receitas = ReceitaProcesso::where('status', 'ativo')->get();
        $total    = 0;

        foreach ($receitas as $receita) {
            $lanc = $receita->gerarMensalidadeDoMes();
            if ($lanc) {
                $total++;
                Log::info("Receita por processo gerada: cliente {$receita->cliente_id}, lançamento #{$lanc->id}, valor R$ {$lanc->valor}");
            }
        }

        $this->info("✓ {$total} lançamento(s) de receita por processos gerado(s).");

        return self::SUCCESS;
    }
}
