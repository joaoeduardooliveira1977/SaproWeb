<?php

namespace App\Livewire;

use App\Models\{Andamento, Documento, FinanceiroLancamento, HonorarioParcela, Pessoa, Prazo, Processo};
use Illuminate\Support\Facades\{Auth, Storage};
use Livewire\Component;
use Livewire\WithFileUploads;

class PastaCliente extends Component
{
    use WithFileUploads;

    public int    $clienteId;
    public string $aba = 'processos'; // processos | prazos | honorarios | documentos | financeiro | historico

    // ── Upload de documento ───────────────────────────────────
    public bool    $modalDoc       = false;
    public ?int    $docId          = null;
    public string  $docTitulo      = '';
    public string  $docTipo        = 'outro';
    public string  $docDescricao   = '';
    public string  $docData        = '';
    public         $docArquivo     = null;

    // ── Modal lançamento avulso (receita/despesa sem processo) ───
    public bool   $modalLanc         = false;
    public ?int   $lancId            = null;
    public string $lancTipo          = 'receita';
    public string $lancDescricao     = '';
    public string $lancValor         = '';
    public string $lancVencimento    = '';
    public string $lancParcelas      = '1';
    public string $lancForma         = '';
    public string $lancStatus        = 'previsto';
    public string $lancDataPagamento = '';
    public string $lancValorPago     = '';
    public string $lancObservacoes   = '';
    public ?int   $lancProcessoId    = null;
    public string $lancTipoHonorario = '';   // fixo | hora | exito | ''
    public string $lancPercentualExito = '20';
    public string $filtroFinanceiro  = 'todos';

    public const FORMAS = [
        'pix'          => 'PIX',
        'boleto'       => 'Boleto',
        'cartao'       => 'Cartão',
        'dinheiro'     => 'Dinheiro',
        'transferencia'=> 'Transferência',
        'outro'        => 'Outro',
    ];

    public const TIPOS_DOC = [
        'contrato'           => 'Contrato',
        'procuracao'         => 'Procuração',
        'identidade'         => 'Identidade / CPF',
        'comprovante'        => 'Comprovante',
        'peticao'            => 'Petição',
        'decisao'            => 'Decisão',
        'certidao'           => 'Certidão',
        'outro'              => 'Outro',
    ];

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->docData   = now()->format('Y-m-d');

        // BelongsToTenant global scope garante isolamento; 404 se não pertencer ao tenant
        Pessoa::findOrFail($clienteId);
    }

    // ── Abrir modal de upload ─────────────────────────────────
    public function abrirModalDoc(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->docId        = $id;
        $this->docArquivo   = null;

        if ($id) {
            $doc = Documento::findOrFail($id);
            $this->docTitulo    = $doc->titulo;
            $this->docTipo      = $doc->tipo ?? 'outro';
            $this->docDescricao = $doc->descricao ?? '';
            $this->docData      = $doc->data_documento?->format('Y-m-d') ?? now()->format('Y-m-d');
        } else {
            $this->docTitulo    = '';
            $this->docTipo      = 'outro';
            $this->docDescricao = '';
            $this->docData      = now()->format('Y-m-d');
        }

        $this->modalDoc = true;
    }

    public function fecharModalDoc(): void
    {
        $this->modalDoc   = false;
        $this->docArquivo = null;
        $this->resetErrorBag();
    }

    // ── Salvar documento ──────────────────────────────────────
    public function salvarDocumento(): void
    {
        $this->validate([
            'docTitulo'   => 'required|string|max:200',
            'docTipo'     => 'required|string',
            'docData'     => 'nullable|date',
            'docArquivo'  => $this->docId ? 'nullable|file|max:20480' : 'required|file|max:20480',
        ], [
            'docTitulo.required'  => 'O título é obrigatório.',
            'docArquivo.required' => 'Selecione um arquivo.',
            'docArquivo.max'      => 'O arquivo não pode passar de 20 MB.',
        ]);

        $dados = [
            'cliente_id'     => $this->clienteId,
            'processo_id'    => null,
            'tipo'           => $this->docTipo,
            'titulo'         => $this->docTitulo,
            'descricao'      => $this->docDescricao ?: null,
            'data_documento' => $this->docData ?: null,
            'uploaded_by'    => Auth::guard('usuarios')->user()?->nome ?? 'Sistema',
        ];

        if ($this->docArquivo) {
            $dados['arquivo']          = $this->docArquivo->store('documentos', 'public');
            $dados['arquivo_original'] = $this->docArquivo->getClientOriginalName();
            $dados['mime_type']        = $this->docArquivo->getMimeType();
            $dados['tamanho']          = $this->docArquivo->getSize();
        }

        if ($this->docId) {
            Documento::findOrFail($this->docId)->update($dados);
        } else {
            Documento::create($dados);
        }

        $this->fecharModalDoc();
        $this->dispatch('toast', message: 'Documento salvo com sucesso!', type: 'success');
    }

    // ── Lançamentos avulsos (receita/despesa sem processo) ───────
    public function abrirModalLanc(string $tipo = 'receita', ?int $id = null): void
    {
        $this->resetValidation();
        $this->lancId      = $id;
        $this->lancTipo    = $tipo;
        $this->lancParcelas = '1';

        if ($id) {
            $l = FinanceiroLancamento::findOrFail($id);
            $this->lancTipo          = $l->tipo;
            $this->lancDescricao     = $l->descricao;
            $this->lancValor         = (string) $l->valor;
            $this->lancVencimento    = $l->vencimento->format('Y-m-d');
            $this->lancForma         = $l->forma_pagamento ?? '';
            $this->lancStatus        = $l->status;
            $this->lancDataPagamento = $l->data_pagamento?->format('Y-m-d') ?? '';
            $this->lancValorPago     = $l->valor_pago ? (string) $l->valor_pago : '';
            $this->lancObservacoes   = $l->observacoes ?? '';
            $this->lancProcessoId   = $l->processo_id;
        } else {
            $this->lancDescricao     = '';
            $this->lancValor         = '';
            $this->lancVencimento    = now()->format('Y-m-d');
            $this->lancForma         = '';
            $this->lancStatus        = 'previsto';
            $this->lancDataPagamento = '';
            $this->lancValorPago     = '';
            $this->lancObservacoes    = '';
            $this->lancProcessoId    = null;
            $this->lancTipoHonorario = '';
            $this->lancPercentualExito = '20';
        }

        $this->modalLanc = true;
    }

    private function calcularExito(): void
    {
        if ($this->lancTipoHonorario !== 'exito' || !$this->lancProcessoId || !$this->lancPercentualExito) {
            return;
        }

        $valorCausa = Processo::where('id', $this->lancProcessoId)
            ->where('cliente_id', $this->clienteId)
            ->value('valor_causa');

        if ($valorCausa > 0) {
            $this->lancValor = number_format(
                (float) $valorCausa * (float) $this->lancPercentualExito / 100,
                2, '.', ''
            );
        }
    }

    public function updatedLancTipoHonorario(): void   { $this->calcularExito(); }
    public function updatedLancPercentualExito(): void  { $this->calcularExito(); }
    public function updatedLancProcessoId(): void       { $this->calcularExito(); }

    public function fecharModalLanc(): void
    {
        $this->modalLanc = false;
        $this->resetValidation();
    }

    public function salvarLancamento(): void
    {
        $this->validate([
            'lancDescricao'     => 'required|string|max:200',
            'lancValor'         => 'required|numeric|min:0.01',
            'lancVencimento'    => 'required|date',
            'lancParcelas'      => 'required|integer|min:1|max:60',
            'lancStatus'        => 'required|in:previsto,recebido',
            'lancDataPagamento' => 'nullable|date|required_if:lancStatus,recebido',
            'lancValorPago'     => 'nullable|numeric|required_if:lancStatus,recebido',
        ], [
            'lancDescricao.required'        => 'A descrição é obrigatória.',
            'lancValor.required'            => 'O valor é obrigatório.',
            'lancVencimento.required'       => 'A data de vencimento é obrigatória.',
            'lancDataPagamento.required_if' => 'Informe a data de pagamento.',
            'lancValorPago.required_if'     => 'Informe o valor pago.',
        ]);

        $totalParcelas = $this->lancId ? 1 : max(1, (int) $this->lancParcelas);
        $valor         = (float) $this->lancValor;

        $dadosBase = [
            'cliente_id'      => $this->clienteId,
            'contrato_id'     => null,
            'processo_id'     => $this->lancProcessoId ?: null,
            'tipo'            => $this->lancTipo,
            'forma_pagamento' => $this->lancForma ?: null,
            'observacoes'     => $this->lancObservacoes ?: null,
        ];

        if ($this->lancId || $totalParcelas === 1) {
            $dados = array_merge($dadosBase, [
                'descricao'      => $this->lancDescricao,
                'valor'          => $valor,
                'vencimento'     => $this->lancVencimento,
                'status'         => $this->lancStatus,
                'data_pagamento' => $this->lancStatus === 'recebido' ? $this->lancDataPagamento : null,
                'valor_pago'     => $this->lancStatus === 'recebido' ? $this->lancValorPago : null,
                'numero_parcela' => null,
                'total_parcelas' => null,
            ]);

            $this->lancId
                ? FinanceiroLancamento::findOrFail($this->lancId)->update($dados)
                : FinanceiroLancamento::create($dados);
        } else {
            $valorParcela = round($valor / $totalParcelas, 2);
            $vencimento   = \Carbon\Carbon::parse($this->lancVencimento);

            for ($i = 1; $i <= $totalParcelas; $i++) {
                $valorAtual = ($i === $totalParcelas)
                    ? round($valor - ($valorParcela * ($totalParcelas - 1)), 2)
                    : $valorParcela;

                FinanceiroLancamento::create(array_merge($dadosBase, [
                    'descricao'      => $this->lancDescricao . " ({$i}/{$totalParcelas})",
                    'valor'          => $valorAtual,
                    'vencimento'     => $vencimento->copy()->addMonths($i - 1)->format('Y-m-d'),
                    'status'         => 'previsto',
                    'data_pagamento' => null,
                    'valor_pago'     => null,
                    'numero_parcela' => $i,
                    'total_parcelas' => $totalParcelas,
                ]));
            }
        }

        $this->fecharModalLanc();
        $msg = $totalParcelas > 1 ? "{$totalParcelas} parcelas geradas!" : 'Lançamento salvo!';
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function marcarLancamentoPago(int $id): void
    {
        $l = FinanceiroLancamento::findOrFail($id);
        $l->update([
            'status'         => 'recebido',
            'data_pagamento' => now()->format('Y-m-d'),
            'valor_pago'     => $l->valor,
        ]);
        $this->dispatch('toast', message: 'Lançamento marcado como ' . ($l->tipo === 'receita' ? 'recebido' : 'pago') . '!', type: 'success');
    }

    public function excluirLancamento(int $id): void
    {
        FinanceiroLancamento::whereNull('contrato_id')->findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Lançamento excluído.', type: 'success');
    }

    // ── Excluir documento ─────────────────────────────────────
    public function excluirDocumento(int $id): void
    {
        $doc = Documento::findOrFail($id);
        if ($doc->arquivo) {
            Storage::disk('public')->delete($doc->arquivo);
        }
        $doc->delete();
        $this->dispatch('toast', message: 'Documento excluído.', type: 'success');
    }

    public function render(): \Illuminate\View\View
    {
        $cliente  = Pessoa::findOrFail($this->clienteId);

        // ── Processos ────────────────────────────────────────────────
        $processos = Processo::with(['fase', 'advogado', 'risco', 'tipoAcao'])
            ->where('cliente_id', $this->clienteId)
            ->orderByRaw("CASE status WHEN 'Ativo' THEN 0 ELSE 1 END")
            ->orderByDesc('created_at')
            ->get();

        $totalAtivos    = $processos->where('status', 'Ativo')->count();
        $totalArquivados = $processos->where('status', '!=', 'Ativo')->count();

        // ── Prazos próximos (processos do cliente) ───────────────────
        $processosIds = $processos->pluck('id');

        $prazos = Prazo::with(['processo:id,numero', 'responsavel:id,nome'])
            ->whereIn('processo_id', $processosIds)
            ->where('status', 'aberto')
            ->orderBy('data_prazo')
            ->take(20)
            ->get();

        $totalPrazosVencidos = $prazos->filter(fn($p) => $p->data_prazo->isPast())->count();
        $totalPrazosHoje     = $prazos->filter(fn($p) => $p->data_prazo->isToday())->count();

        // ── Honorários em aberto ─────────────────────────────────────
        $parcelas = HonorarioParcela::with(['honorario.processo:id,numero'])
            ->whereHas('honorario', fn($q) => $q->where('cliente_id', $this->clienteId))
            ->whereIn('status', ['pendente', 'vencido'])
            ->orderBy('vencimento')
            ->take(30)
            ->get();

        $totalHonorarios = $parcelas->sum('valor');

        // ── Documentos ───────────────────────────────────────────────
        $documentos = Documento::where(function ($q) use ($processosIds) {
                $q->whereIn('processo_id', $processosIds)
                  ->orWhere('cliente_id', $this->clienteId);
            })
            ->orderByDesc('created_at')
            ->take(30)
            ->get();

        // ── Histórico de andamentos ───────────────────────────────────
        $historico = Andamento::with(['processo:id,numero'])
            ->whereIn('processo_id', $processosIds)
            ->orderByDesc('created_at')
            ->take(25)
            ->get();

        // ── Valor total em risco ─────────────────────────────────────
        $valorRisco = $processos->where('status', 'Ativo')->sum('valor_risco');
        $valorCausa = $processos->where('status', 'Ativo')->sum('valor_causa');

        // ── Lançamentos financeiros (só carrega quando a aba está ativa) ──
        $lancamentos               = collect();
        $totalLancamentosAtrasados = 0;
        $lancamentosAReceber       = 0;
        $lancamentosRecebido       = 0;
        $lancamentosAPagar         = 0;
        $lancamentosPago           = 0;

        if ($this->aba === 'financeiro') {
            $todos = FinanceiroLancamento::with(['contrato', 'processo:id,numero'])
                ->where('cliente_id', $this->clienteId)
                ->whereNotIn('status', ['cancelado'])
                ->orderBy('vencimento')
                ->get();

            $totalLancamentosAtrasados = $todos->where('status', 'atrasado')->count();

            $todosReceitas = $todos->where('tipo', 'receita');
            $todosDespesas = $todos->where('tipo', 'despesa');

            $lancamentosAReceber = $todosReceitas->whereIn('status', ['previsto', 'atrasado'])->sum('valor');
            $lancamentosRecebido = $todosReceitas->where('status', 'recebido')->sum('valor_pago');
            $lancamentosAPagar   = $todosDespesas->whereIn('status', ['previsto', 'atrasado'])->sum('valor');
            $lancamentosPago     = $todosDespesas->where('status', 'recebido')->sum('valor_pago');

            $lancamentos = match ($this->filtroFinanceiro) {
                'receitas' => $todosReceitas->values(),
                'despesas' => $todosDespesas->values(),
                'avulsos'  => $todos->filter(fn($l) => !$l->contrato_id && !$l->processo_id)->values(),
                default    => $todos,
            };
        } else {
            $totalLancamentosAtrasados = FinanceiroLancamento::where('cliente_id', $this->clienteId)
                ->where('status', 'atrasado')
                ->count();
        }

        $tiposDoc = self::TIPOS_DOC;
        $formas   = self::FORMAS;

        return view('livewire.pasta-cliente', compact(
            'cliente', 'processos', 'totalAtivos', 'totalArquivados',
            'prazos', 'totalPrazosVencidos', 'totalPrazosHoje',
            'parcelas', 'totalHonorarios',
            'documentos', 'historico',
            'lancamentos', 'totalLancamentosAtrasados',
            'lancamentosAReceber', 'lancamentosRecebido',
            'lancamentosAPagar', 'lancamentosPago',
            'valorRisco', 'valorCausa',
            'tiposDoc', 'formas'
        ));
    }
}
