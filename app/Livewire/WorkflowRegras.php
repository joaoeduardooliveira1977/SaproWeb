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

    /** Estrutura padrão de uma ação visual (sem JSON exposto) */
    private function acaoVazia(): array
    {
        return [
            'tipo'                => '',
            'config_json'         => '{}',
            'wpp_destinatario'    => 'advogado_processo',
            'wpp_mensagem'        => '',
            'prazo_titulo'        => '',
            'prazo_dias'          => 15,
            'prazo_tipo_contagem' => 'uteis',
            'prazo_responsavel'   => 'advogado_processo',
            'notif_titulo'        => '',
            'notif_mensagem'      => '',
            'notif_destinatario'  => 'advogado_processo',
            'agenda_titulo'       => '',
            'agenda_dias'         => 1,
            'agenda_hora'         => '09:00',
            'agenda_urgente'      => false,
            'score_valor'         => 'auto',
        ];
    }

    public function novaRegra(): void
    {
        $this->resetForm();
        $this->acoes = [$this->acaoVazia()];
        $this->modal = true;
    }

    public function criarModelo(string $modelo): void
    {
        $this->resetForm();
        $this->ativo = true;

        match ($modelo) {
            'intimacao' => $this->modeloIntimacao(),
            'sem_andamento' => $this->modeloSemAndamento(),
            'prazo_vencendo' => $this->modeloPrazoVencendo(),
            default => $this->acoes = [$this->acaoVazia()],
        };

        $this->modal = true;
    }

    private function modeloIntimacao(): void
    {
        $this->nome = 'Intimação recebida';
        $this->descricao = 'Quando um novo andamento mencionar intimação, criar prazo, avisar o advogado e gerar resumo com IA.';
        $this->gatilho = WorkflowRegra::GATILHO_ANDAMENTO_CRIADO;
        $this->condicoes = [
            ['campo' => 'andamento.descricao', 'op' => 'contem', 'valor' => 'intimação'],
        ];
        $this->acoes = [
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_CRIAR_PRAZO,
                'prazo_titulo' => 'Analisar intimação — {numero}',
                'prazo_dias' => 5,
                'prazo_tipo_contagem' => 'uteis',
                'prazo_responsavel' => 'advogado_processo',
            ]),
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_ENVIAR_WHATSAPP,
                'wpp_destinatario' => 'advogado_processo',
                'wpp_mensagem' => 'Novo andamento com possível intimação no processo {numero} do cliente {cliente}: {andamento}',
            ]),
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_CHAMAR_IA,
            ]),
        ];
    }

    private function modeloSemAndamento(): void
    {
        $this->nome = 'Processo sem andamento';
        $this->descricao = 'Avisar o responsável quando um processo ficar muitos dias sem movimentação.';
        $this->gatilho = WorkflowRegra::GATILHO_SEM_ANDAMENTO_DIAS;
        $this->gatilhoConfigJson = json_encode(['dias' => 30], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->acoes = [
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_CRIAR_NOTIFICACAO,
                'notif_titulo' => 'Processo sem andamento',
                'notif_mensagem' => 'O processo {numero} está sem andamento há 30 dias. Verifique se há providência pendente.',
                'notif_destinatario' => 'advogado_processo',
            ]),
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_CRIAR_AGENDA,
                'agenda_titulo' => 'Revisar processo sem andamento — {numero}',
                'agenda_dias' => 1,
                'agenda_hora' => '09:00',
                'agenda_urgente' => false,
            ]),
        ];
    }

    private function modeloPrazoVencendo(): void
    {
        $this->nome = 'Prazo vencendo';
        $this->descricao = 'Avisar o responsável quando um prazo estiver próximo do vencimento.';
        $this->gatilho = WorkflowRegra::GATILHO_PRAZO_VENCENDO;
        $this->acoes = [
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_CRIAR_NOTIFICACAO,
                'notif_titulo' => 'Prazo vencendo',
                'notif_mensagem' => 'O processo {numero} possui prazo próximo do vencimento. Revise a providência necessária.',
                'notif_destinatario' => 'advogado_processo',
            ]),
            array_merge($this->acaoVazia(), [
                'tipo' => WorkflowRegra::ACAO_ENVIAR_WHATSAPP,
                'wpp_destinatario' => 'advogado_processo',
                'wpp_mensagem' => 'Atenção: prazo vencendo no processo {numero} do cliente {cliente}.',
            ]),
        ];
    }

    public function editarRegra(int $id): void
    {
        $regra = WorkflowRegra::with('acoes')->findOrFail($id);

        $this->editandoId        = $id;
        $this->nome              = $regra->nome;
        $this->descricao         = $regra->descricao ?? '';
        $this->gatilho           = $regra->gatilho;
        $this->ativo             = $regra->ativo;
        $this->gatilhoConfigJson = json_encode($regra->gatilho_config ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $this->condicoes         = $regra->condicoes ?? [];

        // Hidratar campos visuais a partir do JSON salvo
        $this->acoes = $regra->acoes->map(function ($a) {
            $c = $a->config ?? [];
            return [
                'tipo'                => $a->tipo,
                'config_json'         => json_encode($c),
                'wpp_destinatario'    => $c['destinatario']   ?? 'advogado_processo',
                'wpp_mensagem'        => $c['mensagem']       ?? '',
                'prazo_titulo'        => $c['titulo']         ?? '',
                'prazo_dias'          => $c['dias']           ?? 15,
                'prazo_tipo_contagem' => $c['tipo_contagem']  ?? 'uteis',
                'prazo_responsavel'   => $c['responsavel']    ?? 'advogado_processo',
                'notif_titulo'        => $c['titulo']         ?? '',
                'notif_mensagem'      => $c['mensagem']       ?? '',
                'notif_destinatario'  => $c['destinatario']   ?? 'advogado_processo',
                'agenda_titulo'       => $c['titulo']         ?? '',
                'agenda_dias'         => $c['dias_a_partir']  ?? 1,
                'agenda_hora'         => $c['hora']           ?? '09:00',
                'agenda_urgente'      => $c['urgente']        ?? false,
                'score_valor'         => $c['score']          ?? 'auto',
            ];
        })->toArray();

        if (empty($this->acoes)) {
            $this->acoes = [$this->acaoVazia()];
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
            $dados['tenant_id']       = Auth::guard('usuarios')->user()?->tenant_id;
            $dados['execucoes_total'] = 0;
            $regra = WorkflowRegra::create($dados);
        }

        // Converter campos visuais → JSON e recriar ações na ordem
        foreach ($this->acoes as $index => $acaoForm) {
            $config = [];
            switch ($acaoForm['tipo']) {
                case 'enviar_whatsapp':
                    $config = [
                        'destinatario' => $acaoForm['wpp_destinatario'] ?? 'advogado_processo',
                        'mensagem'     => $acaoForm['wpp_mensagem']     ?? '',
                        'canal'        => 'whatsapp',
                    ];
                    break;
                case 'criar_prazo':
                    $config = [
                        'titulo'        => $acaoForm['prazo_titulo']        ?? 'Prazo — {andamento_descricao}',
                        'dias'          => (int) ($acaoForm['prazo_dias']   ?? 15),
                        'tipo_contagem' => $acaoForm['prazo_tipo_contagem'] ?? 'uteis',
                        'responsavel'   => $acaoForm['prazo_responsavel']   ?? 'advogado_processo',
                        'prazo_fatal'   => false,
                    ];
                    break;
                case 'criar_notificacao':
                    $config = [
                        'tipo'         => 'alerta',
                        'titulo'       => $acaoForm['notif_titulo']       ?? 'Atenção: {andamento_descricao}',
                        'mensagem'     => $acaoForm['notif_mensagem']     ?? '',
                        'destinatario' => $acaoForm['notif_destinatario'] ?? 'advogado_processo',
                    ];
                    break;
                case 'criar_agenda':
                    $config = [
                        'titulo'        => $acaoForm['agenda_titulo']   ?? 'Compromisso — {numero}',
                        'dias_a_partir' => (int) ($acaoForm['agenda_dias'] ?? 1),
                        'hora'          => $acaoForm['agenda_hora']     ?? '09:00',
                        'urgente'       => (bool) ($acaoForm['agenda_urgente'] ?? false),
                        'responsavel'   => 'advogado_processo',
                    ];
                    break;
                case 'atualizar_score':
                    $config = ['score' => $acaoForm['score_valor'] ?? 'auto'];
                    break;
                case 'chamar_ia':
                    $config = ['tipo' => 'resumo_andamento', 'salvar_em' => 'andamento.resumo_ia'];
                    break;
                default:
                    try {
                        $config = json_decode($acaoForm['config_json'] ?? '{}', true) ?? [];
                    } catch (\Throwable) {}
            }

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
        $this->acoes[] = $this->acaoVazia();
    }

    public function removerAcao(int $index): void
    {
        array_splice($this->acoes, $index, 1);
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
