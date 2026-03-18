<?php

namespace App\Livewire;

use App\Models\{AaspPublicacao, Processo, TjspVerificacao};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MonitoramentoJuridico extends Component
{
    public string $aba = 'resumo'; // resumo | publicacoes | andamentos | alertas

    protected $queryString = [
        'aba' => ['except' => 'resumo'],
    ];

    public function render()
    {
        $hoje     = today();
        $semana   = today()->subDays(7);
        $mes      = today()->subDays(30);

        // ── KPIs do dia ────────────────────────────────────────
        $pubsHoje = AaspPublicacao::whereDate('created_at', $hoje)->count();

        $ultimaVerif = TjspVerificacao::where('status', 'concluido')
            ->latest('concluido_em')->first();

        $andamentosHoje = DB::table('andamentos')
            ->whereDate('created_at', $hoje)
            ->count();

        $andamentosSemana = DB::table('andamentos')
            ->where('created_at', '>=', $semana)
            ->count();

        // Processos sem consulta há mais de 30 dias
        $processosSemUpdate = Processo::where('status', 'Ativo')
            ->where(fn($q) => $q
                ->whereNull('tjsp_ultima_consulta')
                ->orWhere('tjsp_ultima_consulta', '<', $mes)
            )->count();

        // Prazos críticos (≤ 3 dias, abertos, fatais ou normais)
        $prazosCriticos = DB::table('prazos')
            ->where('status', 'aberto')
            ->whereBetween('data_prazo', [$hoje, $hoje->copy()->addDays(3)])
            ->count();

        $prazosFatais = DB::table('prazos')
            ->where('status', 'aberto')
            ->where('prazo_fatal', true)
            ->where('data_prazo', '>=', $hoje)
            ->count();

        // ── Publicações recentes vinculadas a processos ────────
        try {
            $publicacoesVinculadas = AaspPublicacao::with('processo.cliente')
                ->whereNotNull('processo_id')
                ->latest()
                ->limit(20)
                ->get();
        } catch (\Throwable) {
            $publicacoesVinculadas = collect();
        }

        // ── Últimos andamentos importados do DataJud ────────────
        $andamentosRecentes = DB::table('andamentos as a')
            ->join('processos as p', 'p.id', '=', 'a.processo_id')
            ->leftJoin('pessoas as pe', 'pe.id', '=', 'p.cliente_id')
            ->select('a.id', 'a.data', 'a.descricao', 'a.created_at',
                     'p.numero', 'pe.nome as cliente_nome')
            ->where('a.created_at', '>=', $semana)
            ->orderByDesc('a.created_at')
            ->limit(50)
            ->get();

        // ── Processos sem atualização (alerta) ─────────────────
        $processosDesatualizados = Processo::with('cliente')
            ->where('status', 'Ativo')
            ->where(fn($q) => $q
                ->whereNull('tjsp_ultima_consulta')
                ->orWhere('tjsp_ultima_consulta', '<', $mes)
            )
            ->orderBy('tjsp_ultima_consulta')
            ->limit(30)
            ->get();

        // ── Histórico de verificações DataJud ──────────────────
        $verificacoes = TjspVerificacao::orderByDesc('id')->limit(5)->get();

        return view('livewire.monitoramento-juridico', compact(
            'pubsHoje', 'andamentosHoje', 'andamentosSemana',
            'processosSemUpdate', 'prazosCriticos', 'prazosFatais',
            'publicacoesVinculadas', 'andamentosRecentes',
            'processosDesatualizados', 'ultimaVerif', 'verificacoes',
        ));
    }
}
