<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{Orcamento, Processo, Pessoa, Agenda, Custa, TipoAcao};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    // ── Index ──────────────────────────────────────────────────

    public function index()
    {
        $clientes = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Cliente')
            ->where('p.ativo', true)
            ->where('p.tenant_id', tenant_id())
            ->orderBy('p.nome')
            ->select('p.id', 'p.nome')
            ->get();

        $tiposAcao = TipoAcao::orderBy('descricao')->get(['id', 'descricao']);

        return view('relatorios.index', compact('clientes', 'tiposAcao'));
    }

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

    // ── CSV helper ────────────────────────────────────────────

    private function csv(array $cabecalho, array $linhas, string $nome): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $arquivo = $nome . '_' . now()->format('Ymd_Hi') . '.csv';
        return response()->streamDownload(function () use ($cabecalho, $linhas) {
            $f = fopen('php://output', 'w');
            fputs($f, "\xEF\xBB\xBF"); // BOM para Excel reconhecer UTF-8
            fputcsv($f, $cabecalho, ';');
            foreach ($linhas as $linha) {
                fputcsv($f, array_values((array) $linha), ';');
            }
            fclose($f);
        }, $arquivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
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

        if ($request->formato === 'csv') {
            $linhas = [];
            foreach ($fases as $grupo) {
                foreach ($grupo['processos'] as $p) {
                    $linhas[] = [
                        $grupo['fase'], $p->numero, $p->status,
                        $p->cliente?->nome ?? '', $p->advogado?->nome ?? '',
                    ];
                }
            }
            return $this->csv(['Fase', 'Número', 'Status', 'Cliente', 'Advogado'], $linhas, 'processos_por_fase');
        }

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

        if ($request->formato === 'csv') {
            $linhas = [];
            foreach ($advogados as $grupo) {
                foreach ($grupo['processos'] as $p) {
                    $linhas[] = [
                        $grupo['advogado'], $p->numero, $p->status,
                        $p->cliente?->nome ?? '', $p->fase?->descricao ?? '',
                    ];
                }
            }
            return $this->csv(['Advogado', 'Número', 'Status', 'Cliente', 'Fase'], $linhas, 'processos_por_advogado');
        }

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

    public function custasReembolso(Request $request)
    {
        $custas = \App\Models\Custa::with(['processo.cliente'])
            ->where('pago', true)
            ->where('reembolsavel', true)
            ->whereNull('cobranca_lancamento_id')
            ->orderBy('data')
            ->get();

        // Agrupa por cliente → processo
        $porCliente = $custas->groupBy(fn($c) => $c->processo?->cliente?->nome ?? 'Sem cliente');

        $totalGeral = $custas->sum('valor');
        $totalCustas = $custas->count();

        return $this->pdf('pdf.custas-reembolso', [
            'porCliente'   => $porCliente,
            'totalGeral'   => $totalGeral,
            'totalCustas'  => $totalCustas,
        ], 'Custas a Reembolsar por Cliente');
    }

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

    // ── 7. Andamentos por Cliente ──────────────────────────────

    public function andamentosPorCliente(Request $request)
    {
        [$ini, $fim] = $this->datas($request);
        $clienteId = $request->cliente_id ? (int) $request->cliente_id : null;
        $tipo      = $request->tipo ?? 'todos'; // todos | judiciais | extrajudiciais

        $query = Processo::with(['cliente', 'andamentos' => function ($q) use ($ini, $fim) {
            $q->publico()->whereBetween('data', [$ini->toDateString(), $fim->toDateString()])
              ->orderBy('data');
        }])
        ->when($clienteId, fn ($q) => $q->where('cliente_id', $clienteId))
        ->when($tipo === 'judiciais',      fn ($q) => $q->whereNotNull('numero')->where('numero', '!=', ''))
        ->when($tipo === 'extrajudiciais', fn ($q) => $q->where(fn ($i) => $i->whereNull('numero')->orWhere('numero', '')))
        ->whereHas('andamentos', function ($q) use ($ini, $fim) {
            $q->publico()->whereBetween('data', [$ini->toDateString(), $fim->toDateString()]);
        })
        ->orderBy('numero');

        $processos = $query->get()->filter(fn ($p) => $p->andamentos->isNotEmpty())->values();

        $clienteNome = $clienteId
            ? (Pessoa::find($clienteId)?->nome ?? 'Todos os Clientes')
            : 'Todos os Clientes';

        $tipoLabel = match ($tipo) {
            'judiciais'      => 'Somente Judiciais',
            'extrajudiciais' => 'Somente Extrajudiciais',
            default          => 'Todos',
        };

        return $this->pdf('pdf.andamentos-por-cliente', [
            'processos'   => $processos,
            'clienteNome' => $clienteNome,
            'data_ini'    => $ini->format('d/m/Y'),
            'data_fim'    => $fim->format('d/m/Y'),
            'tipoLabel'   => $tipoLabel,
            'total'       => $processos->sum(fn ($p) => $p->andamentos->count()),
        ], 'Andamentos por Cliente — ' . $clienteNome);
    }

    // ── 8. Honorários em Aberto ────────────────────────────────

    public function honorariosEmAberto(Request $request)
    {
        $clienteId = $request->cliente_id ? (int) $request->cliente_id : null;
        $status    = $request->status ?? 'todos'; // todos | pendente | atrasado

        $parcelas = DB::table('honorario_parcelas as hp')
            ->join('honorarios as h',  'h.id',  '=', 'hp.honorario_id')
            ->join('pessoas as p',     'p.id',  '=', 'h.cliente_id')
            ->leftJoin('processos as pr', 'pr.id', '=', 'h.processo_id')
            ->whereIn('hp.status', ['pendente', 'atrasado'])
            ->when($clienteId, fn($q) => $q->where('h.cliente_id', $clienteId))
            ->when($status !== 'todos', fn($q) => $q->where('hp.status', $status))
            ->orderBy('hp.vencimento')
            ->select(
                'hp.numero_parcela', 'hp.valor', 'hp.vencimento', 'hp.status',
                'h.tipo', 'h.descricao as honorario_desc',
                'p.nome as cliente_nome',
                'pr.numero as processo_numero'
            )
            ->get()
            ->map(fn($r) => [
                'cliente'     => $r->cliente_nome,
                'processo'    => $r->processo_numero ?? '—',
                'honorario'   => $r->honorario_desc,
                'tipo'        => match($r->tipo) {
                    'fixo_mensal'     => 'Fixo Mensal',
                    'exito'           => 'Êxito',
                    'hora'            => 'Por Hora',
                    'ato_diligencia'  => 'Ato/Diligência',
                    default           => $r->tipo,
                },
                'parcela'     => $r->numero_parcela,
                'valor'       => $r->valor,
                'vencimento'  => Carbon::parse($r->vencimento)->format('d/m/Y'),
                'atraso'      => Carbon::parse($r->vencimento)->isPast()
                                    ? Carbon::parse($r->vencimento)->diffInDays(now()) . 'd'
                                    : null,
                'status'      => $r->status,
            ]);

        $clienteNome = $clienteId
            ? (DB::table('pessoas')->where('id', $clienteId)->where('tenant_id', tenant_id())->value('nome') ?? 'Todos')
            : 'Todos os Clientes';

        if ($request->formato === 'csv') {
            $linhas = $parcelas->map(fn($r) => [
                $r['cliente'], $r['processo'], $r['honorario'], $r['tipo'],
                'Parcela ' . $r['parcela'],
                number_format($r['valor'], 2, ',', '.'),
                $r['vencimento'], $r['atraso'] ?? '', $r['status'],
            ])->toArray();
            return $this->csv(
                ['Cliente','Processo','Honorário','Tipo','Parcela','Valor','Vencimento','Atraso','Status'],
                $linhas, 'honorarios_em_aberto'
            );
        }

        return $this->pdf('pdf.honorarios-em-aberto', [
            'parcelas'    => $parcelas,
            'clienteNome' => $clienteNome,
            'status'      => $status,
            'total'       => $parcelas->count(),
            'valor_total' => $parcelas->sum('valor'),
        ], 'Honorários em Aberto');
    }

    // ── 9. Financeiro por Período ──────────────────────────────

    public function financeiroPorPeriodo(Request $request)
    {
        [$ini, $fim] = $this->datas($request);

        $recebimentos = DB::table('recebimentos as r')
            ->join('processos as p',   'p.id',  '=', 'r.processo_id')
            ->leftJoin('pessoas as pe', 'pe.id', '=', 'p.cliente_id')
            ->whereBetween('r.data', [$ini->toDateString(), $fim->toDateString()])
            ->select('r.data', 'r.descricao', 'r.valor', 'r.valor_recebido', 'r.recebido',
                     'p.numero as processo_numero', 'pe.nome as cliente_nome')
            ->orderBy('r.data')
            ->get();

        $pagamentos = DB::table('pagamentos as pg')
            ->join('processos as p',   'p.id',  '=', 'pg.processo_id')
            ->leftJoin('pessoas as pe', 'pe.id', '=', 'p.cliente_id')
            ->whereBetween('pg.data', [$ini->toDateString(), $fim->toDateString()])
            ->select('pg.data', 'pg.descricao', 'pg.valor', 'pg.valor_pago', 'pg.pago',
                     'p.numero as processo_numero', 'pe.nome as cliente_nome')
            ->orderBy('pg.data')
            ->get();

        $totalRecebido = $recebimentos->where('recebido', true)->sum('valor_recebido');
        $totalAReceber = $recebimentos->where('recebido', false)->sum('valor');
        $totalPago     = $pagamentos->where('pago', true)->sum('valor_pago');
        $totalAPagar   = $pagamentos->where('pago', false)->sum('valor');

        if ($request->formato === 'csv') {
            $linhas = [];
            foreach ($recebimentos as $r) {
                $linhas[] = [
                    'Receita', $r->data, $r->processo_numero ?? '', $r->cliente_nome ?? '',
                    $r->descricao, number_format($r->recebido ? $r->valor_recebido : $r->valor, 2, ',', '.'),
                    $r->recebido ? 'Recebido' : 'Pendente',
                ];
            }
            foreach ($pagamentos as $p) {
                $linhas[] = [
                    'Despesa', $p->data, $p->processo_numero ?? '', $p->cliente_nome ?? '',
                    $p->descricao, number_format($p->pago ? $p->valor_pago : $p->valor, 2, ',', '.'),
                    $p->pago ? 'Pago' : 'Pendente',
                ];
            }
            usort($linhas, fn($a, $b) => $a[1] <=> $b[1]);
            return $this->csv(
                ['Tipo','Data','Processo','Cliente','Descrição','Valor','Status'],
                $linhas, 'financeiro_periodo'
            );
        }

        return $this->pdf('pdf.financeiro-periodo', [
            'recebimentos'  => $recebimentos,
            'pagamentos'    => $pagamentos,
            'totalRecebido' => $totalRecebido,
            'totalAReceber' => $totalAReceber,
            'totalPago'     => $totalPago,
            'totalAPagar'   => $totalAPagar,
            'saldo'         => $totalRecebido - $totalPago,
            'data_ini'      => $ini->format('d/m/Y'),
            'data_fim'      => $fim->format('d/m/Y'),
        ], 'Financeiro do Período');
    }

    // ── 10. Processos sem Andamento ────────────────────────────

    public function processosSemAndamento(Request $request)
    {
        $dias   = max(1, (int) ($request->dias ?? 30));
        $status = $request->status ?? 'Ativo';

        $processos = DB::select(
            "SELECT p.id, p.numero, p.status,
                    pe.nome  AS cliente_nome,
                    adv.nome AS advogado_nome,
                    fa.descricao AS fase,
                    MAX(a.data) AS ultimo_andamento,
                    (CURRENT_DATE - MAX(a.data)) AS dias_sem_andamento
             FROM   processos p
             LEFT   JOIN pessoas pe   ON pe.id  = p.cliente_id
             LEFT   JOIN pessoas adv  ON adv.id = p.advogado_id
             LEFT   JOIN fases fa     ON fa.id  = p.fase_id
             LEFT   JOIN andamentos a ON a.processo_id = p.id
             WHERE  (? = 'Todos' OR p.status = ?)
             GROUP  BY p.id, p.numero, p.status, pe.nome, adv.nome, fa.descricao
             HAVING MAX(a.data) IS NULL
                 OR MAX(a.data) < CURRENT_DATE - (? * INTERVAL '1 day')
             ORDER  BY ultimo_andamento ASC NULLS FIRST, p.numero",
            [$status, $status, $dias]
        );

        return $this->pdf('pdf.processos-sem-andamento', [
            'processos' => $processos,
            'dias'      => $dias,
            'status'    => $status,
            'total'     => count($processos),
        ], "Processos sem Andamento há {$dias} dias");
    }

    // ── 11. Produtividade por Advogado ────────────────────────

    public function produtividadeAdvogado(Request $request)
    {
        [$ini, $fim] = $this->datas($request);

        $advogados = \Illuminate\Support\Facades\DB::select("
            SELECT
                pe.id, pe.nome, pe.oab,
                (SELECT COUNT(*) FROM processos WHERE advogado_id = pe.id AND status = 'Ativo') AS processos_ativos,
                (SELECT COUNT(*) FROM processos WHERE advogado_id = pe.id) AS processos_total,
                (SELECT COUNT(*) FROM apontamentos WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_apontamentos,
                (SELECT COALESCE(SUM(horas), 0) FROM apontamentos WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_horas,
                (SELECT COALESCE(SUM(valor), 0) FROM apontamentos WHERE advogado_id = pe.id AND data BETWEEN ? AND ?) AS total_valor,
                (SELECT COUNT(*) FROM andamentos an JOIN usuarios u ON u.id = an.usuario_id WHERE u.pessoa_id = pe.id AND an.data BETWEEN ? AND ?) AS total_andamentos,
                (SELECT COUNT(*) FROM prazos pz JOIN usuarios u ON u.id = pz.responsavel_id WHERE u.pessoa_id = pe.id AND pz.status = 'cumprido') AS prazos_cumpridos,
                (SELECT COUNT(*) FROM prazos pz JOIN usuarios u ON u.id = pz.responsavel_id WHERE u.pessoa_id = pe.id AND pz.status = 'perdido') AS prazos_perdidos,
                (SELECT COUNT(*) FROM prazos pz JOIN usuarios u ON u.id = pz.responsavel_id WHERE u.pessoa_id = pe.id AND pz.status = 'aberto' AND pz.data_prazo < CURRENT_DATE) AS prazos_vencidos
            FROM pessoas pe
            JOIN pessoa_tipos pt ON pt.pessoa_id = pe.id AND pt.tipo = 'Advogado'
            WHERE pe.ativo = true
            ORDER BY pe.nome
        ", [
            $ini->toDateString(), $fim->toDateString(),
            $ini->toDateString(), $fim->toDateString(),
            $ini->toDateString(), $fim->toDateString(),
            $ini->toDateString(), $fim->toDateString(),
        ]);

        return $this->pdf('pdf.produtividade-advogado', [
            'advogados'  => $advogados,
            'dataIniFmt' => $ini->format('d/m/Y'),
            'dataFimFmt' => $fim->format('d/m/Y'),
        ], 'Produtividade por Advogado');
    }

    // ── 12. Processos por Tipo de Ação ────────────────────────

    public function processosPorTipoAcao(Request $request)
    {
        $status = $request->status ?? 'Ativo';

        $tipos = TipoAcao::orderBy('descricao')->get()->map(function ($tipo) use ($status) {
            $processos = Processo::with(['cliente', 'advogado', 'fase'])
                ->where('tipo_acao_id', $tipo->id)
                ->when($status !== 'Todos', fn($q) => $q->where('status', $status))
                ->orderBy('numero')
                ->get();
            return [
                'tipo'      => $tipo->descricao,
                'total'     => $processos->count(),
                'processos' => $processos,
            ];
        })->filter(fn($t) => $t['total'] > 0)->values();

        if ($request->formato === 'csv') {
            $linhas = [];
            foreach ($tipos as $grupo) {
                foreach ($grupo['processos'] as $p) {
                    $linhas[] = [
                        $grupo['tipo'], $p->numero, $p->status,
                        $p->cliente?->nome ?? '', $p->advogado?->nome ?? '',
                        $p->fase?->descricao ?? '',
                    ];
                }
            }
            return $this->csv(
                ['Tipo de Ação', 'Número', 'Status', 'Cliente', 'Advogado', 'Fase'],
                $linhas, 'processos_por_tipo_acao'
            );
        }

        return $this->pdf('pdf.processos-por-tipo-acao', [
            'tipos'  => $tipos,
            'status' => $status,
            'total'  => $tipos->sum('total'),
        ], 'Processos por Tipo de Ação');
    }

    // ── 13. Lista Geral de Processos ──────────────────────────

    public function listaGeral(Request $request)
    {
        $status    = $request->status    ?? 'Ativo';
        $clienteId = $request->cliente_id ? (int) $request->cliente_id : null;
        $tipoId    = $request->tipo_acao_id ? (int) $request->tipo_acao_id : null;

        $processos = Processo::with(['cliente', 'advogado', 'fase', 'tipoAcao', 'risco'])
            ->when($status !== 'Todos', fn($q) => $q->where('status', $status))
            ->when($clienteId, fn($q) => $q->where('cliente_id', $clienteId))
            ->when($tipoId,    fn($q) => $q->where('tipo_acao_id', $tipoId))
            ->orderBy('numero')
            ->get();

        $clienteNome = $clienteId
            ? (Pessoa::find($clienteId)?->nome ?? 'Todos')
            : 'Todos os Clientes';

        $tipoNome = $tipoId
            ? (TipoAcao::find($tipoId)?->descricao ?? 'Todos')
            : 'Todos os Tipos';

        if ($request->formato === 'csv') {
            $linhas = $processos->map(fn($p) => [
                $p->numero,
                $p->status,
                $p->cliente?->nome ?? '',
                $p->advogado?->nome ?? '',
                $p->tipoAcao?->descricao ?? '',
                $p->fase?->descricao ?? '',
                $p->risco?->descricao ?? '',
                $p->vara ?? '',
                $p->data_distribuicao?->format('d/m/Y') ?? '',
                $p->valor_causa ? number_format($p->valor_causa, 2, ',', '.') : '',
            ])->toArray();
            return $this->csv(
                ['Número','Status','Cliente','Advogado','Tipo de Ação','Fase','Risco','Vara','Distribuição','Valor da Causa'],
                $linhas, 'lista_geral_processos'
            );
        }

        return $this->pdf('pdf.lista-geral-processos', [
            'processos'   => $processos,
            'status'      => $status,
            'clienteNome' => $clienteNome,
            'tipoNome'    => $tipoNome,
            'total'       => $processos->count(),
        ], 'Lista Geral de Processos');
    }

    // ── 14. Relatório Financeiro Mensal ───────────────────────────

    public function relatorioFinanceiroMensal(Request $request)
    {
        $mes       = $request->mes ?? now()->format('Y-m');
        $clienteId = $request->cliente_id ? (int) $request->cliente_id : null;

        [$ano, $numMes] = explode('-', $mes);

        $mesesNomes = [
            '01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril',
            '05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto',
            '09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro',
        ];
        $mesNome = ($mesesNomes[$numMes] ?? $numMes) . '/' . $ano;

        $lancamentos = DB::table('financeiro_lancamentos as fl')
            ->join('pessoas as p', 'p.id', '=', 'fl.cliente_id')
            ->leftJoin('contratos as ct', 'ct.id', '=', 'fl.contrato_id')
            ->whereRaw("TO_CHAR(fl.vencimento, 'YYYY-MM') = ?", [$mes])
            ->whereNotIn('fl.status', ['cancelado'])
            ->when($clienteId, fn($q) => $q->where('fl.cliente_id', $clienteId))
            ->select(
                'fl.id', 'fl.tipo', 'fl.descricao', 'fl.valor', 'fl.valor_pago',
                'fl.status', 'fl.vencimento', 'fl.data_pagamento', 'fl.forma_pagamento',
                'p.nome as cliente_nome',
                'ct.descricao as contrato_desc'
            )
            ->orderBy('fl.tipo')
            ->orderBy('fl.vencimento')
            ->orderBy('p.nome')
            ->get();

        $receitas  = $lancamentos->where('tipo', 'receita');
        $despesas  = $lancamentos->where('tipo', 'despesa');
        $repassess = $lancamentos->where('tipo', 'repasse');

        $totais = [
            'receita_prevista' => $receitas->whereIn('status', ['previsto','atrasado'])->sum('valor'),
            'receita_recebida' => $receitas->where('status', 'recebido')->sum('valor_pago'),
            'receita_atrasada' => $receitas->where('status', 'atrasado')->sum('valor'),
            'despesa_total'    => $despesas->sum('valor'),
            'despesa_paga'     => $despesas->where('status', 'recebido')->sum('valor_pago'),
            'repasse_total'    => $repassess->sum('valor'),
        ];
        $totais['saldo'] = $totais['receita_recebida'] - $totais['despesa_paga'];

        $clienteNome = $clienteId
            ? (DB::table('pessoas')->where('id', $clienteId)->value('nome') ?? 'Todos')
            : 'Todos os Clientes';

        if ($request->formato === 'csv') {
            $linhas = $lancamentos->map(fn($l) => [
                ucfirst($l->tipo),
                $l->cliente_nome,
                $l->descricao,
                $l->contrato_desc ?? '',
                Carbon::parse($l->vencimento)->format('d/m/Y'),
                number_format($l->valor, 2, ',', '.'),
                $l->valor_pago ? number_format($l->valor_pago, 2, ',', '.') : '',
                $l->data_pagamento ? Carbon::parse($l->data_pagamento)->format('d/m/Y') : '',
                $l->forma_pagamento ?? '',
                match($l->status) {
                    'previsto' => 'Previsto',
                    'atrasado' => 'Atrasado',
                    'recebido' => 'Recebido/Pago',
                    default    => $l->status,
                },
            ])->toArray();
            return $this->csv(
                ['Tipo','Cliente','Descrição','Contrato','Vencimento','Valor','Valor Pago','Data Pgto','Forma Pgto','Status'],
                $linhas,
                'financeiro_mensal_' . str_replace('-', '_', $mes)
            );
        }

        return $this->pdf('pdf.financeiro-mensal', [
            'lancamentos' => $lancamentos,
            'receitas'    => $receitas,
            'despesas'    => $despesas,
            'repassess'   => $repassess,
            'totais'      => $totais,
            'mesNome'     => $mesNome,
            'clienteNome' => $clienteNome,
        ], 'Relatório Financeiro — ' . $mesNome);
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

    // ── 15. Extrato Financeiro por Cliente ────────────────────

    public function extratoCliente(Request $request)
    {
        $clienteId = $request->cliente_id ? (int) $request->cliente_id : null;
        if (! $clienteId) {
            abort(400, 'Selecione um cliente.');
        }

        [$ini, $fim] = $this->datas($request);

        $cliente = Pessoa::findOrFail($clienteId);
        $tenant  = DB::table('tenants')->where('id', tenant_id())->first();

        $lancamentos = DB::table('financeiro_lancamentos as fl')
            ->leftJoin('contratos as ct', 'ct.id', '=', 'fl.contrato_id')
            ->leftJoin('processos as pr', 'pr.id', '=', 'fl.processo_id')
            ->where('fl.cliente_id', $clienteId)
            ->where('fl.tenant_id', tenant_id())
            ->whereNotIn('fl.status', ['cancelado'])
            ->whereBetween('fl.vencimento', [$ini->toDateString(), $fim->toDateString()])
            ->select(
                'fl.id', 'fl.tipo', 'fl.descricao', 'fl.valor', 'fl.valor_pago',
                'fl.status', 'fl.vencimento', 'fl.data_pagamento', 'fl.forma_pagamento',
                'fl.numero_parcela', 'fl.total_parcelas',
                'ct.descricao as contrato_desc',
                'pr.numero as processo_numero'
            )
            ->orderBy('fl.vencimento')
            ->get();

        $custasReembolso = DB::table('custas as c')
            ->join('processos as p', 'p.id', '=', 'c.processo_id')
            ->where('p.cliente_id', $clienteId)
            ->where('p.tenant_id', tenant_id())
            ->where('c.pago', true)
            ->where('c.reembolsavel', true)
            ->whereNull('c.cobranca_lancamento_id')
            ->select('c.data', 'c.descricao', 'c.valor', 'p.numero as processo_numero')
            ->orderBy('c.data')
            ->get();

        $receitas = $lancamentos->where('tipo', 'receita');
        $despesas = $lancamentos->where('tipo', 'despesa');

        $totais = [
            'cobrado'          => $receitas->sum('valor'),
            'recebido'         => $receitas->where('status', 'recebido')->sum('valor_pago'),
            'a_receber'        => $receitas->whereIn('status', ['previsto', 'atrasado'])->sum('valor'),
            'atrasado'         => $receitas->where('status', 'atrasado')->sum('valor'),
            'custas_pendentes' => $custasReembolso->sum('valor'),
            'custas_qtd'       => $custasReembolso->count(),
        ];
        $totais['saldo_devedor'] = $totais['a_receber'] + $totais['custas_pendentes'];

        if ($request->formato === 'csv') {
            $linhas = [];
            foreach ($lancamentos as $l) {
                $linhas[] = [
                    ucfirst($l->tipo),
                    $l->descricao,
                    $l->contrato_desc ?? '',
                    $l->processo_numero ?? '',
                    Carbon::parse($l->vencimento)->format('d/m/Y'),
                    number_format($l->valor, 2, ',', '.'),
                    $l->valor_pago ? number_format($l->valor_pago, 2, ',', '.') : '',
                    $l->data_pagamento ? Carbon::parse($l->data_pagamento)->format('d/m/Y') : '',
                    $l->forma_pagamento ?? '',
                    match($l->status) {
                        'previsto' => 'Previsto', 'atrasado' => 'Atrasado',
                        'recebido' => 'Recebido', default => $l->status,
                    },
                ];
            }
            foreach ($custasReembolso as $c) {
                $linhas[] = [
                    'Reembolso de Custa', $c->descricao, '', $c->processo_numero ?? '',
                    Carbon::parse($c->data)->format('d/m/Y'),
                    number_format($c->valor, 2, ',', '.'), '', '', '', 'Pendente',
                ];
            }
            return $this->csv(
                ['Tipo','Descrição','Contrato','Processo','Vencimento','Valor','Valor Pago','Data Pgto','Forma Pgto','Status'],
                $linhas, 'extrato_' . \Illuminate\Support\Str::slug($cliente->nome)
            );
        }

        return $this->pdf('pdf.extrato-cliente', [
            'cliente'         => $cliente,
            'tenant'          => $tenant,
            'lancamentos'     => $lancamentos,
            'receitas'        => $receitas,
            'despesas'        => $despesas,
            'custasReembolso' => $custasReembolso,
            'totais'          => $totais,
            'data_ini'        => $ini->format('d/m/Y'),
            'data_fim'        => $fim->format('d/m/Y'),
        ], 'Extrato — ' . $cliente->nome);
    }

    // ── PDF Orçamento ──────────────────────────────────────────

    public function orcamentoPdf(int $id)
    {
        $orc = Orcamento::findOrFail($id);

        $escritorio = DB::table('tenants')->where('id', tenant_id())->value('nome') ?? 'Escritório';

        return $this->pdf('pdf.orcamento', [
            'orc'        => $orc,
            'escritorio' => $escritorio,
        ], "Proposta {$orc->numero}");
    }

    // ── PDF Contrato ───────────────────────────────────────────

    public function contratoPdf(int $id)
    {
        $contrato = \App\Models\Contrato::with(['cliente', 'advogadoResponsavel', 'processo'])->findOrFail($id);
        $tenant   = DB::table('tenants')->where('id', tenant_id())->first();

        return $this->pdf('pdf.contrato', [
            'contrato' => $contrato,
            'tenant'   => $tenant,
        ], "Contrato — {$contrato->cliente?->nome}");
    }
}
