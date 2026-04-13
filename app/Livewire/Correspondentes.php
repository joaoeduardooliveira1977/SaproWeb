<?php

namespace App\Livewire;

use App\Models\Correspondente;
use App\Models\Pessoa;
use App\Models\Processo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Correspondentes extends Component
{
    use WithPagination;

    // ── Filtros ──────────────────────────────────────────────
    public string $filtroBusca     = '';
    public string $filtroStatus    = '';
    public string $filtroTipo      = '';
    public string $filtroAdvogado  = '';

    protected $queryString = [
        'filtroBusca'    => ['except' => ''],
        'filtroStatus'   => ['except' => ''],
        'filtroTipo'     => ['except' => ''],
        'filtroAdvogado' => ['except' => ''],
    ];

    // ── Modal principal ───────────────────────────────────────
    public bool  $modalAberto     = false;
    public ?int  $correspondente_id = null;

    // ── Formulário ───────────────────────────────────────────
    public string $processo_id      = '';
    public string $advogado_id      = '';
    public string $comarca          = '';
    public string $estado           = '';
    public string $tipo             = 'diligencia';
    public string $descricao        = '';
    public string $data_solicitacao = '';
    public string $data_prazo       = '';
    public string $valor_combinado  = '';
    public string $observacoes      = '';
    public string $status           = 'pendente';

    // ── Modal avançar status ──────────────────────────────────
    public bool  $modalAvancar      = false;
    public ?int  $avancarId         = null;
    public string $avancarStatus    = '';
    public string $avancarData      = '';
    public string $avancarValorPago = '';
    public string $avancarObs       = '';

    protected function rules(): array
    {
        return [
            'advogado_id'      => 'required|exists:pessoas,id',
            'comarca'          => 'required|min:2|max:150',
            'estado'           => 'nullable|size:2',
            'tipo'             => 'required|in:audiencia,protocolo,citacao,pericia,diligencia,outro',
            'descricao'        => 'required|min:3',
            'data_solicitacao' => 'required|date',
            'data_prazo'       => 'nullable|date',
            'valor_combinado'  => 'nullable|numeric|min:0',
            'processo_id'      => 'nullable|exists:processos,id',
        ];
    }

    public function updatedFiltroBusca(): void    { $this->resetPage(); }
    public function updatedFiltroStatus(): void   { $this->resetPage(); }
    public function updatedFiltroTipo(): void     { $this->resetPage(); }
    public function updatedFiltroAdvogado(): void { $this->resetPage(); }

    // ── CRUD ─────────────────────────────────────────────────

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->correspondente_id = $id;
        $this->modalAberto = true;

        if ($id) {
            $c = Correspondente::findOrFail($id);
            $this->processo_id      = (string) ($c->processo_id ?? '');
            $this->advogado_id      = (string) $c->advogado_id;
            $this->comarca          = $c->comarca;
            $this->estado           = $c->estado ?? '';
            $this->tipo             = $c->tipo;
            $this->descricao        = $c->descricao;
            $this->data_solicitacao = $c->data_solicitacao->format('Y-m-d');
            $this->data_prazo       = $c->data_prazo?->format('Y-m-d') ?? '';
            $this->valor_combinado  = $c->valor_combinado ? number_format($c->valor_combinado, 2, '.', '') : '';
            $this->observacoes      = $c->observacoes ?? '';
            $this->status           = $c->status;
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'processo_id'     => $this->processo_id ?: null,
            'advogado_id'     => (int) $this->advogado_id,
            'comarca'         => $this->comarca,
            'estado'          => strtoupper($this->estado) ?: null,
            'tipo'            => $this->tipo,
            'descricao'       => $this->descricao,
            'data_solicitacao'=> $this->data_solicitacao,
            'data_prazo'      => $this->data_prazo ?: null,
            'valor_combinado' => $this->valor_combinado ? (float) $this->valor_combinado : null,
            'observacoes'     => $this->observacoes ?: null,
            'status'          => $this->status,
        ];

        if ($this->correspondente_id) {
            Correspondente::findOrFail($this->correspondente_id)->update($dados);
            $this->dispatch('toast', message: 'Correspondência atualizada.', type: 'success');
        } else {
            $dados['solicitado_por'] = Auth::guard('usuarios')->id();
            Correspondente::create($dados);
            $this->dispatch('toast', message: 'Correspondência cadastrada.', type: 'success');
        }

        $this->fecharModal();
    }

    public function excluir(int $id): void
    {
        Correspondente::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Correspondência excluída.', type: 'success');
    }

    // ── Avançar status ────────────────────────────────────────

    public function abrirAvancar(int $id): void
    {
        $c = Correspondente::findOrFail($id);
        $proximo = $c->proximoStatus();

        if (!$proximo) return;

        $this->avancarId         = $id;
        $this->avancarStatus     = $proximo;
        $this->avancarData       = now()->format('Y-m-d');
        $this->avancarValorPago  = $c->valor_combinado ? number_format($c->valor_combinado, 2, '.', '') : '';
        $this->avancarObs        = '';
        $this->modalAvancar      = true;
    }

    public function confirmarAvancar(): void
    {
        $c = Correspondente::findOrFail($this->avancarId);

        $update = ['status' => $this->avancarStatus];

        if ($this->avancarStatus === 'realizado') {
            $update['data_realizado'] = $this->avancarData ?: today();
            if ($this->avancarObs) {
                $update['observacoes'] = ($c->observacoes ? $c->observacoes . "\n" : '') . $this->avancarObs;
            }
        }

        if ($this->avancarStatus === 'pago') {
            $update['data_pagamento'] = $this->avancarData ?: today();
            $update['valor_pago']     = $this->avancarValorPago ? (float) $this->avancarValorPago : $c->valor_combinado;
        }

        $c->update($update);

        $label = Correspondente::statusLabel()[$this->avancarStatus] ?? $this->avancarStatus;
        $this->dispatch('toast', message: "Correspondência marcada como {$label}.", type: 'success');
        $this->modalAvancar = false;
    }

    // ── Helpers ───────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->correspondente_id = null;
        $this->processo_id       = '';
        $this->advogado_id       = '';
        $this->comarca           = '';
        $this->estado            = '';
        $this->tipo              = 'diligencia';
        $this->descricao         = '';
        $this->data_solicitacao  = now()->format('Y-m-d');
        $this->data_prazo        = '';
        $this->valor_combinado   = '';
        $this->observacoes       = '';
        $this->status            = 'pendente';
        $this->resetErrorBag();
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = \App\Models\Correspondente::with(['processo.cliente', 'advogado'])
            ->when($this->filtroStatus,   fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroAdvogado, fn($q) => $q->where('advogado_id', $this->filtroAdvogado))
            ->when($this->filtroBusca, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('comarca', 'ilike', "%{$this->filtroBusca}%")
                      ->orWhere('descricao', 'ilike', "%{$this->filtroBusca}%")
                      ->orWhereHas('advogado', fn($a) =>
                          $a->where('nome', 'ilike', "%{$this->filtroBusca}%")
                      )
                      ->orWhereHas('processo', fn($p) =>
                          $p->where('numero', 'ilike', "%{$this->filtroBusca}%")
                      )
                )
            )
            ->orderBy('data_prazo')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Processo','Cliente','Advogado','Tipo','Comarca','Estado','Status','Descrição','Data Solicitação','Data Prazo','Valor Combinado','Valor Pago'], ';');
            foreach ($rows as $c) {
                fputcsv($out, [
                    $c->processo?->numero ?? '',
                    $c->processo?->cliente?->nome ?? '',
                    $c->advogado?->nome ?? '',
                    $c->tipo,
                    $c->comarca ?? '',
                    $c->estado ?? '',
                    $c->status,
                    $c->descricao ?? '',
                    $c->data_solicitacao?->format('d/m/Y') ?? '',
                    $c->data_prazo?->format('d/m/Y') ?? '',
                    $c->valor_combinado ? number_format($c->valor_combinado, 2, ',', '.') : '',
                    $c->valor_pago ? number_format($c->valor_pago, 2, ',', '.') : '',
                ], ';');
            }
            fclose($out);
        }, 'correspondentes-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $correspondencias = Correspondente::with(['processo.cliente', 'advogado'])
            ->when($this->filtroStatus,   fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroAdvogado, fn($q) => $q->where('advogado_id', $this->filtroAdvogado))
            ->when($this->filtroBusca, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('comarca', 'ilike', "%{$this->filtroBusca}%")
                      ->orWhere('descricao', 'ilike', "%{$this->filtroBusca}%")
                      ->orWhereHas('advogado', fn($a) =>
                          $a->where('nome', 'ilike', "%{$this->filtroBusca}%")
                      )
                      ->orWhereHas('processo', fn($p) =>
                          $p->where('numero', 'ilike', "%{$this->filtroBusca}%")
                      )
                )
            )
            ->orderByRaw("CASE status
                WHEN 'pendente'  THEN 1
                WHEN 'aceito'    THEN 2
                WHEN 'realizado' THEN 3
                WHEN 'pago'      THEN 5
                WHEN 'cancelado' THEN 6
                ELSE 4 END")
            ->orderBy('data_prazo')
            ->paginate(20);

        $processos  = Processo::where('status', 'Ativo')->orderBy('numero')->get();
        $advogados  = Pessoa::doTipo('Advogado')->ativos()->orderBy('nome')->get();

        // KPIs
        $kpis = [
            'pendentes'       => Correspondente::where('status', 'pendente')->count(),
            'aceitas'         => Correspondente::where('status', 'aceito')->count(),
            'realizadas_mes'  => Correspondente::where('status', 'realizado')
                ->whereMonth('data_realizado', now()->month)->count(),
            'a_pagar'         => Correspondente::where('status', 'realizado')
                ->whereNotNull('valor_combinado')->sum('valor_combinado'),
            'pagas_mes'       => Correspondente::where('status', 'pago')
                ->whereMonth('data_pagamento', now()->month)->sum('valor_pago'),
        ];

        return view('livewire.correspondentes', compact(
            'correspondencias', 'processos', 'advogados', 'kpis'
        ));
    }
}
