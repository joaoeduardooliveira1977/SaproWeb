<?php

namespace App\Livewire;

use App\Models\WorkflowAcao;
use App\Models\WorkflowExecucao;
use App\Models\WorkflowRegra;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WorkflowRegras extends Component
{
    use WithPagination;

    // ── Tabs ──────────────────────────────────────────────────────
    public string $aba = 'regras'; // regras | historico

    // ── Modal de criação/edição ───────────────────────────────────
    public bool  $modal    = false;
    public ?int  $editandoId = null;

    // ── Campos do formulário ──────────────────────────────────────
    public string $nome       = '';
    public string $descricao  = '';
    public string $gatilho    = '';
    public bool   $ativo      = true;

    // gatilho_config como campos simples (ex: dias para sem_andamento_dias)
    public string $gatilhoConfigJson = '{}';

    // Condições: array de ['campo' => '', 'op' => 'igual', 'valor' => '']
    public array $condicoes = [];

    // Ações: array de ['tipo' => '', 'config_json' => '{}']
    public array $acoes = [];

    // ── Filtros / busca ───────────────────────────────────────────
    public string $busca   = '';
    public string $filtroGatilho = '';

    // ── Confirmação de exclusão ───────────────────────────────────
    public ?int $confirmandoExclusao = null;

    protected $queryString = [
        'aba' => ['except' => 'regras'],
    ];

    // ── Validação ─────────────────────────────────────────────────
    protected function rules(): array
    {
        return [
            'nome'              => 'required|min:3|max:120',
            'descricao'         => 'nullable|max:500',
            'gatilho'           => 'required',
            'gatilhoConfigJson' => 'nullable',
            'condicoes'         => 'array',
            'acoes'             => 'array|min:1',
            'acoes.*.tipo'      => 'required',
        ];
    }

    protected $messages = [
        'nome.required'        => 'O nome da regra é obrigatório.',
        'nome.min'             => 'O nome deve ter ao menos 3 caracteres.',
        'gatilho.required'     => 'Selecione um gatilho.',
        'acoes.min'            => 'Adicione ao menos uma ação.',
        'acoes.*.tipo.required' => 'Selecione o tipo de cada ação.',
    ];

    // ── Ciclo de vida ─────────────────────────────────────────────

    public function updatingBusca(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroGatilho(): void
    {
        $this->resetPage();
    }

    // ── Abrir modal ───────────────────────────────────────────────

    public function novaRegra(): void
    {
        $this->resetForm();
        $this->acoes = [['tipo' => '', 'config_json' => '{}']];
        $this->modal = true;
    }

    public function editarRegra(int $id): void
    {
        $regra = WorkflowRegra::with('acoes')->findOrFail($id);

        $this->editandoId      = $id;
        $this->nome            = $regra->nome;
        $this->descricao       = $regra->descricao ?? '';
        $this->gatilho         = $regra->gatilho;
        $this->ativo           = $regra->ativo;
        $this->gatilhoConfigJson = json_encode($regra->gatilho_config ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->condicoes       = $regra->condicoes ?? [];
        $this->acoes           = $regra->acoes->map(fn($a) => [
            'tipo'        => $a->tipo,
            'config_json' => json_encode($a->config ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ])->toArray();

        if (empty($this->acoes)) {
            $this->acoes = [['tipo' => '', 'config_json' => '{}']];
        }

        if (empty($this->condicoes)) {
            $this->condicoes = [];
        }

        $this->modal = true;
    }

    public function fecharModal(): void
    {
        $this->modal = false;
        $this->resetForm();
    }

    // ── Salvar (criar ou atualizar) ───────────────────────────────

    public function salvar(): void
    {
        $this->validate();

        $gatilhoConfig = [];
        try {
            $gatilhoConfig = json_decode($this->gatilhoConfigJson, true) ?? [];
        } catch (\Throwable) {}

        $dados = [
            'nome'           => trim($this->nome),
            'descricao'      => trim($this->descricao) ?: null,
            'gatilho'        => $this->gatilho,
            'gatilho_config' => $gatilhoConfig,
            'condicoes'      => $this->condicoes,
            'ativo'          => $this->ativo,
        ];

        if ($this->editandoId) {
            $regra = WorkflowRegra::findOrFail($this->editandoId);
            $regra->update($dados);
            $regra->acoes()->delete();
        } else {
            $dados['tenant_id']       = Auth::user()->tenant_id;
            $dados['execucoes_total'] = 0;
            $regra = WorkflowRegra::create($dados);
        }

        // Recriar ações na ordem
        foreach ($this->acoes as $index => $acaoForm) {
            $config = [];
            try {
                $config = json_decode($acaoForm['config_json'] ?? '{}', true) ?? [];
            } catch (\Throwable) {}

            WorkflowAcao::create([
                'regra_id' => $regra->id,
                'ordem'    => $index + 1,
                'tipo'     => $acaoForm['tipo'],
                'config'   => $config,
            ]);
        }

        $this->modal = false;
        $this->resetForm();
        $this->dispatch('toast', message: 'Regra salva com sucesso.', type: 'success');
    }

    // ── Toggle ativo ──────────────────────────────────────────────

    public function toggleAtivo(int $id): void
    {
        $regra = WorkflowRegra::findOrFail($id);
        $regra->update(['ativo' => !$regra->ativo]);
        $this->dispatch('toast',
            message: $regra->fresh()->ativo ? 'Regra ativada.' : 'Regra desativada.',
            type: 'info'
        );
    }

    // ── Exclusão ──────────────────────────────────────────────────

    public function confirmarExclusao(int $id): void
    {
        $this->confirmandoExclusao = $id;
    }

    public function cancelarExclusao(): void
    {
        $this->confirmandoExclusao = null;
    }

    public function excluirRegra(): void
    {
        if (!$this->confirmandoExclusao) return;

        WorkflowRegra::findOrFail($this->confirmandoExclusao)->delete();
        $this->confirmandoExclusao = null;
        $this->dispatch('toast', message: 'Regra excluída.', type: 'success');
    }

    // ── Gerenciar condições ───────────────────────────────────────

    public function adicionarCondicao(): void
    {
        $this->condicoes[] = ['campo' => 'andamento.descricao', 'op' => 'contem', 'valor' => ''];
    }

    public function removerCondicao(int $index): void
    {
        array_splice($this->condicoes, $index, 1);
    }

    // ── Gerenciar ações ───────────────────────────────────────────

    public function adicionarAcao(): void
    {
        $this->acoes[] = ['tipo' => '', 'config_json' => '{}'];
    }

    public function removerAcao(int $index): void
    {
        array_splice($this->acoes, $index, 1);
    }

    public function preencherConfigPadrao(int $index): void
    {
        $tipo = $this->acoes[$index]['tipo'] ?? '';

        $templates = [
            WorkflowRegra::ACAO_CRIAR_PRAZO => [
                'titulo'         => 'Prazo — {andamento_descricao}',
                'dias'           => 15,
                'tipo_contagem'  => 'uteis',
                'prazo_fatal'    => false,
                'responsavel'    => 'advogado_processo',
            ],
            WorkflowRegra::ACAO_CRIAR_NOTIFICACAO => [
                'tipo'        => 'alerta',
                'titulo'      => 'Atenção: {andamento_descricao}',
                'mensagem'    => 'Novo andamento no processo {numero}.',
                'destinatario' => 'advogado_processo',
            ],
            WorkflowRegra::ACAO_CRIAR_AGENDA => [
                'titulo'         => 'Compromisso — {numero}',
                'tipo'           => 'prazo',
                'dias_a_partir'  => 1,
                'hora'           => '09:00',
                'urgente'        => false,
                'responsavel'    => 'advogado_processo',
            ],
            WorkflowRegra::ACAO_ENVIAR_WHATSAPP => [
                'destinatario' => 'advogado_processo',
                'mensagem'     => 'Novo andamento no processo {numero}: {andamento_descricao}',
                'canal'        => 'whatsapp',
            ],
            WorkflowRegra::ACAO_ATUALIZAR_SCORE => [
                'score' => 'auto',
            ],
            WorkflowRegra::ACAO_CHAMAR_IA => [
                'tipo'     => 'resumo_andamento',
                'salvar_em' => 'andamento.resumo_ia',
            ],
        ];

        if (isset($templates[$tipo])) {
            $this->acoes[$index]['config_json'] = json_encode($templates[$tipo], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }

    // ── Reset form ────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->editandoId       = null;
        $this->nome             = '';
        $this->descricao        = '';
        $this->gatilho          = '';
        $this->ativo            = true;
        $this->gatilhoConfigJson = '{}';
        $this->condicoes        = [];
        $this->acoes            = [];
        $this->resetValidation();
    }

    // ── Render ────────────────────────────────────────────────────

    public function render()
    {
        $query = WorkflowRegra::with('acoes')
            ->when($this->busca, fn($q) => $q->where('nome', 'ilike', "%{$this->busca}%"))
            ->when($this->filtroGatilho, fn($q) => $q->where('gatilho', $this->filtroGatilho))
            ->orderByDesc('ativo')
            ->orderBy('nome');

        $regras = $query->paginate(12);

        $historico = null;
        if ($this->aba === 'historico') {
            $historico = WorkflowExecucao::with(['regra'])
                ->latest('created_at')
                ->paginate(20);
        }

        $totalRegras  = WorkflowRegra::count();
        $totalAtivas  = WorkflowRegra::where('ativo', true)->count();
        $totalExecucoes = WorkflowExecucao::count();
        $errosHoje    = WorkflowExecucao::where('status', WorkflowExecucao::STATUS_ERRO)
            ->whereDate('created_at', today())->count();

        $gatilhos = WorkflowRegra::gatilhosDisponiveis();
        $acoesTipos = WorkflowRegra::acoesDisponiveis();

        $campos = [
            'andamento.descricao'   => 'Andamento: Descrição',
            'processo.status'       => 'Processo: Status',
            'processo.score'        => 'Processo: Score',
            'processo.numero'       => 'Processo: Número',
            'processo.tipo_acao_id' => 'Processo: Tipo de Ação',
            'processo.fase_id'      => 'Processo: Fase',
            'processo.advogado_id'  => 'Processo: Advogado',
        ];

        $operadores = [
            'igual'      => 'é igual a',
            'diferente'  => 'é diferente de',
            'contem'     => 'contém',
            'nao_contem' => 'não contém',
            'maior_que'  => 'maior que',
            'menor_que'  => 'menor que',
            'vazio'      => 'está vazio',
            'nao_vazio'  => 'não está vazio',
        ];

        return view('livewire.workflow-regras', compact(
            'regras', 'historico',
            'totalRegras', 'totalAtivas', 'totalExecucoes', 'errosHoje',
            'gatilhos', 'acoesTipos', 'campos', 'operadores'
        ))->extends('layouts.app')->section('content');
    }
}
