<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CalcularScoreProcessos extends Command
{
    protected $signature   = 'processos:calcular-score {--tenant= : ID do tenant específico}';
    protected $description = 'Recalcula o score de risco (critico/atencao/normal) de todos os processos ativos';

    public function handle(): void
    {
        $tenantId = $this->option('tenant');

        $query = DB::table('processos')->where('status', 'Ativo');
        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $processos = $query->select('id', 'tenant_id', 'score')->get();

        $hoje       = Carbon::today();
        $em3dias    = $hoje->copy()->addDays(3);
        $em7dias    = $hoje->copy()->addDays(7);
        $ha30dias   = $hoje->copy()->subDays(30);
        $ha60dias   = $hoje->copy()->subDays(60);

        $atualizados = ['critico' => 0, 'atencao' => 0, 'normal' => 0];

        foreach ($processos as $proc) {
            $score = $this->calcularScore($proc->id, $hoje, $em3dias, $em7dias, $ha30dias, $ha60dias);

            if ($score !== $proc->score) {
                DB::table('processos')
                    ->where('id', $proc->id)
                    ->update(['score' => $score, 'updated_at' => now()]);
            }

            $atualizados[$score]++;
        }

        $total = $processos->count();
        $this->info("Score calculado para {$total} processos.");
        $this->line("  Crítico: {$atualizados['critico']}");
        $this->line("  Atenção: {$atualizados['atencao']}");
        $this->line("  Normal : {$atualizados['normal']}");
    }

    private function calcularScore(int $processoId, Carbon $hoje, Carbon $em3dias, Carbon $em7dias, Carbon $ha30dias, Carbon $ha60dias): string
    {
        // ── Prazos ────────────────────────────────────────────────
        $temPrazoVencido = DB::table('prazos')
            ->where('processo_id', $processoId)
            ->where('status', 'aberto')
            ->where('data_prazo', '<', $hoje)
            ->exists();

        if ($temPrazoVencido) return 'critico';

        $temPrazoFatal3d = DB::table('prazos')
            ->where('processo_id', $processoId)
            ->where('status', 'aberto')
            ->where('prazo_fatal', true)
            ->whereBetween('data_prazo', [$hoje, $em3dias])
            ->exists();

        if ($temPrazoFatal3d) return 'critico';

        // ── Audiências ────────────────────────────────────────────
        $temAudiencia3d = DB::table('audiencias')
            ->where('processo_id', $processoId)
            ->where('status', 'agendada')
            ->whereBetween('data_hora', [$hoje, $em3dias])
            ->exists();

        if ($temAudiencia3d) return 'critico';

        // ── Sem andamento há 60 dias → crítico ────────────────────
        $ultimoAndamento = DB::table('andamentos')
            ->where('processo_id', $processoId)
            ->max('created_at');

        if (! $ultimoAndamento || Carbon::parse($ultimoAndamento)->lt($ha60dias)) {
            return 'critico';
        }

        // ── Prazo nos próximos 7 dias → atenção ───────────────────
        $temPrazo7d = DB::table('prazos')
            ->where('processo_id', $processoId)
            ->where('status', 'aberto')
            ->whereBetween('data_prazo', [$hoje, $em7dias])
            ->exists();

        if ($temPrazo7d) return 'atencao';

        // ── Audiência nos próximos 7 dias → atenção ───────────────
        $temAudiencia7d = DB::table('audiencias')
            ->where('processo_id', $processoId)
            ->where('status', 'agendada')
            ->whereBetween('data_hora', [$hoje, $em7dias])
            ->exists();

        if ($temAudiencia7d) return 'atencao';

        // ── Sem andamento há 30 dias → atenção ────────────────────
        if (Carbon::parse($ultimoAndamento)->lt($ha30dias)) {
            return 'atencao';
        }

        return 'normal';
    }
}
