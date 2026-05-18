<?php

namespace App\Observers;

use App\Models\ContratoMensal;
use App\Models\FinanceiroLancamento;
use Carbon\Carbon;

class ContratoMensalObserver
{
    public function created(ContratoMensal $contrato): void
    {
        $contrato->gerarMensalidades();
    }

    public function updated(ContratoMensal $contrato): void
    {
        // Cancelar mensalidades e lançamentos futuros se encerrado/suspenso
        if ($contrato->wasChanged('status') && in_array($contrato->status, ['suspenso', 'encerrado'])) {
            $mensalidadesIds = $contrato->mensalidades()
                ->whereIn('status', ['pendente', 'vencido'])
                ->where('vencimento', '>=', today()->toDateString())
                ->pluck('id');

            $lancamentosIds = $contrato->mensalidades()
                ->whereIn('status', ['pendente', 'vencido'])
                ->where('vencimento', '>=', today()->toDateString())
                ->pluck('financeiro_lancamento_id')
                ->filter();

            $contrato->mensalidades()
                ->whereIn('status', ['pendente', 'vencido'])
                ->where('vencimento', '>=', today()->toDateString())
                ->update(['status' => 'cancelado']);

            if ($lancamentosIds->isNotEmpty()) {
                FinanceiroLancamento::withoutGlobalScopes()
                    ->whereIn('id', $lancamentosIds)
                    ->update(['status' => 'cancelado', 'updated_at' => now()]);
            }
        }

        // Regenerar mensalidades futuras se valor ou dia_vencimento mudou e está ativo
        if ($contrato->status === 'ativo' && $contrato->wasChanged(['valor', 'dia_vencimento'])) {
            $contrato->gerarMensalidades(Carbon::today());
        }
    }
}
