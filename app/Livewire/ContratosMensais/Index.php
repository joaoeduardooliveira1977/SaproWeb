<?php

namespace App\Livewire\ContratosMensais;

use App\Models\ContratoMensal;
use App\Models\Mensalidade;
use App\Models\Pessoa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busca        = '';
    public string $filtroStatus = '';

    protected $queryString = [
        'busca'        => ['except' => ''],
        'filtroStatus' => ['except' => ''],
    ];

    // Modal contrato
    public bool $modal       = false;
    public bool $modalExcluir = false;

    // Form
    public ?int  $contratoId     = null;
    public int   $cliente_id     = 0;
    public string $descricao     = '';
    public string $valor         = '';
    public int    $dia_vencimento = 5;
    public string $periodicidade  = 'mensal';
    public string $data_inicio    = '';
    public string $data_fim       = '';
    public string $status         = 'ativo';
    public string $observacoes    = '';

    // Autocomplete cliente
    public string $clienteBusca     = '';
    public array  $clienteSugestoes = [];

    // Auxiliar
    public ?int $excluindoId = null;

    protected array $rules = [
        'cliente_id'     => 'required|integer|min:1',
        'descricao'      => 'required|min:3',
        'valor'          => 'required|numeric|min:0.01',
        'dia_vencimento' => 'required|integer|min:1|max:28',
        'periodicidade'  => 'required|in:mensal,bimestral,trimestral,semestral,anual',
        'data_inicio'    => 'required|date',
        'data_fim'       => 'nullable|date|after_or_equal:data_inicio',
        'status'         => 'required|in:ativo,suspenso,encerrado',
    ];

    public function mount(): void
    {
        $this->data_inicio = now()->format('Y-m-d');
    }

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroStatus(): void
    {
        $this->resetPage();
    }

    public function updatedClienteBusca(): void
    {
        if (strlen($this->clienteBusca) < 2) {
            $this->clienteSugestoes = [];
            return;
        }
        $tid  = auth('usuarios')->user()->tenant_id;
        $termo = '%' . $this->clienteBusca . '%';
        $this->clienteSugestoes = DB::table('pessoas')
            ->where('tenant_id', $tid)
            ->where('nome', 'ilike', $termo)
            ->orderBy('nome')
            ->limit(8)
            ->get(['id', 'nome'])
            ->toArray();
    }

    public function selecionarCliente(int $id, string $nome): void
    {
        $this->cliente_id     = $id;
        $this->clienteBusca   = $nome;
        $this->clienteSugestoes = [];
    }

    public function limparCliente(): void
    {
        $this->cliente_id     = 0;
        $this->clienteBusca   = '';
        $this->clienteSugestoes = [];
    }

    public function abrirModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->resetForm();

        if ($id) {
            $tid = auth('usuarios')->user()->tenant_id;
            $c   = ContratoMensal::where('tenant_id', $tid)->findOrFail($id);
            $this->contratoId     = $c->id;
            $this->cliente_id     = $c->cliente_id;
            $this->clienteBusca   = $c->cliente?->nome ?? '';
            $this->descricao      = $c->descricao;
            $this->valor          = number_format($c->valor, 2, '.', '');
            $this->dia_vencimento = $c->dia_vencimento;
            $this->periodicidade  = $c->periodicidade;
            $this->data_inicio    = $c->data_inicio->format('Y-m-d');
            $this->data_fim       = $c->data_fim ? $c->data_fim->format('Y-m-d') : '';
            $this->status         = $c->status;
            $this->observacoes    = $c->observacoes ?? '';
        }

        $this->modal = true;
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetForm();
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'cliente_id'     => $this->cliente_id,
            'descricao'      => trim($this->descricao),
            'valor'          => $this->valor,
            'dia_vencimento' => $this->dia_vencimento,
            'periodicidade'  => $this->periodicidade,
            'data_inicio'    => $this->data_inicio,
            'data_fim'       => $this->data_fim ?: null,
            'status'         => $this->status,
            'observacoes'    => trim($this->observacoes) ?: null,
        ];

        if ($this->contratoId) {
            $tid = auth('usuarios')->user()->tenant_id;
            ContratoMensal::where('tenant_id', $tid)
                ->where('id', $this->contratoId)
                ->firstOrFail()
                ->update($dados);
            $this->dispatch('toast', tipo: 'success', msg: 'Contrato atualizado.');
        } else {
            ContratoMensal::create($dados);
            $this->dispatch('toast', tipo: 'success', msg: 'Contrato criado e mensalidades geradas.');
        }

        $this->fecharModal();
    }

    public function confirmarExcluir(int $id): void
    {
        $this->excluindoId  = $id;
        $this->modalExcluir = true;
    }

    public function excluir(): void
    {
        if (!$this->excluindoId) {
            return;
        }
        $tid = auth('usuarios')->user()->tenant_id;
        $c   = ContratoMensal::where('tenant_id', $tid)->findOrFail($this->excluindoId);

        // Cancelar mensalidades pendentes antes de excluir
        $c->mensalidades()->whereIn('status', ['pendente', 'vencido'])->update(['status' => 'cancelado']);
        $c->delete();

        $this->excluindoId  = null;
        $this->modalExcluir = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Contrato excluído.');
    }

    public function cancelarExcluir(): void
    {
        $this->excluindoId  = null;
        $this->modalExcluir = false;
    }

    private function resetForm(): void
    {
        $this->contratoId     = null;
        $this->cliente_id     = 0;
        $this->clienteBusca   = '';
        $this->clienteSugestoes = [];
        $this->descricao      = '';
        $this->valor          = '';
        $this->dia_vencimento = 5;
        $this->periodicidade  = 'mensal';
        $this->data_inicio    = now()->format('Y-m-d');
        $this->data_fim       = '';
        $this->status         = 'ativo';
        $this->observacoes    = '';
    }

    public function render()
    {
        $tid = auth('usuarios')->user()->tenant_id;

        $contratos = ContratoMensal::where('tenant_id', $tid)
            ->with('cliente')
            ->when($this->busca, fn($q) =>
                $q->where(function ($q2) {
                    $q2->where('descricao', 'ilike', '%' . $this->busca . '%')
                       ->orWhereHas('cliente', fn($q3) =>
                            $q3->where('nome', 'ilike', '%' . $this->busca . '%')
                       );
                })
            )
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // KPIs
        $totalAtivos   = ContratoMensal::where('tenant_id', $tid)->where('status', 'ativo')->count();
        $receitaMensal = ContratoMensal::where('tenant_id', $tid)->where('status', 'ativo')->sum('valor');

        $emAberto = Mensalidade::where('tenant_id', $tid)
            ->whereIn('status', ['pendente', 'vencido', 'parcial'])
            ->sum('valor');

        $recebidoMes = Mensalidade::where('tenant_id', $tid)
            ->whereIn('status', ['recebido', 'parcial'])
            ->whereMonth('data_recebimento', now()->month)
            ->whereYear('data_recebimento', now()->year)
            ->sum('valor_recebido');

        return view('livewire.contratos-mensais.index', compact(
            'contratos', 'totalAtivos', 'receitaMensal', 'emAberto', 'recebidoMes'
        ))->extends('layouts.app')->section('content');
    }
}
