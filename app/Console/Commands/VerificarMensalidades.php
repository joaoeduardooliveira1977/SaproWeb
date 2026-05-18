<?php

namespace App\Console\Commands;

use App\Models\ContratoMensal;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarMensalidades extends Command
{
    protected $signature   = 'app:verificar-mensalidades';
    protected $description = 'Marca mensalidades vencidas como atrasadas e gera parcelas futuras para contratos ativos';

    public function handle(): int
    {
        // 1. Marcar pendentes vencidas como atrasadas
        $atrasadas = DB::table('mensalidades')
            ->where('status', 'pendente')
            ->where('vencimento', '<', today()->toDateString())
            ->whereNull('deleted_at')
            ->update([
                'status'     => 'atrasado',
                'updated_at' => now(),
            ]);

        $this->info("✓ {$atrasadas} mensalidade(s) marcada(s) como atrasada.");

        // 2. Gerar parcelas do próximo mês para contratos ativos
        $proximo = Carbon::now()->addMonth()->startOfMonth();
        $geradas  = 0;

        ContratoMensal::where('status', 'ativo')
            ->where(function ($q) use ($proximo) {
                $q->whereNull('data_fim')
                  ->orWhere('data_fim', '>=', $proximo->toDateString());
            })
            ->each(function (ContratoMensal $contrato) use ($proximo, &$geradas) {
                $geradas += $contrato->gerarMensalidades($proximo, $proximo);
            });

        $this->info("✓ {$geradas} mensalidade(s) futura(s) gerada(s).");

        return self::SUCCESS;
    }
}
