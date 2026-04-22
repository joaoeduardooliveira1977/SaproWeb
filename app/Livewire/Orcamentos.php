<?php

namespace App\Livewire;

use App\Models\{CrmOportunidade, Orcamento, Pessoa};
use Illuminate\Support\Facades\{Auth, DB};
use Livewire\Component;

class Orcamentos extends Component
{
    public string $filtroStatus = '';
    public string $filtroBusca  = '';

    // Modal
    public bool   $modal       = false;
    public ?int   $orcId       = null;
    public string $opNome      = '';
    public string $opEmail     = '';
    public string $opTelefone  = '';
    public string $opTitulo    = '';
    public string $opArea      = '';
    public string $opDescricao = '';
    public string $opObs       = '';
    public string $opTipo      = 'fixo';
    public string $opValor     = '';
    public string $opParcelas  = '1';
    public string $opPercentual= '';
    public string $opValorHora = '';
    public string $opValidade  = '';
    public string $opOportunidade = '';
    public string $opPessoa    = '';

    // Modal recusa
    public bool   $modalRecusa  = false;
    public ?int   $recusandoId  = null;
    public string $motivoRecusa = '';

    protected $queryString = [
        'filtroStatus' => ['except' => ''],
        'filtroBusca'  => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->opValidade = now()->addDays(15)->format('Y-m-d');

        // Abre modal pré-preenchido quando vem do CRM (?crm=ID)
        if ($crmId = request('crm')) {
            $this->novo((int) $crmId);
        }
    }

    // ── Modal ──────────────────────────────────────────────────

    public function novo(?int $oportunidadeId = null): void
    {
        $this->resetOp();
        if ($oportunidadeId) {
            $op = CrmOportunidade::find($oportunidadeId);
            if ($op) {
                $this->opOportunidade = (string) $op->id;
                $this->opNome         = $op->nome;
                $this->opEmail        = $op->email ?? '';
                $this->opTelefone     = $op->telefone ?? '';
                $this->opTitulo       = $op->titulo ?? '';
                $this->opArea         = $op->area_direito ?? '';
                $this->opValor        = $op->valor_estimado ? number_format($op->valor_estimado, 2, ',', '.') : '';
            }
        }
        $this->modal = true;
    }

    public function editar(int $id): void
    {
        $orc = Orcamento::find($id);
        if (! $orc) return;

        $this->orcId         = $orc->id;
        $this->opNome        = $orc->nome_cliente;
        $this->opEmail       = $orc->email_cliente ?? '';
        $this->opTelefone    = $orc->telefone_cliente ?? '';
        $this->opTitulo      = $orc->titulo;
        $this->opArea        = $orc->area_direito ?? '';
        $this->opDescricao   = $orc->descricao ?? '';
        $this->opObs         = $orc->observacoes ?? '';
        $this->opTipo        = $orc->tipo_honorario;
        $this->opValor       = $orc->valor ? number_format($orc->valor, 2, ',', '.') : '';
        $this->opParcelas    = (string) $orc->parcelas;
        $this->opPercentual  = $orc->percentual_exito ?? '';
        $this->opValorHora   = $orc->valor_hora ?? '';
        $this->opValidade    = $orc->validade?->format('Y-m-d') ?? '';
        $this->opOportunidade= (string) ($orc->oportunidade_id ?? '');
        $this->opPessoa      = (string) ($orc->pessoa_id ?? '');
        $this->modal         = true;
        $this->resetErrorBag();
    }

    public function salvar(): void
    {
        $this->validate([
            'opNome'   => 'required|min:2',
            'opTitulo' => 'required|min:3',
            'opTipo'   => 'required',
        ], [
            'opNome.required'   => 'Informe o nome do cliente.',
            'opTitulo.required' => 'Informe o título da proposta.',
        ]);

        $tenantId = tenant_id();
        $valor    = $this->opValor ? (float) str_replace(['.', ','], ['', '.'], $this->opValor) : 0;
        $parcelas = max(1, (int) $this->opParcelas);

        $dados = [
            'tenant_id'        => $tenantId,
            'nome_cliente'     => $this->opNome,
            'email_cliente'    => $this->opEmail ?: null,
            'telefone_cliente' => $this->opTelefone ?: null,
            'titulo'           => $this->opTitulo,
            'area_direito'     => $this->opArea ?: null,
            'descricao'        => $this->opDescricao ?: null,
            'observacoes'      => $this->opObs ?: null,
            'tipo_honorario'   => $this->opTipo,
            'valor'            => $valor,
            'parcelas'         => $parcelas,
            'valor_parcela'    => $parcelas > 1 ? round($valor / $parcelas, 2) : null,
            'percentual_exito' => $this->opPercentual ?: null,
            'valor_hora'       => $this->opValorHora ?: null,
            'validade'         => $this->opValidade ?: null,
            'oportunidade_id'  => $this->opOportunidade ?: null,
            'pessoa_id'        => $this->opPessoa ?: null,
            'usuario_id'       => Auth::guard('usuarios')->id(),
        ];

        if ($this->orcId) {
            Orcamento::find($this->orcId)?->update($dados);
            $msg = 'Orçamento atualizado.';
        } else {
            $dados['numero'] = Orcamento::proximoNumero($tenantId);
            $dados['status'] = 'rascunho';
            Orcamento::create($dados);
            $msg = 'Orçamento criado.';
        }

        $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: $msg);
    }

    public function excluir(int $id): void
    {
        Orcamento::destroy($id);
        $this->dispatch('toast', tipo: 'success', msg: 'Orçamento excluído.');
    }

    public function marcarEnviado(int $id): void
    {
        Orcamento::where('id', $id)->update(['status' => 'enviado']);
        $this->dispatch('toast', tipo: 'success', msg: 'Orçamento marcado como enviado.');
    }

    public function marcarAceito(int $id): void
    {
        Orcamento::where('id', $id)->update([
            'status'        => 'aceito',
            'data_resposta' => today(),
        ]);

        // Atualiza etapa do CRM se vinculado
        $orc = Orcamento::find($id);
        if ($orc?->oportunidade_id) {
            CrmOportunidade::where('id', $orc->oportunidade_id)
                ->update(['etapa' => 'ganho', 'data_fechamento' => today()]);
        }

        $this->dispatch('toast', tipo: 'success', msg: 'Proposta aceita! CRM atualizado.');
    }

    public function abrirRecusa(int $id): void
    {
        $this->recusandoId  = $id;
        $this->motivoRecusa = '';
        $this->modalRecusa  = true;
    }

    public function confirmarRecusa(): void
    {
        if (! $this->recusandoId) return;

        Orcamento::where('id', $this->recusandoId)->update([
            'status'        => 'recusado',
            'data_resposta' => today(),
            'motivo_recusa' => $this->motivoRecusa ?: null,
        ]);

        $orc = Orcamento::find($this->recusandoId);
        if ($orc?->oportunidade_id) {
            CrmOportunidade::where('id', $orc->oportunidade_id)
                ->update(['etapa' => 'perdido', 'data_fechamento' => today(), 'motivo_perda' => $this->motivoRecusa ?: null]);
        }

        $this->modalRecusa = false;
        $this->recusandoId = null;
        $this->dispatch('toast', tipo: 'info', msg: 'Proposta recusada. CRM atualizado.');
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetOp();
        $this->resetErrorBag();
    }

    private function resetOp(): void
    {
        $this->orcId = null;
        $this->opNome = $this->opEmail = $this->opTelefone = '';
        $this->opTitulo = $this->opArea = $this->opDescricao = $this->opObs = '';
        $this->opTipo = 'fixo';
        $this->opValor = '';
        $this->opParcelas = '1';
        $this->opPercentual = $this->opValorHora = '';
        $this->opValidade = now()->addDays(15)->format('Y-m-d');
        $this->opOportunidade = $this->opPessoa = '';
    }

    // ── Render ─────────────────────────────────────────────────

    public function render()
    {
        $q = Orcamento::with(['oportunidade', 'usuario'])->latest();

        if ($this->filtroStatus) $q->where('status', $this->filtroStatus);
        if ($this->filtroBusca) {
            $t = $this->filtroBusca;
            $q->where(fn($s) => $s
                ->where('nome_cliente', 'ilike', "%{$t}%")
                ->orWhere('titulo', 'ilike', "%{$t}%")
                ->orWhere('numero', 'ilike', "%{$t}%")
            );
        }

        $orcamentos = $q->limit(200)->get();

        $kpis = [
            'total'    => $orcamentos->count(),
            'enviados' => $orcamentos->where('status', 'enviado')->count(),
            'aceitos'  => $orcamentos->where('status', 'aceito')->count(),
            'valor_pipeline' => $orcamentos->whereIn('status', ['rascunho', 'enviado'])->sum('valor'),
            'valor_aceito'   => $orcamentos->where('status', 'aceito')->sum('valor'),
        ];

        $oportunidades = CrmOportunidade::whereIn('etapa', ['proposta', 'negociacao', 'reuniao', 'qualificacao'])
            ->orderBy('nome')->get();

        $pessoas = Pessoa::doTipo('Cliente')->ativos()->orderBy('nome')->limit(200)->get();

        return view('livewire.orcamentos', compact('orcamentos', 'kpis', 'oportunidades', 'pessoas'));
    }
}
