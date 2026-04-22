<?php

namespace App\Livewire;

use Illuminate\Support\Facades\{Auth, DB, Response};
use Livewire\Component;

class ControleHoras extends Component
{
    public string $filtroPeriodoInicio = '';
    public string $filtroPeriodoFim    = '';
    public string $filtroAdvogado      = '';
    public string $filtroCliente       = '';
    public string $filtroProcesso      = '';

    // Modal novo apontamento global
    public bool   $modal       = false;
    public ?int   $editandoId  = null;
    public string $opProcesso  = '';
    public string $opAdvogado  = '';
    public string $opData      = '';
    public string $opHoras     = '';
    public string $opValor     = '';
    public string $opDescricao = '';

    protected $queryString = [
        'filtroPeriodoInicio' => ['except' => ''],
        'filtroPeriodoFim'    => ['except' => ''],
        'filtroAdvogado'      => ['except' => ''],
        'filtroCliente'       => ['except' => ''],
        'filtroProcesso'      => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->filtroPeriodoInicio = now()->startOfMonth()->format('Y-m-d');
        $this->filtroPeriodoFim    = now()->endOfMonth()->format('Y-m-d');
        $this->opData = today()->format('Y-m-d');
    }

    // ── Modal ──────────────────────────────────────────────────

    public function novoApontamento(): void
    {
        $this->resetOp();
        $this->modal = true;
    }

    public function editar(int $id): void
    {
        $row = DB::selectOne(
            'SELECT a.*, pr.numero as processo_numero FROM apontamentos a
             JOIN processos pr ON pr.id = a.processo_id
             WHERE a.id = ? AND pr.tenant_id = ?',
            [$id, tenant_id()]
        );
        if (! $row) return;

        $this->editandoId  = $id;
        $this->opProcesso  = (string) $row->processo_id;
        $this->opAdvogado  = (string) ($row->advogado_id ?? '');
        $this->opData      = substr($row->data, 0, 10);
        $this->opHoras     = $row->horas;
        $this->opValor     = $row->valor ?? '';
        $this->opDescricao = $row->descricao;
        $this->modal       = true;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $this->validate([
            'opProcesso'  => 'required|integer',
            'opData'      => 'required|date',
            'opHoras'     => 'required|numeric|min:0.01',
            'opDescricao' => 'required|min:3',
        ], [
            'opProcesso.required'  => 'Selecione o processo.',
            'opHoras.required'     => 'Informe as horas.',
            'opHoras.min'          => 'Mínimo 0,01 hora.',
            'opDescricao.required' => 'Descrição obrigatória.',
        ]);

        $dados = [
            'processo_id'  => (int) $this->opProcesso,
            'advogado_id'  => $this->opAdvogado ?: null,
            'data'         => $this->opData,
            'horas'        => (float) $this->opHoras,
            'valor'        => $this->opValor !== '' ? (float) $this->opValor : null,
            'descricao'    => trim($this->opDescricao),
            'usuario_id'   => Auth::guard('usuarios')->id(),
        ];

        if ($this->editandoId) {
            DB::table('apontamentos')
                ->where('id', $this->editandoId)
                ->update(array_merge($dados, ['updated_at' => now()]));
        } else {
            DB::table('apontamentos')->insert(array_merge($dados, [
                'created_at' => now(), 'updated_at' => now(),
            ]));
        }

        $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: 'Apontamento salvo.');
    }

    public function excluir(int $id): void
    {
        DB::table('apontamentos')
            ->whereExists(fn($q) => $q->from('processos')
                ->whereColumn('processos.id', 'apontamentos.processo_id')
                ->where('processos.tenant_id', tenant_id()))
            ->where('id', $id)
            ->delete();
        $this->dispatch('toast', tipo: 'success', msg: 'Apontamento excluído.');
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetOp();
        $this->resetErrorBag();
    }

    private function resetOp(): void
    {
        $this->editandoId  = null;
        $this->opProcesso  = '';
        $this->opAdvogado  = '';
        $this->opData      = today()->format('Y-m-d');
        $this->opHoras     = '';
        $this->opValor     = '';
        $this->opDescricao = '';
    }

    // ── Export CSV ─────────────────────────────────────────────

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->queryBase()->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="horas_' . now()->format('Ymd') . '.csv"',
        ];

        return Response::stream(function () use ($rows) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($f, ['Data', 'Processo', 'Cliente', 'Advogado', 'Descrição', 'Horas', 'Valor (R$)'], ';');
            foreach ($rows as $r) {
                fputcsv($f, [
                    \Carbon\Carbon::parse($r->data)->format('d/m/Y'),
                    $r->processo_numero,
                    $r->cliente_nome ?? '',
                    $r->advogado_nome ?? '',
                    $r->descricao,
                    number_format($r->horas, 2, ',', '.'),
                    $r->valor ? number_format($r->valor, 2, ',', '.') : '',
                ], ';');
            }
            fclose($f);
        }, 200, $headers);
    }

    // ── Query base ─────────────────────────────────────────────

    private function queryBase()
    {
        $q = DB::table('apontamentos as a')
            ->join('processos as pr', 'pr.id', '=', 'a.processo_id')
            ->leftJoin('pessoas as adv', 'adv.id', '=', 'a.advogado_id')
            ->leftJoin('pessoas as cli', 'cli.id', '=', 'pr.cliente_id')
            ->where('pr.tenant_id', tenant_id())
            ->select(
                'a.id', 'a.data', 'a.descricao', 'a.horas', 'a.valor',
                'a.processo_id', 'pr.numero as processo_numero',
                'a.advogado_id', 'adv.nome as advogado_nome',
                'pr.cliente_id', 'cli.nome as cliente_nome'
            )
            ->orderByDesc('a.data')->orderByDesc('a.id');

        if ($this->filtroPeriodoInicio) {
            $q->where('a.data', '>=', $this->filtroPeriodoInicio);
        }
        if ($this->filtroPeriodoFim) {
            $q->where('a.data', '<=', $this->filtroPeriodoFim);
        }
        if ($this->filtroAdvogado) {
            $q->where('a.advogado_id', $this->filtroAdvogado);
        }
        if ($this->filtroCliente) {
            $q->where('pr.cliente_id', $this->filtroCliente);
        }
        if ($this->filtroProcesso) {
            $q->where('pr.numero', 'ilike', "%{$this->filtroProcesso}%");
        }

        return $q;
    }

    // ── Render ─────────────────────────────────────────────────

    public function render()
    {
        $tenantId = tenant_id();

        $apontamentos = $this->queryBase()->limit(200)->get();

        $totalHoras = $apontamentos->sum('horas');
        $totalValor = $apontamentos->sum('valor');

        // KPI: horas por advogado (top 5)
        $porAdvogado = $apontamentos
            ->groupBy('advogado_nome')
            ->map(fn($g) => ['nome' => $g->first()->advogado_nome ?? 'Sem advogado', 'horas' => $g->sum('horas')])
            ->sortByDesc('horas')
            ->take(5)
            ->values();

        // KPI: horas por cliente (top 5)
        $porCliente = $apontamentos
            ->groupBy('cliente_nome')
            ->map(fn($g) => ['nome' => $g->first()->cliente_nome ?? 'Sem cliente', 'horas' => $g->sum('horas')])
            ->sortByDesc('horas')
            ->take(5)
            ->values();

        $advogados = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Advogado')
            ->where('p.ativo', true)
            ->where('p.tenant_id', $tenantId)
            ->orderBy('p.nome')
            ->select('p.id', 'p.nome')
            ->get();

        $clientes = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Cliente')
            ->where('p.ativo', true)
            ->where('p.tenant_id', $tenantId)
            ->orderBy('p.nome')
            ->select('p.id', 'p.nome')
            ->get();

        $processos = DB::table('processos')
            ->where('tenant_id', $tenantId)
            ->where('status', 'Ativo')
            ->orderBy('numero')
            ->select('id', 'numero')
            ->limit(500)
            ->get();

        return view('livewire.controle-horas', compact(
            'apontamentos', 'totalHoras', 'totalValor',
            'porAdvogado', 'porCliente',
            'advogados', 'clientes', 'processos'
        ));
    }
}
