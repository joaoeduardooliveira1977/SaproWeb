<?php

namespace App\Livewire\Financeiro;

use App\Models\ContratoMensal;
use App\Models\FinanceiroLancamento;
use App\Models\Mensalidade;
use App\Models\Pessoa;
use App\Models\ReceitaProcesso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cliente extends Component
{
    use WithPagination;

    public int  $clienteId;
    public ?int $tenantId = null;

    // Filtros grid
    public string $filtroStatus    = '';
    public string $filtroOrigem    = '';
    public string $filtroAno       = '';
    public string $filtroMes       = '';

    // ─── Modal: Contrato Mensal ───────────────────────────────────
    public bool   $modalContrato     = false;
    public ?int   $contratoId        = null;
    public string $cm_descricao      = '';
    public string $cm_responsavel    = '';
    public string $cm_valor          = '';
    public int    $cm_dia_vencimento = 5;
    public string $cm_periodicidade  = 'mensal';
    public string $cm_data_inicio    = '';
    public string $cm_data_fim       = '';
    public string $cm_status         = 'ativo';
    public string $cm_forma_cobranca  = 'pix';
    public string $cm_email           = '';
    public string $cm_whatsapp        = '';
    public bool   $cm_envio_automatico = false;
    public string $cm_observacoes     = '';

    // ─── Modal: Receita por Processos ─────────────────────────────
    public bool   $modalProcesso   = false;
    public ?int   $receitaId       = null;
    public string $rp_valor_unit   = '';
    public int    $rp_dia_vcto     = 5;
    public string $rp_data_inicio  = '';
    public string $rp_observacoes  = '';
    public ?array $rp_simulacao    = null;

    // ─── Modal: Receita Pontual ───────────────────────────────────
    public bool   $modalPontual      = false;
    public string $pt_descricao      = '';
    public string $pt_valor          = '';
    public string $pt_vencimento     = '';
    public string $pt_forma          = 'pix';
    public string $pt_email          = '';
    public string $pt_whatsapp       = '';
    public string $pt_observacoes    = '';
    public bool   $pt_ja_recebido    = false;
    public string $pt_data_receb     = '';

    // ─── Modal: PIX ──────────────────────────────────────────────
    public bool   $modalPix          = false;
    public int    $pixLancamentoId   = 0;

    // ─── Modal: Baixa Financeira ──────────────────────────────────
    public bool   $modalBaixa        = false;
    public ?int   $baixaLancId       = null;
    public ?array $baixaLancDados    = null;
    public string $bx_valor          = '';
    public string $bx_data           = '';
    public string $bx_forma          = 'pix';
    public string $bx_juros          = '0';
    public string $bx_multa          = '0';
    public string $bx_desconto       = '0';
    public string $bx_observacoes    = '';
    public string $bx_tipo_rec       = 'total'; // total | parcial

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->tenantId  = tenant_id();
        $this->filtroAno     = (string) now()->year;
        $this->cm_data_inicio = now()->format('Y-m-d');
        $this->rp_data_inicio = now()->format('Y-m-d');
        $this->pt_vencimento  = now()->format('Y-m-d');
        $this->pt_data_receb  = now()->format('Y-m-d');
        $this->bx_data        = now()->format('Y-m-d');
    }

    // ── Listeners ─────────────────────────────────────────────────
    #[\Livewire\Attributes\On('financeiro-atualizado')]
    public function recarregar(): void
    {
        $this->resetPage();
    }

    // ─── Filtros ─────────────────────────────────────────────────
    public function updatedFiltroStatus(): void  { $this->resetPage(); }
    public function updatedFiltroOrigem(): void   { $this->resetPage(); }
    public function updatedFiltroAno(): void      { $this->resetPage(); }
    public function updatedFiltroMes(): void      { $this->resetPage(); }

    // ═══════════════════════════════════════════════════════════════
    //  CONTRATO MENSAL
    // ═══════════════════════════════════════════════════════════════
    public function abrirModalContrato(?int $id = null): void
    {
        $this->resetValidation();
        $this->contratoId = $id;

        if ($id) {
            $c = ContratoMensal::where('tenant_id', $this->tenantId)->findOrFail($id);
            $this->cm_descricao      = $c->descricao;
            $this->cm_responsavel    = $c->responsavel ?? '';
            $this->cm_valor          = number_format($c->valor, 2, '.', '');
            $this->cm_dia_vencimento = $c->dia_vencimento;
            $this->cm_periodicidade  = $c->periodicidade;
            $this->cm_data_inicio    = $c->data_inicio->format('Y-m-d');
            $this->cm_data_fim       = $c->data_fim ? $c->data_fim->format('Y-m-d') : '';
            $this->cm_status         = $c->status;
            $this->cm_forma_cobranca = $c->forma_cobranca ?? 'pix';
            $this->cm_email          = $c->email_financeiro ?? '';
            $this->cm_whatsapp        = $c->whatsapp_financeiro ?? '';
            $this->cm_envio_automatico = (bool) ($c->envio_automatico ?? false);
            $this->cm_observacoes     = $c->observacoes ?? '';
        } else {
            $this->cm_descricao        = '';
            $this->cm_responsavel      = '';
            $this->cm_valor            = '';
            $this->cm_dia_vencimento   = 5;
            $this->cm_periodicidade    = 'mensal';
            $this->cm_data_inicio      = now()->format('Y-m-d');
            $this->cm_data_fim         = '';
            $this->cm_status           = 'ativo';
            $this->cm_forma_cobranca   = 'pix';
            $this->cm_email            = '';
            $this->cm_whatsapp         = '';
            $this->cm_envio_automatico = false;
            $this->cm_observacoes      = '';
        }

        $this->modalContrato = true;
    }

    public function salvarContrato(): void
    {
        $this->validate([
            'cm_descricao'      => 'required|min:3',
            'cm_valor'          => 'required|numeric|min:0.01',
            'cm_dia_vencimento' => 'required|integer|between:1,28',
            'cm_data_inicio'    => 'required|date',
            'cm_data_fim'       => 'nullable|date|after:cm_data_inicio',
            'cm_status'         => 'required|in:ativo,suspenso,encerrado',
        ]);

        $dados = [
            'cliente_id'         => $this->clienteId,
            'descricao'          => trim($this->cm_descricao),
            'responsavel'        => $this->cm_responsavel ?: null,
            'valor'              => $this->cm_valor,
            'dia_vencimento'     => $this->cm_dia_vencimento,
            'periodicidade'      => $this->cm_periodicidade,
            'data_inicio'        => $this->cm_data_inicio,
            'data_fim'           => $this->cm_data_fim ?: null,
            'status'             => $this->cm_status,
            'forma_cobranca'     => $this->cm_forma_cobranca,
            'email_financeiro'    => $this->cm_email ?: null,
            'whatsapp_financeiro' => $this->cm_whatsapp ?: null,
            'envio_automatico'    => $this->cm_envio_automatico,
            'observacoes'         => trim($this->cm_observacoes) ?: null,
        ];

        if ($this->contratoId) {
            ContratoMensal::where('tenant_id', $this->tenantId)
                ->findOrFail($this->contratoId)
                ->update($dados);
            $this->dispatch('toast', tipo: 'success', msg: 'Contrato atualizado.');
        } else {
            ContratoMensal::create($dados);
            $this->dispatch('toast', tipo: 'success', msg: 'Contrato mensal criado e mensalidades geradas.');
        }

        $this->modalContrato = false;
        $this->dispatch('financeiro-atualizado');
    }

    public function suspenderContrato(int $id): void
    {
        ContratoMensal::where('tenant_id', $this->tenantId)
            ->findOrFail($id)
            ->update(['status' => 'suspenso']);
        $this->dispatch('toast', tipo: 'success', msg: 'Contrato suspenso.');
        $this->dispatch('financeiro-atualizado');
    }

    public function encerrarContrato(int $id): void
    {
        ContratoMensal::where('tenant_id', $this->tenantId)
            ->findOrFail($id)
            ->update(['status' => 'encerrado']);
        $this->dispatch('toast', tipo: 'success', msg: 'Contrato encerrado.');
        $this->dispatch('financeiro-atualizado');
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECEITA POR PROCESSOS
    // ═══════════════════════════════════════════════════════════════
    public function abrirModalProcesso(?int $id = null): void
    {
        $this->resetValidation();
        $this->rp_simulacao = null;
        $this->receitaId    = $id;

        if ($id) {
            $r = ReceitaProcesso::where('tenant_id', $this->tenantId)->findOrFail($id);
            $this->rp_valor_unit  = number_format($r->valor_unitario, 2, '.', '');
            $this->rp_dia_vcto    = $r->dia_vencimento;
            $this->rp_data_inicio = $r->data_inicio->format('Y-m-d');
            $this->rp_observacoes = $r->observacoes ?? '';
        } else {
            $this->rp_valor_unit  = '';
            $this->rp_dia_vcto    = 5;
            $this->rp_data_inicio = now()->format('Y-m-d');
            $this->rp_observacoes = '';
        }

        $this->modalProcesso = true;
    }

    public function simularReceita(): void
    {
        $this->validate(['rp_valor_unit' => 'required|numeric|min:0.01']);

        $qtd = \App\Models\Processo::where('cliente_id', $this->clienteId)
            ->where('tenant_id', $this->tenantId)
            ->where('status', 'Ativo')
            ->count();

        $this->rp_simulacao = [
            'processos' => $qtd,
            'unitario'  => (float) $this->rp_valor_unit,
            'total'     => (float) $this->rp_valor_unit * $qtd,
        ];
    }

    public function salvarReceita(): void
    {
        $this->validate([
            'rp_valor_unit'  => 'required|numeric|min:0.01',
            'rp_dia_vcto'    => 'required|integer|between:1,28',
            'rp_data_inicio' => 'required|date',
        ]);

        $dados = [
            'tenant_id'      => $this->tenantId,
            'cliente_id'     => $this->clienteId,
            'valor_unitario' => $this->rp_valor_unit,
            'dia_vencimento' => $this->rp_dia_vcto,
            'data_inicio'    => $this->rp_data_inicio,
            'status'         => 'ativo',
            'observacoes'    => trim($this->rp_observacoes) ?: null,
        ];

        if ($this->receitaId) {
            ReceitaProcesso::where('tenant_id', $this->tenantId)
                ->findOrFail($this->receitaId)
                ->update($dados);
        } else {
            $receita = ReceitaProcesso::create($dados);
            $receita->gerarMensalidadeDoMes();
        }

        $this->modalProcesso = false;
        $this->rp_simulacao  = null;
        $this->dispatch('toast', tipo: 'success', msg: 'Receita por processos salva.');
        $this->dispatch('financeiro-atualizado');
    }

    // ═══════════════════════════════════════════════════════════════
    //  RECEITA PONTUAL
    // ═══════════════════════════════════════════════════════════════
    public function abrirModalPontual(): void
    {
        $this->resetValidation();
        $this->pt_descricao   = '';
        $this->pt_valor       = '';
        $this->pt_vencimento  = now()->format('Y-m-d');
        $this->pt_forma       = 'pix';
        $this->pt_email       = '';
        $this->pt_whatsapp    = '';
        $this->pt_observacoes = '';
        $this->pt_ja_recebido = false;
        $this->pt_data_receb  = now()->format('Y-m-d');
        $this->modalPontual   = true;
    }

    public function salvarPontual(): void
    {
        $this->validate([
            'pt_descricao'  => 'required|min:3',
            'pt_valor'      => 'required|numeric|min:0.01',
            'pt_vencimento' => 'required|date',
        ]);

        $dados = [
            'tenant_id'      => $this->tenantId,
            'cliente_id'     => $this->clienteId,
            'tipo'           => 'receita',
            'origem_tipo'    => 'pontual',
            'descricao'      => trim($this->pt_descricao),
            'valor'          => $this->pt_valor,
            'vencimento'     => $this->pt_vencimento,
            'forma_pagamento'=> $this->pt_forma,
            'observacoes'    => trim($this->pt_observacoes) ?: null,
        ];

        if ($this->pt_ja_recebido) {
            $dados['status']         = 'recebido';
            $dados['valor_pago']     = $this->pt_valor;
            $dados['valor_liquido']  = $this->pt_valor;
            $dados['data_pagamento'] = $this->pt_data_receb;
        } else {
            $dados['status'] = 'previsto';
        }

        FinanceiroLancamento::create($dados);

        $this->modalPontual = false;
        $this->dispatch('toast', tipo: 'success', msg: 'Receita pontual registrada.');
        $this->dispatch('financeiro-atualizado');
    }

    // ═══════════════════════════════════════════════════════════════
    //  PIX
    // ═══════════════════════════════════════════════════════════════
    public function abrirPix(int $lancamentoId): void
    {
        $this->pixLancamentoId = $lancamentoId;
        $this->modalPix        = true;
    }

    public function fecharPix(): void
    {
        $this->modalPix        = false;
        $this->pixLancamentoId = 0;
    }

    // ═══════════════════════════════════════════════════════════════
    //  BAIXA FINANCEIRA
    // ═══════════════════════════════════════════════════════════════
    public function abrirBaixa(int $lancId): void
    {
        $this->resetValidation();
        $lanc = FinanceiroLancamento::where('tenant_id', $this->tenantId)
            ->where('cliente_id', $this->clienteId)
            ->findOrFail($lancId);

        $this->baixaLancId    = $lancId;
        $this->baixaLancDados = [
            'descricao'      => $lanc->descricao,
            'valor'          => $lanc->valor,
            'vencimento'     => $lanc->vencimento->format('d/m/Y'),
            'origem_tipo'    => $lanc->origem_tipo,
            'mensalidade_id' => $lanc->mensalidade_id,
        ];
        $this->bx_valor       = number_format($lanc->valor, 2, '.', '');
        $this->bx_data        = now()->format('Y-m-d');
        $this->bx_forma       = 'pix';
        $this->bx_juros       = '0';
        $this->bx_multa       = '0';
        $this->bx_desconto    = '0';
        $this->bx_observacoes = '';
        $this->bx_tipo_rec    = 'total';
        $this->modalBaixa     = true;
    }

    public function updatedBxTipoRec(): void
    {
        if ($this->bx_tipo_rec === 'total' && $this->baixaLancDados) {
            $this->bx_valor = number_format($this->baixaLancDados['valor'], 2, '.', '');
        }
    }

    public function registrarBaixa(): void
    {
        $this->validate([
            'bx_valor' => 'required|numeric|min:0.01',
            'bx_data'  => 'required|date',
            'bx_forma' => 'required',
        ]);

        $lanc = FinanceiroLancamento::where('tenant_id', $this->tenantId)
            ->where('cliente_id', $this->clienteId)
            ->findOrFail($this->baixaLancId);

        $valor    = (float) $this->bx_valor;
        $juros    = (float) $this->bx_juros;
        $multa    = (float) $this->bx_multa;
        $desconto = (float) $this->bx_desconto;
        $liquido  = $valor + $juros + $multa - $desconto;
        $status   = $valor >= (float) $lanc->valor ? 'recebido' : 'parcial';

        // Se lançamento é de mensalidade, atualiza via Mensalidade::marcarRecebido
        if ($lanc->origem_tipo === 'mensal' && $lanc->mensalidade_id) {
            $mens = Mensalidade::withoutGlobalScopes()
                ->where('id', $lanc->mensalidade_id)
                ->first();

            if ($mens) {
                $mens->marcarRecebido($valor, $this->bx_forma, Carbon::parse($this->bx_data), $juros, $multa, $desconto);
                if ($this->bx_observacoes) {
                    $mens->update(['observacoes' => $this->bx_observacoes]);
                }
                $this->fecharBaixa();
                $this->dispatch('toast', tipo: 'success', msg: 'Baixa registrada.');
                return;
            }
        }

        // Atualiza lançamento diretamente
        $lanc->update([
            'valor_pago'     => $valor,
            'data_pagamento' => $this->bx_data,
            'juros'          => $juros,
            'multa'          => $multa,
            'desconto'       => $desconto,
            'valor_liquido'  => $liquido,
            'status'         => $status,
            'forma_pagamento'=> $this->bx_forma,
            'observacoes'    => $this->bx_observacoes ?: $lanc->observacoes,
        ]);

        $this->fecharBaixa();
        $this->dispatch('toast', tipo: 'success', msg: 'Baixa registrada.');
    }

    public function fecharBaixa(): void
    {
        $this->modalBaixa     = false;
        $this->baixaLancId    = null;
        $this->baixaLancDados = null;
        $this->dispatch('financeiro-atualizado');
    }

    // ═══════════════════════════════════════════════════════════════
    //  RENDER
    // ═══════════════════════════════════════════════════════════════
    public function render()
    {
        $tid = $this->tenantId;

        if (!$tid) {
            return view('livewire.financeiro.cliente', [
                'kpiReceitaMensal'    => ['valor' => 0, 'label' => ''],
                'kpiEmAberto'         => 0,
                'kpiRecebidoAno'      => 0,
                'proximoLanc'         => null,
                'lancamentos'         => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                'contratos'           => collect(),
                'receitasProcesso'    => collect(),
                'contratoAtivo'       => null,
                'receitaProcessoAtiva'=> null,
                'anos'                => [],
                'meses'               => [],
            ])->extends('layouts.app')->section('content');
        }

        // ── KPI 1: Receita Mensal Atual ──────────────────────────
        $contratoAtivo = ContratoMensal::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->where('status', 'ativo')
            ->first();

        $receitaProcessoAtiva = ReceitaProcesso::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->where('status', 'ativo')
            ->first();

        $kpiReceitaMensal = $this->calcularKpiReceitaMensal($contratoAtivo, $receitaProcessoAtiva);

        // ── KPI 2: Total em Aberto ───────────────────────────────
        $kpiEmAberto = FinanceiroLancamento::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->where('tipo', 'receita')
            ->whereIn('status', ['previsto', 'atrasado', 'vencido'])
            ->whereNotNull('origem_tipo')
            ->sum('valor');

        // ── KPI 3: Recebido no Ano ───────────────────────────────
        $kpiRecebidoAno = FinanceiroLancamento::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->where('tipo', 'receita')
            ->whereIn('status', ['recebido', 'parcial'])
            ->whereYear('data_pagamento', now()->year)
            ->sum('valor_pago');

        // ── KPI 4: Próximo Vencimento ────────────────────────────
        $proximoLanc = FinanceiroLancamento::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->where('tipo', 'receita')
            ->whereIn('status', ['previsto', 'atrasado', 'vencido'])
            ->where('vencimento', '>=', today()->toDateString())
            ->orderBy('vencimento')
            ->first(['descricao', 'valor', 'vencimento']);

        // ── Grid de lançamentos ──────────────────────────────────
        $lancamentos = FinanceiroLancamento::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->whereNotNull('origem_tipo')
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroOrigem, fn($q) => $q->where('origem_tipo', $this->filtroOrigem))
            ->when($this->filtroAno, fn($q) => $q->whereYear('vencimento', $this->filtroAno))
            ->when($this->filtroMes, fn($q) => $q->whereMonth('vencimento', $this->filtroMes))
            ->orderBy('vencimento', 'asc')
            ->paginate(15);

        // ── Contratos e Receitas para info adicional ─────────────
        $contratos = ContratoMensal::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->orderBy('status')
            ->get();

        $receitasProcesso = ReceitaProcesso::when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->where('cliente_id', $this->clienteId)
            ->get();

        $anos = range(now()->year - 1, now()->year + 1);
        $meses = [
            '01' => 'Jan', '02' => 'Fev', '03' => 'Mar', '04' => 'Abr',
            '05' => 'Mai', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
            '09' => 'Set', '10' => 'Out', '11' => 'Nov', '12' => 'Dez',
        ];

        return view('livewire.financeiro.cliente', compact(
            'kpiReceitaMensal', 'kpiEmAberto', 'kpiRecebidoAno', 'proximoLanc',
            'lancamentos', 'contratos', 'receitasProcesso', 'contratoAtivo',
            'receitaProcessoAtiva', 'anos', 'meses'
        ))->extends('layouts.app')->section('content');
    }

    private function calcularKpiReceitaMensal(?ContratoMensal $contrato, ?ReceitaProcesso $receita): array
    {
        $valorContrato = $contrato ? (float) $contrato->valor : 0;
        $valorProcesso = 0;
        $qtdProcessos  = 0;

        if ($receita) {
            $qtdProcessos  = \App\Models\Processo::where('cliente_id', $this->clienteId)
                ->where('tenant_id', $this->tenantId)
                ->where('status', 'Ativo')
                ->count();
            $valorProcesso = (float) $receita->valor_unitario * $qtdProcessos;
        }

        $total = $valorContrato + $valorProcesso;

        if ($contrato && $receita) {
            $label = 'Misto';
        } elseif ($contrato) {
            $label = 'Contrato Mensal';
        } elseif ($receita) {
            $label = "{$qtdProcessos} processo(s) × R$ " . number_format($receita->valor_unitario, 2, ',', '.');
        } else {
            $label = 'Sem receita recorrente';
        }

        return ['valor' => $total, 'label' => $label];
    }
}
