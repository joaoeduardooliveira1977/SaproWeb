<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Processo, Pessoa, Agenda, Custa};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    // ── Helpers ────────────────────────────────────────────────

    private function datas(Request $request): array
    {
        $ini = $request->data_ini
            ? Carbon::parse($request->data_ini)->startOfDay()
            : Carbon::now()->startOfMonth();
        $fim = $request->data_fim
            ? Carbon::parse($request->data_fim)->endOfDay()
            : Carbon::now()->endOfDay();
        return [$ini, $fim];
    }

    private function pdf(string $view, array $dados, string $titulo): \Illuminate\Http\Response
    {
        $dados['titulo']    = $titulo;
        $dados['gerado_em'] = Carbon::now()->format('d/m/Y H:i');
        $dados['usuario']   = auth('usuarios')->user()->login ?? 'sistema';

        $pdf = Pdf::loadView($view, $dados)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'  => 'DejaVu Sans',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ]);

        $nome = str_replace(' ', '_', strtolower($titulo)) . '_' . now()->format('Ymd_Hi') . '.pdf';
        return $pdf->download($nome);
    }

    // ── 1. Processos por Fase ──────────────────────────────────

    public function processosPorFase(Request $request)
    {
        $status = $request->status ?? 'Ativo';

        $fases = DB::table('fases')
            ->orderBy('ordem')
            ->get()
            ->map(function ($fase) use ($status) {
                $processos = Processo::with(['cliente', 'advogado', 'risco'])
                    ->where('fase_id', $fase->id)
                    ->when($status !== 'Todos', fn($q) => $q->where('status', $status))
                    ->orderBy('numero')
                    ->get();
                return [
                    'fase'      => $fase->descricao,
                    'total'     => $processos->count(),
                    'processos' => $processos,
                ];
            })
            ->filter(fn($f) => $f['total'] > 0)
            ->values();

        return $this->pdf('pdf.processos-por-fase', [
            'fases'  => $fases,
            'status' => $status,
            'total'  => $fases->sum('total'),
        ], 'Processos por Fase');
    }

    // ── 2. Processos por Advogado ──────────────────────────────

    public function processosPorAdvogado(Request $request)
    {
        $status = $request->status ?? 'Ativo';

        $advogados = Pessoa::doTipo('Advogado')
            ->ativos()
            ->orderBy('nome')
            ->get()
            ->map(function ($adv) use ($status) {
                $processos = Processo::with(['cliente', 'fase', 'risco'])
                    ->where('advogado_id', $adv->id)
                    ->when($status !== 'Todos', fn($q) => $q->where('status', $status))
                    ->orderBy('numero')
                    ->get();
                return [
                    'advogado'  => $adv->nome,
                    'total'     => $processos->count(),
                    'processos' => $processos,
                ];
            })
            ->filter(fn($a) => $a['total'] > 0)
            ->values();

        return $this->pdf('pdf.processos-por-advogado', [
            'advogados' => $advogados,
            'status'    => $status,
            'total'     => $advogados->sum('total'),
        ], 'Processos por Advogado');
    }

    // ── 3. Processos por Risco ─────────────────────────────────

    public function processosPorRisco(Request $request)
    {
        $status = $request->status ?? 'Ativo';

        $riscos = DB::table('graus_risco')
            ->orderBy('codigo')
            ->get()
            ->map(function ($risco) use ($status) {
                $processos = Processo::with(['cliente', 'advogado', 'fase'])
                    ->where('risco_id', $risco->id)
                    ->when($status !== 'Todos', fn($q) => $q->where('status', $status))
                    ->orderBy('numero')
                    ->get();
                return [
                    'risco'     => $risco->descricao,
                    'cor'       => $risco->cor_hex,
                    'total'     => $processos->count(),
                    'processos' => $processos,
                ];
            })
            ->filter(fn($r) => $r['total'] > 0)
            ->values();

        return $this->pdf('pdf.processos-por-risco', [
            'riscos' => $riscos,
            'status' => $status,
            'total'  => $riscos->sum('total'),
        ], 'Processos por Risco');
    }

    // ── 4. Agenda do Período ───────────────────────────────────

    public function agendaPeriodo(Request $request)
    {
        [$ini, $fim] = $this->datas($request);

        $compromissos = Agenda::with(['processo.cliente', 'responsavel'])
            ->whereBetween('data_hora', [$ini, $fim])
            ->orderBy('data_hora')
            ->get()
            ->map(fn($a) => [
                'data_hora'  => $a->data_hora->format('d/m/Y H:i'),
                'titulo'     => $a->titulo,
                'tipo'       => $a->tipo,
                'urgente'    => $a->urgente,
                'concluido'  => $a->concluido,
                'local'      => $a->local,
                'processo'   => $a->processo?->numero,
                'cliente'    => $a->processo?->cliente?->nome,
                'responsavel'=> $a->responsavel?->login,
            ]);

        return $this->pdf('pdf.agenda-periodo', [
            'compromissos' => $compromissos,
            'data_ini'     => $ini->format('d/m/Y'),
            'data_fim'     => $fim->format('d/m/Y'),
            'total'        => $compromissos->count(),
        ], 'Agenda do Período');
    }

    // ── 5. Custas Pendentes ────────────────────────────────────

    public function custasPendentes(Request $request)
    {
        [$ini, $fim] = $this->datas($request);

        $custas = Custa::with(['processo.cliente', 'processo.advogado'])
            ->where('pago', false)
            ->whereBetween('data', [$ini, $fim])
            ->orderBy('data')
            ->get()
            ->map(fn($c) => [
                'data'      => $c->data->format('d/m/Y'),
                'processo'  => $c->processo?->numero,
                'cliente'   => $c->processo?->cliente?->nome,
                'descricao' => $c->descricao,
                'valor'     => $c->valor,
            ]);

        return $this->pdf('pdf.custas-pendentes', [
            'custas'   => $custas,
            'data_ini' => $ini->format('d/m/Y'),
            'data_fim' => $fim->format('d/m/Y'),
            'total'    => $custas->count(),
            'valor_total' => $custas->sum('valor'),
        ], 'Custas Pendentes');
    }

    // ── 6. Aniversários de Clientes ────────────────────────────

    public function aniversarios(Request $request)
    {
        $mes = $request->mes ?? now()->month;

        $clientes = Pessoa::doTipo('Cliente')
            ->ativos()
            ->whereNotNull('data_nascimento')
            ->whereMonth('data_nascimento', $mes)
            ->orderByRaw('DAY(data_nascimento)')
            ->get()
            ->map(fn($p) => [
                'nome'       => $p->nome,
                'nascimento' => $p->data_nascimento->format('d/m/Y'),
                'dia'        => $p->data_nascimento->format('d'),
                'idade'      => $p->data_nascimento->age,
                'email'      => $p->email,
                'telefone'   => $p->celular ?? $p->telefone,
            ]);

        $meses = [
            1=>'Janeiro',2=>'Fevereiro',3=>'Março',4=>'Abril',
            5=>'Maio',6=>'Junho',7=>'Julho',8=>'Agosto',
            9=>'Setembro',10=>'Outubro',11=>'Novembro',12=>'Dezembro',
        ];

        return $this->pdf('pdf.aniversarios', [
            'clientes'  => $clientes,
            'mes_nome'  => $meses[$mes],
            'total'     => $clientes->count(),
        ], 'Aniversários de Clientes — ' . $meses[$mes]);
    }
}
