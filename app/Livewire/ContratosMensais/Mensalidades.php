<?php

namespace App\Livewire\ContratosMensais;

use App\Models\ContratoMensal;
use App\Models\Mensalidade;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Mensalidades extends Component
{
    use WithPagination;

    public int $contratoMensalId;

    public string $filtroStatus = '';
    public string $filtroAno    = '';

    // Modal recebimento
    public bool   $modalRecebimento = false;
    public ?int   $mensalidadeId    = null;
    public string $data_recebimento = '';
    public string $valor_recebido   = '';
    public string $forma_pagamento  = 'pix';
    public string $obs_recebimento  = '';

    // Modal cancelar
    public bool $modalCancelar = false;
    public ?int $cancelandoId  = null;

    protected array $rulesRecebimento = [
        'data_recebimento' => 'required|date',
        'valor_recebido'   => 'required|numeric|min:0.01',
        'forma_pagamento'  => 'required',
    ];

    public ContratoMensal $contrato;

    public function mount(int $id): void
    {
        $tid            = auth('usuarios')->user()->tenant_id;
        $this->contrato = ContratoMensal::where('tenant_id', $tid)->with('cliente')->findOrFail($id);
        $this->contratoMensalId  = $id;
        $this->filtroAno         = (string) now()->year;
        $this->data_recebimento  = now()->format('Y-m-d');
    }

    public function abrirRecebimento(int $id): void
    {
        $this->resetValidation();
        $tid = auth('usuarios')->user()->tenant_id;
        $m   = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->findOrFail($id);

        $this->mensalidadeId    = $m->id;
        $this->valor_recebido   = number_format($m->valor, 2, '.', '');
        $this->data_recebimento = now()->format('Y-m-d');
        $this->forma_pagamento  = 'pix';
        $this->obs_recebimento  = '';
        $this->modalRecebimento = true;
    }

    public function fecharRecebimento(): void
    {
        $this->modalRecebimento = false;
        $this->mensalidadeId    = null;
    }

    public function registrarRecebimento(): void
    {
        $this->validate($this->rulesRecebimento);

        $tid = auth('usuarios')->user()->tenant_id;
        $m   = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->findOrFail($this->mensalidadeId);

        $m->marcarRecebido(
            (float) $this->valor_recebido,
            $this->forma_pagamento,
            Carbon::parse($this->data_recebimento)
        );

        if ($this->obs_recebimento) {
            $m->update(['observacoes' => $this->obs_recebimento]);
        }

        $this->fecharRecebimento();
        $this->dispatch('toast', tipo: 'success', msg: 'Recebimento registrado.');
    }

    public function confirmarCancelar(int $id): void
    {
        $this->cancelandoId  = $id;
        $this->modalCancelar = true;
    }

    public function cancelarMensalidade(): void
    {
        if (!$this->cancelandoId) {
            return;
        }
        $tid = auth('usuarios')->user()->tenant_id;
        Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->where('id', $this->cancelandoId)
            ->update(['status' => 'cancelado']);

        $this->cancelandoId  = null;
        $this->modalCancelar = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Mensalidade cancelada.');
    }

    public function fecharCancelar(): void
    {
        $this->cancelandoId  = null;
        $this->modalCancelar = false;
    }

    public function render()
    {
        $tid = auth('usuarios')->user()->tenant_id;

        $mensalidades = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroAno, fn($q) => $q->where('competencia', 'like', $this->filtroAno . '-%'))
            ->orderBy('competencia', 'desc')
            ->paginate(12);

        $totalPendente = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->whereIn('status', ['pendente', 'vencido', 'parcial'])
            ->sum('valor');

        $totalRecebido = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->whereIn('status', ['recebido', 'parcial'])
            ->sum('valor_recebido');

        $vencidas = Mensalidade::where('tenant_id', $tid)
            ->where('contrato_mensal_id', $this->contratoMensalId)
            ->where('status', 'vencido')
            ->count();

        $proximoVcto = $this->contrato->proximoVencimento();

        $anos = range(now()->year - 1, now()->year + 1);

        return view('livewire.contratos-mensais.mensalidades', compact(
            'mensalidades', 'totalPendente', 'totalRecebido', 'vencidas', 'proximoVcto', 'anos'
        ))->extends('layouts.app')->section('content');
    }
}
