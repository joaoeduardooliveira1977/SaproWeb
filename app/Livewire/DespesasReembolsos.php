<?php

namespace App\Livewire;

use App\Models\{DespesaReembolso, DespesaReembolsoAnexo, FinanceiroLancamento, Pessoa, Usuario};
use Illuminate\Support\Facades\{DB, Storage};
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;

class DespesasReembolsos extends Component
{
    use WithFileUploads;

    // ── Filtros ────────────────────────────────────────────────────
    public string $busca          = '';
    public string $filtroTipo     = '';
    public string $filtroStatus   = '';
    public string $filtroDataIni  = '';
    public string $filtroDataFim  = '';
    public ?int   $filtroClienteId = null;

    // ── Modal create/edit ─────────────────────────────────────────
    public bool   $modalAberto    = false;
    public ?int   $despesaId      = null;
    public ?int   $clienteId      = null;
    public string $clienteBusca   = '';
    public array  $clienteSugestoes = [];
    public string $dataLancamento = '';
    public string $tipo           = 'despesa';
    public string $descricao      = '';
    public string $valor          = '';
    public ?int   $responsavelId  = null;
    public string $observacoes    = '';

    // ── Uploads inline no modal ───────────────────────────────────
    public $novoAnexo            = null;
    public string $tipoDocAnexo  = 'comprovante';
    public array  $anexosPendentes = []; // [['nome_original','caminho_temp','tipo_documento','tamanho','mime_type']]

    // ── Modal de anexos ───────────────────────────────────────────
    public bool  $modalAnexos      = false;
    public ?int  $despesaAnexosId  = null;

    // ── Filtro de relatórios ──────────────────────────────────────
    public string $relClienteBusca     = '';
    public ?int   $relClienteId        = null;
    public string $relClienteNome      = '';
    public array  $relClienteSugestoes = [];
    public string $relDataIni          = '';
    public string $relDataFim          = '';

    // ── Confirmações ─────────────────────────────────────────────
    public ?int  $confirmarAprovarId  = null;
    public ?int  $confirmarNaoCobrarId = null;
    public ?int  $confirmarExcluirId  = null;
    public ?int  $gerarTituloId       = null;

    private function tid(): int
    {
        return auth('usuarios')->user()->tenant_id;
    }

    public function mount(): void
    {
        $this->dataLancamento = now()->format('Y-m-d');
        $this->responsavelId  = auth('usuarios')->id();
        $this->filtroDataIni  = now()->startOfMonth()->format('Y-m-d');
        $this->filtroDataFim  = now()->endOfMonth()->format('Y-m-d');
        $this->relDataIni     = now()->startOfMonth()->format('Y-m-d');
        $this->relDataFim     = now()->endOfMonth()->format('Y-m-d');
    }

    // ── Autocomplete cliente ──────────────────────────────────────
    public function updatedClienteBusca(): void
    {
        if (strlen($this->clienteBusca) < 2) {
            $this->clienteSugestoes = [];
            return;
        }
        $this->clienteSugestoes = Pessoa::where('tenant_id', $this->tid())
            ->busca($this->clienteBusca)
            ->limit(8)
            ->get(['id', 'nome', 'cpf_cnpj'])
            ->toArray();
    }

    public function selecionarCliente(int $id, string $nome): void
    {
        $this->clienteId      = $id;
        $this->clienteBusca   = $nome;
        $this->clienteSugestoes = [];
    }

    // ── Autocomplete cliente (relatório) ─────────────────────────
    public function updatedRelClienteBusca(): void
    {
        if (strlen($this->relClienteBusca) < 2) {
            $this->relClienteSugestoes = [];
            return;
        }
        $this->relClienteSugestoes = Pessoa::where('tenant_id', $this->tid())
            ->busca($this->relClienteBusca)
            ->limit(8)
            ->get(['id', 'nome', 'cpf_cnpj'])
            ->toArray();
    }

    public function selecionarRelCliente(int $id, string $nome): void
    {
        $this->relClienteId        = $id;
        $this->relClienteNome      = $nome;
        $this->relClienteBusca     = $nome;
        $this->relClienteSugestoes = [];
    }

    public function limparRelCliente(): void
    {
        $this->relClienteId        = null;
        $this->relClienteNome      = '';
        $this->relClienteBusca     = '';
        $this->relClienteSugestoes = [];
    }

    public function abrirRelatorio(string $tipo): void
    {
        $this->validate([
            'relClienteId' => 'required|integer',
            'relDataIni'   => 'required|date',
            'relDataFim'   => 'required|date|after_or_equal:relDataIni',
        ], [
            'relClienteId.required'      => 'Selecione um cliente.',
            'relDataIni.required'        => 'Informe a data de início.',
            'relDataFim.required'        => 'Informe a data de fim.',
            'relDataFim.after_or_equal'  => 'A data final deve ser igual ou posterior à inicial.',
        ]);

        $routeName = $tipo === 'reembolso' ? 'despesas.relatorio.reembolso' : 'despesas.relatorio.despesas';
        $url = route($routeName, [
            'cliente_id'  => $this->relClienteId,
            'data_inicio' => $this->relDataIni,
            'data_fim'    => $this->relDataFim,
        ]);

        $this->dispatch('abrir-relatorio', url: $url);
    }

    // ── CRUD ──────────────────────────────────────────────────────
    public function abrir(?int $id = null): void
    {
        $this->resetearModal();
        $this->despesaId      = $id;
        $this->dataLancamento = now()->format('Y-m-d');
        $this->responsavelId  = auth('usuarios')->id();

        if ($id) {
            $d = DespesaReembolso::with('cliente')->findOrFail($id);
            $this->clienteId      = $d->cliente_id;
            $this->clienteBusca   = $d->cliente?->nome ?? '';
            $this->dataLancamento = $d->data_lancamento->format('Y-m-d');
            $this->tipo           = $d->tipo;
            $this->descricao      = $d->descricao;
            $this->valor          = number_format($d->valor, 2, ',', '.');
            $this->responsavelId  = $d->responsavel_id;
            $this->observacoes    = $d->observacoes ?? '';
        }

        $this->modalAberto = true;
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
        $this->resetearModal();
    }

    public function salvar(): void
    {
        $this->validate([
            'clienteId'      => 'required|integer',
            'dataLancamento' => 'required|date',
            'tipo'           => 'required|in:despesa,reembolso',
            'descricao'      => 'required|string|max:300',
            'valor'          => 'required',
            'responsavelId'  => 'required|integer',
        ], [
            'clienteId.required'      => 'Selecione o cliente.',
            'descricao.required'      => 'Informe a descrição.',
            'valor.required'          => 'Informe o valor.',
            'responsavelId.required'  => 'Selecione o responsável.',
        ]);

        $valorNum = (float) str_replace(['.', ','], ['', '.'], $this->valor);

        $dados = [
            'cliente_id'      => $this->clienteId,
            'data_lancamento' => $this->dataLancamento,
            'tipo'            => $this->tipo,
            'descricao'       => trim($this->descricao),
            'valor'           => $valorNum,
            'responsavel_id'  => $this->responsavelId,
            'observacoes'     => $this->observacoes ?: null,
        ];

        $usuario = auth('usuarios')->user();

        if ($this->despesaId) {
            $despesa = DespesaReembolso::findOrFail($this->despesaId);
            $despesa->update($dados);
            $usuario->registrarAuditoria('Despesa/Reembolso editado', 'despesas_reembolsos', $despesa->id, null, $dados);
            $this->dispatch('toast', type: 'sucesso', msg: 'Lançamento atualizado!');
        } else {
            $despesa = DespesaReembolso::create(array_merge($dados, ['status' => 'pendente']));
            // Gera automaticamente Contas Pagas (desembolso do escritório)
            FinanceiroLancamento::create([
                'cliente_id'     => $despesa->cliente_id,
                'tipo'           => 'despesa',
                'origem_tipo'    => 'manual',
                'descricao'      => $despesa->descricao,
                'valor'          => $despesa->valor,
                'vencimento'     => $despesa->data_lancamento->format('Y-m-d'),
                'data_pagamento' => $despesa->data_lancamento->format('Y-m-d'),
                'valor_pago'     => $despesa->valor,
                'status'         => 'recebido',
                'observacoes'    => 'Gerado de Despesa #' . $despesa->id,
            ]);
            $usuario->registrarAuditoria('Despesa/Reembolso criado', 'despesas_reembolsos', $despesa->id, null, $dados);
            $this->dispatch('toast', type: 'sucesso', msg: 'Lançamento criado!');
        }

        // Processa anexos pendentes
        $this->salvarAnexosPendentes($despesa->id);

        $this->modalAberto = false;
        $this->resetearModal();
    }

    // ── Upload de anexo no modal ──────────────────────────────────
    public function adicionarAnexo(): void
    {
        $this->validate([
            'novoAnexo'    => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png',
            'tipoDocAnexo' => 'required',
        ], [
            'novoAnexo.required' => 'Selecione um arquivo.',
            'novoAnexo.mimes'    => 'Formato não permitido (PDF, JPG ou PNG).',
            'novoAnexo.max'      => 'Arquivo excede 10 MB.',
        ]);

        $this->anexosPendentes[] = [
            'nome_original' => $this->novoAnexo->getClientOriginalName(),
            'caminho_temp'  => $this->novoAnexo->getRealPath(),
            'tipo_documento'=> $this->tipoDocAnexo,
            'tamanho'       => $this->novoAnexo->getSize(),
            'mime_type'     => $this->novoAnexo->getMimeType(),
            'arquivo_obj'   => $this->novoAnexo,
        ];

        $this->novoAnexo   = null;
        $this->tipoDocAnexo = 'comprovante';
    }

    public function removerAnexoPendente(int $idx): void
    {
        array_splice($this->anexosPendentes, $idx, 1);
    }

    private function salvarAnexosPendentes(int $despesaId): void
    {
        if (empty($this->anexosPendentes)) return;

        $usuario = auth('usuarios')->user();
        $slug    = $usuario->tenant?->slug ?? 'tenant';
        $pasta   = "tenants/{$slug}/despesas/" . now()->format('Y/m');

        foreach ($this->anexosPendentes as $a) {
            if (!isset($a['arquivo_obj'])) continue;
            $caminho = $a['arquivo_obj']->store($pasta, 'public');
            DespesaReembolsoAnexo::create([
                'despesa_reembolso_id' => $despesaId,
                'nome_original'        => $a['nome_original'],
                'caminho'              => $caminho,
                'tipo_documento'       => $a['tipo_documento'],
                'tamanho'              => $a['tamanho'] ?? null,
                'mime_type'            => $a['mime_type'] ?? null,
                'criado_por'           => $usuario->id,
            ]);
        }

        $usuario->registrarAuditoria('Anexos adicionados', 'despesas_reembolsos', $despesaId, null, ['qtd' => count($this->anexosPendentes)]);
        $this->anexosPendentes = [];
    }

    // ── Modal de anexos salvos ────────────────────────────────────
    public function abrirAnexos(int $id): void
    {
        $this->despesaAnexosId = $id;
        $this->modalAnexos     = true;
    }

    public function fecharAnexos(): void
    {
        $this->modalAnexos     = false;
        $this->despesaAnexosId = null;
    }

    public function removerAnexo(int $anexoId): void
    {
        $anexo = DespesaReembolsoAnexo::findOrFail($anexoId);
        Storage::disk('public')->delete($anexo->caminho);
        $despesaId = $anexo->despesa_reembolso_id;
        $anexo->delete();
        auth('usuarios')->user()->registrarAuditoria('Anexo removido', 'despesas_reembolsos_anexos', $anexoId, null, null);
        $this->dispatch('toast', type: 'sucesso', msg: 'Anexo removido.');
    }

    // ── Aprovação ─────────────────────────────────────────────────
    public function aprovar(int $id): void
    {
        $despesa = DespesaReembolso::findOrFail($id);
        $usuario = auth('usuarios')->user();

        $updates = [
            'status'       => 'aprovado',
            'aprovado_por' => $usuario->id,
            'aprovado_em'  => now(),
        ];

        // Gera Contas a Receber apenas se ainda não foi gerado
        if (!$despesa->financeiro_id) {
            $lancamento = FinanceiroLancamento::create([
                'cliente_id'  => $despesa->cliente_id,
                'tipo'        => 'receita',
                'origem_tipo' => 'manual',
                'descricao'   => $despesa->descricao,
                'valor'       => $despesa->valor,
                'vencimento'  => now()->addDays(30)->format('Y-m-d'),
                'status'      => 'previsto',
                'observacoes' => 'A receber - Despesa #' . $despesa->id,
            ]);
            $updates['financeiro_id']   = $lancamento->id;
            $updates['financeiro_tipo'] = 'recebimento';
        }

        $despesa->update($updates);

        $usuario->registrarAuditoria('Despesa/Reembolso aprovado', 'despesas_reembolsos', $id, ['status' => 'pendente'], ['status' => 'aprovado']);
        $this->confirmarAprovarId = null;
        $this->dispatch('toast', type: 'sucesso', msg: 'Lançamento aprovado! Título gerado em Contas a Receber.');
    }

    public function marcarNaoCobrar(int $id): void
    {
        DespesaReembolso::findOrFail($id)->update(['status' => 'nao_cobrar']);
        auth('usuarios')->user()->registrarAuditoria('Despesa/Reembolso marcado como não cobrar', 'despesas_reembolsos', $id, null, null);
        $this->confirmarNaoCobrarId = null;
        $this->dispatch('toast', type: 'sucesso', msg: 'Marcado como não cobrar.');
    }

    public function excluir(int $id): void
    {
        DespesaReembolso::findOrFail($id)->delete();
        auth('usuarios')->user()->registrarAuditoria('Despesa/Reembolso excluído', 'despesas_reembolsos', $id, null, null);
        $this->confirmarExcluirId = null;
        $this->dispatch('toast', type: 'sucesso', msg: 'Lançamento excluído.');
    }

    // ── Gerar título financeiro ───────────────────────────────────
    public function gerarTitulo(int $id): void
    {
        $despesa = DespesaReembolso::findOrFail($id);

        if ($despesa->status !== 'aprovado') {
            $this->dispatch('toast', type: 'erro', msg: 'O lançamento precisa estar aprovado.');
            return;
        }

        $tipo = $despesa->tipo === 'reembolso' ? 'receita' : 'despesa';

        $lancamento = FinanceiroLancamento::create([
            'cliente_id'   => $despesa->cliente_id,
            'tipo'         => $tipo,
            'origem_tipo'  => 'manual',
            'descricao'    => $despesa->descricao,
            'valor'        => $despesa->valor,
            'vencimento'   => $despesa->tipo === 'reembolso'
                ? now()->addDays(30)->format('Y-m-d')
                : now()->format('Y-m-d'),
            'status'       => 'previsto',
            'forma_pagamento' => 'pix',
            'observacoes'  => 'Gerado automaticamente de Despesas/Reembolsos #' . $despesa->id,
        ]);

        $despesa->update([
            'status'          => 'ja_cobrado',
            'financeiro_id'   => $lancamento->id,
            'financeiro_tipo' => $tipo === 'receita' ? 'recebimento' : 'pagamento',
        ]);

        auth('usuarios')->user()->registrarAuditoria('Título financeiro gerado', 'despesas_reembolsos', $despesa->id, null, ['financeiro_id' => $lancamento->id, 'tipo' => $tipo]);
        $this->gerarTituloId = null;
        $this->dispatch('toast', type: 'sucesso', msg: 'Título financeiro gerado com sucesso!');
    }

    // ── Computed ──────────────────────────────────────────────────
    #[Computed]
    public function usuarios(): \Illuminate\Database\Eloquent\Collection
    {
        return Usuario::where('tenant_id', $this->tid())
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome']);
    }

    #[Computed]
    public function totais(): array
    {
        $tid = $this->tid();
        $ini = $this->filtroDataIni ?: now()->startOfMonth()->format('Y-m-d');
        $fim = $this->filtroDataFim ?: now()->endOfMonth()->format('Y-m-d');

        $base = DespesaReembolso::where('tenant_id', $tid)
            ->whereBetween('data_lancamento', [$ini, $fim]);

        return [
            'despesas'          => (clone $base)->where('tipo', 'despesa')->sum('valor'),
            'reembolsos'        => (clone $base)->where('tipo', 'reembolso')->sum('valor'),
            'pendente_aprovacao'=> (clone $base)->where('status', 'pendente')->sum('valor'),
            'aprovado_cobranca' => (clone $base)->where('status', 'aprovado')->sum('valor'),
        ];
    }

    #[Computed]
    public function despesas(): \Illuminate\Support\Collection
    {
        $tid = $this->tid();

        return DespesaReembolso::where('despesas_reembolsos.tenant_id', $tid)
            ->with(['cliente:id,nome', 'responsavel:id,nome'])
            ->withCount('anexos')
            ->when($this->busca, fn($q) => $q->where(function ($q) {
                $q->where('descricao', 'ilike', "%{$this->busca}%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->busca}%"));
            }))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroClienteId, fn($q) => $q->where('cliente_id', $this->filtroClienteId))
            ->when($this->filtroDataIni, fn($q) => $q->where('data_lancamento', '>=', $this->filtroDataIni))
            ->when($this->filtroDataFim, fn($q) => $q->where('data_lancamento', '<=', $this->filtroDataFim))
            ->orderByDesc('data_lancamento')
            ->orderByDesc('id')
            ->limit(200)
            ->get();
    }

    #[Computed]
    public function anexosDaDespesa(): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->despesaAnexosId) return new \Illuminate\Database\Eloquent\Collection();
        return DespesaReembolsoAnexo::where('despesa_reembolso_id', $this->despesaAnexosId)->get();
    }

    private function resetearModal(): void
    {
        $this->despesaId       = null;
        $this->clienteId       = null;
        $this->clienteBusca    = '';
        $this->clienteSugestoes = [];
        $this->dataLancamento  = now()->format('Y-m-d');
        $this->tipo            = 'despesa';
        $this->descricao       = '';
        $this->valor           = '';
        $this->responsavelId   = auth('usuarios')->id();
        $this->observacoes     = '';
        $this->novoAnexo       = null;
        $this->tipoDocAnexo    = 'comprovante';
        $this->anexosPendentes = [];
        $this->resetErrorBag();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.despesas-reembolsos', [
            'despesas'        => $this->despesas,
            'totais'          => $this->totais,
            'usuarios'        => $this->usuarios,
            'anexosDaDespesa' => $this->anexosDaDespesa,
        ])->extends('layouts.app')->section('content');
    }
}
