<?php

namespace App\Livewire;

use App\Models\{FinanceiroLancamento, Pessoa, Contrato};
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class FinanceiroCentral extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filtros ───────────────────────────────────────────────
    public string $busca          = '';
    public string $filtroStatus   = '';
    public string $filtroTipo     = '';
    public string $filtroMes      = '';
    public string $filtroCliente  = '';
    public string $filtroProcesso = '';

    protected $queryString = [
        'busca'          => ['except' => ''],
        'filtroStatus'   => ['except' => ''],
        'filtroTipo'     => ['except' => ''],
        'filtroMes'      => ['except' => ''],
        'filtroCliente'  => ['except' => ''],
        'filtroProcesso' => ['except' => ''],
    ];

    // ── Modal lançamento ──────────────────────────────────────
    public bool   $modal        = false;
    public ?int   $lancamentoId = null;
    public int    $clienteId    = 0;
    public ?int   $contratoId   = null;
    public string $processoId   = '';
    public string $tipo         = 'receita';
    public string $descricao    = '';
    public string $valor        = '';
    public string $vencimento   = '';
    public string $observacoes  = '';

    // ── Anexos ────────────────────────────────────────────────
    public $anexo       = null;
    public array $anexos = [];

    // ── Modal: registrar pagamento ────────────────────────────
    public bool   $modalPagamento  = false;
    public ?int   $pagamentoLancId = null;
    public string $dataPagamento   = '';
    public string $valorPago       = '';
    public string $formaPagamento  = 'pix';
    public string $pagamentoTipo   = 'receita';

    // ── Ordenação ─────────────────────────────────────────────
    public string $ordenarPor = 'vencimento';
    public string $ordenarDir = 'asc';

    // ── Dados auxiliares ──────────────────────────────────────
    public array $clientes     = [];
    public array $fornecedores = [];
    public array $contratos    = [];

    public function mount(): void
    {
        $this->filtroMes = now()->format('Y-m');
        $this->carregarAuxiliares();

        $novo = request()->query('novo');
        if (in_array($novo, ['receita', 'despesa'])) {
            $this->tipo = $novo;
            $this->abrirModal();
        }
    }

    private function carregarAuxiliares(): void
    {
        $tenantId = tenant_id();

        $this->clientes = DB::table('pessoas as p')
            ->where('p.ativo', true)
            ->when($tenantId, fn($q) => $q->where('p.tenant_id', $tenantId))
            ->where(function ($q) use ($tenantId) {
                $q->whereExists(fn($s) => $s->from('pessoa_tipos')->whereColumn('pessoa_id', 'p.id')->where('tipo', 'Cliente'))
                  ->orWhereExists(fn($s) => $s->from('processos')->whereColumn('cliente_id', 'p.id')
                      ->when($tenantId, fn($s2) => $s2->where('tenant_id', $tenantId)))
                  ->orWhereExists(fn($s) => $s->from('financeiro_lancamentos')->whereColumn('cliente_id', 'p.id')
                      ->when($tenantId, fn($s2) => $s2->where('tenant_id', $tenantId)));
            })
            ->orderBy('p.nome')
            ->distinct()
            ->get(['p.id', 'p.nome'])
            ->map(fn($r) => ['id' => $r->id, 'nome' => $r->nome])
            ->values()
            ->toArray();

        $this->fornecedores = DB::table('pessoas as p')
            ->join('pessoa_tipos as pt', 'pt.pessoa_id', '=', 'p.id')
            ->where('pt.tipo', 'Fornecedor')
            ->where('p.ativo', true)
            ->when($tenantId, fn($q) => $q->where('p.tenant_id', $tenantId))
            ->orderBy('p.nome')
            ->get(['p.id', 'p.nome'])
            ->map(fn($r) => ['id' => $r->id, 'nome' => $r->nome])
            ->toArray();
    }

    public function updatedClienteId(): void
    {
        $this->contratos = $this->clienteId
            ? Contrato::where('cliente_id', $this->clienteId)->where('status', 'ativo')->get(['id','descricao'])->toArray()
            : [];
        $this->contratoId = null;
    }

    // ── Filtros reset page ────────────────────────────────────
    public function updatingBusca():          void { $this->resetPage(); }
    public function updatingFiltroStatus():   void { $this->resetPage(); }
    public function updatingFiltroTipo():     void { $this->resetPage(); }
    public function updatingFiltroMes():      void { $this->resetPage(); }
    public function updatingFiltroCliente():  void { $this->resetPage(); }
    public function updatingFiltroProcesso(): void { $this->resetPage(); }

    public function ordenar(string $coluna): void
    {
        if ($this->ordenarPor === $coluna) {
            $this->ordenarDir = $this->ordenarDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $coluna;
            $this->ordenarDir = 'asc';
        }
        $this->resetPage();
    }

    public function mesAnterior(): void
    {
        $this->filtroMes = \Carbon\Carbon::createFromFormat('Y-m', $this->filtroMes ?: now()->format('Y-m'))
            ->subMonth()->format('Y-m');
        $this->resetPage();
    }

    public function mesSeguinte(): void
    {
        $this->filtroMes = \Carbon\Carbon::createFromFormat('Y-m', $this->filtroMes ?: now()->format('Y-m'))
            ->addMonth()->format('Y-m');
        $this->resetPage();
    }

    // ── Modal lançamento ──────────────────────────────────────
    public function abrirModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->lancamentoId = $id;
        $this->contratoId   = null;
        $this->contratos    = [];
        $this->anexo        = null;
        $this->anexos       = [];

        if ($id) {
            $l = FinanceiroLancamento::findOrFail($id);
            $this->clienteId   = $l->cliente_id;
            $this->contratoId  = $l->contrato_id;
            $this->processoId  = $l->processo_id ?? '';
            $this->tipo        = $l->tipo;
            $this->descricao   = $l->descricao;
            $this->valor       = number_format($l->valor, 2, ',', '.');
            $this->vencimento  = $l->vencimento->format('Y-m-d');
            $this->observacoes = $l->observacoes ?? '';
            $this->updatedClienteId();
            $this->carregarAnexos($id);
        } else {
            $this->clienteId   = 0;
            $this->processoId  = '';
            $this->tipo        = 'receita';
            $this->descricao   = '';
            $this->valor       = '';
            $this->vencimento  = now()->format('Y-m-d');
            $this->observacoes = '';
        }

        $this->modal = true;
    }

    public function fecharModal(): void
    {
        $this->modal      = false;
        $this->anexo      = null;
        $this->anexos     = [];
        $this->resetErrorBag();
    }

    private function carregarAnexos(int $lancamentoId): void
    {
        $this->anexos = DB::table('financeiro_lancamento_anexos')
            ->where('lancamento_id', $lancamentoId)
            ->where('tenant_id', Auth::guard('usuarios')->user()?->tenant_id)
            ->orderBy('created_at')
            ->get()
            ->toArray();
    }

    public function salvar(): void
    {
        $this->validate([
            'clienteId'  => 'required|integer|min:1',
            'tipo'       => 'required|string',
            'descricao'  => 'required|string|max:300',
            'valor'      => 'required',
            'vencimento' => 'required|date',
            'anexo'      => 'nullable|file|max:20480|mimes:pdf,jpg,jpeg,png,gif,doc,docx,xls,xlsx,txt',
        ], [
            'clienteId.min'      => 'Selecione o cliente.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required'     => 'Informe o valor.',
            'anexo.max'          => 'O arquivo não pode exceder 20MB.',
            'anexo.mimes'        => 'Formato inválido. Use: PDF, imagem, Word, Excel ou TXT.',
        ]);

        $tenantId = Auth::guard('usuarios')->user()?->tenant_id;

        $dados = [
            'tenant_id'   => $tenantId,
            'cliente_id'  => $this->clienteId,
            'contrato_id' => $this->contratoId ?: null,
            'processo_id' => $this->processoId ?: null,
            'tipo'        => $this->tipo,
            'descricao'   => $this->descricao,
            'valor'       => (float) str_replace(['.', ','], ['', '.'], $this->valor),
            'vencimento'  => $this->vencimento,
            'observacoes' => $this->observacoes ?: null,
        ];

        if ($this->lancamentoId) {
            DB::table('financeiro_lancamentos')
                ->where('id', $this->lancamentoId)
                ->where('tenant_id', $tenantId)
                ->update(array_merge($dados, ['updated_at' => now()]));
            $lancId = $this->lancamentoId;
            $msg = 'Lançamento atualizado.';
        } else {
            $lancId = DB::table('financeiro_lancamentos')
                ->insertGetId(array_merge($dados, ['status' => 'previsto', 'created_at' => now(), 'updated_at' => now()]));
            $msg = 'Lançamento criado!';
        }

        // Salvar anexo se enviado
        if ($this->anexo) {
            $caminho   = $this->anexo->store('financeiro/anexos', 'public');
            $original  = $this->anexo->getClientOriginalName();
            $mime      = $this->anexo->getMimeType();
            $tamanho   = $this->anexo->getSize();
            $uploadedBy = Auth::guard('usuarios')->user()?->nome ?? 'Sistema';

            DB::table('financeiro_lancamento_anexos')->insert([
                'lancamento_id'    => $lancId,
                'tenant_id'        => $tenantId,
                'arquivo'          => $caminho,
                'arquivo_original' => $original,
                'mime_type'        => $mime,
                'tamanho'          => $tamanho,
                'uploaded_by'      => $uploadedBy,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $this->fecharModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function excluirAnexo(int $anexoId): void
    {
        $tenantId = Auth::guard('usuarios')->user()?->tenant_id;

        $anexo = DB::table('financeiro_lancamento_anexos')
            ->where('id', $anexoId)
            ->where('tenant_id', $tenantId)
            ->first();

        if (! $anexo) return;

        Storage::disk('public')->delete($anexo->arquivo);

        DB::table('financeiro_lancamento_anexos')
            ->where('id', $anexoId)
            ->where('tenant_id', $tenantId)
            ->delete();

        if ($this->lancamentoId) {
            $this->carregarAnexos($this->lancamentoId);
        }

        $this->dispatch('toast', message: 'Anexo removido.', type: 'success');
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $lancamentos = FinanceiroLancamento::with(['cliente', 'contrato', 'processo'])
            ->when($this->busca, fn($q) => $q->where(fn($s) => $s
                ->where('descricao', 'ilike', "%{$this->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"))
            ))
            ->when($this->filtroStatus,   fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroCliente,  fn($q) => $q->where('cliente_id', $this->filtroCliente))
            ->when($this->filtroProcesso, fn($q) => $q->where('processo_id', $this->filtroProcesso))
            ->when($this->filtroMes,      fn($q) => $q->whereRaw("TO_CHAR(vencimento, 'YYYY-MM') = ?", [$this->filtroMes]))
            ->orderBy('vencimento')
            ->get();

        $nome = 'financeiro_' . ($this->filtroMes ?: now()->format('Y-m')) . '.csv';

        return response()->streamDownload(function () use ($lancamentos) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['Vencimento','Cliente','Descrição','Tipo','Status','Valor','Valor Pago','Data Pagamento','Forma Pgto','Contrato'], ';');
            foreach ($lancamentos as $l) {
                fputcsv($out, [
                    $l->vencimento->format('d/m/Y'),
                    $l->cliente?->nome ?? '',
                    $l->descricao,
                    ucfirst($l->tipo),
                    ucfirst($l->status),
                    number_format($l->valor, 2, ',', '.'),
                    $l->valor_pago ? number_format($l->valor_pago, 2, ',', '.') : '',
                    $l->data_pagamento?->format('d/m/Y') ?? '',
                    $l->forma_pagamento ?? '',
                    $l->contrato?->descricao ?? '',
                ], ';');
            }
            fclose($out);
        }, $nome, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function cancelar(int $id): void
    {
        DB::table('financeiro_lancamentos')
            ->where('id', $id)
            ->where('tenant_id', Auth::guard('usuarios')->user()?->tenant_id)
            ->update(['status' => 'cancelado', 'updated_at' => now()]);
        $this->dispatch('toast', message: 'Lançamento cancelado.', type: 'success');
    }

    public function excluir(int $id): void
    {
        $tenantId = Auth::guard('usuarios')->user()?->tenant_id;

        // Excluir arquivos físicos dos anexos
        $anexos = DB::table('financeiro_lancamento_anexos')
            ->where('lancamento_id', $id)
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($anexos as $a) {
            Storage::disk('public')->delete($a->arquivo);
        }

        DB::table('financeiro_lancamentos')
            ->where('id', $id)
            ->where('tenant_id', $tenantId)
            ->delete();

        $this->dispatch('toast', message: 'Lançamento excluído.', type: 'success');
    }

    // ── Modal: registrar recebimento ──────────────────────────
    public function abrirPagamento(int $id): void
    {
        $l = FinanceiroLancamento::findOrFail($id);
        $this->pagamentoLancId = $id;
        $this->dataPagamento   = now()->format('Y-m-d');
        $this->valorPago       = number_format($l->valor, 2, ',', '.');
        $this->formaPagamento  = 'pix';
        $this->pagamentoTipo   = $l->tipo;
        $this->modalPagamento  = true;
    }

    public function fecharPagamento(): void
    {
        $this->modalPagamento  = false;
        $this->pagamentoLancId = null;
        $this->resetErrorBag();
    }

    public function registrarPagamento(): void
    {
        $this->validate([
            'dataPagamento' => 'required|date',
            'valorPago'     => 'required',
        ]);

        DB::table('financeiro_lancamentos')
            ->where('id', $this->pagamentoLancId)
            ->where('tenant_id', Auth::guard('usuarios')->user()?->tenant_id)
            ->update([
                'status'          => 'recebido',
                'data_pagamento'  => $this->dataPagamento,
                'valor_pago'      => (float) str_replace(['.', ','], ['', '.'], $this->valorPago),
                'forma_pagamento' => $this->formaPagamento,
                'updated_at'      => now(),
            ]);

        $this->fecharPagamento();
        $this->dispatch('toast', message: 'Pagamento registrado!', type: 'success');
    }

    // ── Render ────────────────────────────────────────────────
    public function render(): \Illuminate\View\View
    {
        $lancamentos = FinanceiroLancamento::with(['cliente', 'contrato', 'processo', 'servico'])
            ->when($this->busca, fn($q) => $q->where(fn($s) => $s
                ->where('descricao', 'ilike', "%{$this->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"))
            ))
            ->when($this->filtroStatus,   fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroCliente,  fn($q) => $q->where('cliente_id', $this->filtroCliente))
            ->when($this->filtroProcesso, fn($q) => $q->where('processo_id', $this->filtroProcesso))
            ->when($this->filtroMes,      fn($q) => $q->whereRaw("TO_CHAR(vencimento, 'YYYY-MM') = ?", [$this->filtroMes]))
            ->when($this->ordenarPor === 'cliente',
                fn($q) => $q->leftJoin('pessoas', 'pessoas.id', '=', 'financeiro_lancamentos.cliente_id')
                             ->orderBy('pessoas.nome', $this->ordenarDir)
                             ->select('financeiro_lancamentos.*'),
                fn($q) => $q->orderBy($this->ordenarPor, $this->ordenarDir)
            )
            ->paginate(20);

        $base = FinanceiroLancamento::query()
            ->when($this->filtroMes,      fn($q) => $q->whereRaw("TO_CHAR(vencimento, 'YYYY-MM') = ?", [$this->filtroMes]))
            ->when($this->filtroCliente,  fn($q) => $q->where('cliente_id', $this->filtroCliente))
            ->when($this->filtroProcesso, fn($q) => $q->where('processo_id', $this->filtroProcesso))
            ->when($this->busca,          fn($q) => $q->where(fn($s) => $s
                ->where('descricao', 'ilike', "%{$this->busca}%")
                ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"))
            ));

        $totalPrevisto = (clone $base)->where('tipo', 'receita')->whereIn('status', ['previsto','atrasado'])->sum('valor');
        $totalRecebido = (clone $base)->where('tipo', 'receita')->where('status', 'recebido')->sum('valor_pago');
        $totalAtrasado = (clone $base)->where('tipo', 'receita')->where('status', 'atrasado')->sum('valor');
        $totalDespesa  = (clone $base)->where('tipo', 'despesa')->whereIn('status', ['previsto','atrasado','recebido'])->sum('valor');
        $totalRepasse  = (clone $base)->where('tipo', 'repasse')->whereIn('status', ['previsto','atrasado'])->sum('valor');

        $clientes     = $this->clientes;
        $fornecedores = $this->fornecedores;
        $ordenarPor   = $this->ordenarPor;
        $ordenarDir   = $this->ordenarDir;

        $tenantId  = tenant_id();
        $processos = DB::table('processos')
            ->when($tenantId, fn($q) => $q->where('tenant_id', $tenantId))
            ->orderBy('numero')
            ->get(['id', 'numero']);

        return view('livewire.financeiro-central', compact(
            'lancamentos', 'totalPrevisto', 'totalRecebido', 'totalAtrasado', 'totalDespesa', 'totalRepasse',
            'clientes', 'fornecedores', 'ordenarPor', 'ordenarDir', 'processos'
        ));
    }
}
