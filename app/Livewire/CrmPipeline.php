<?php

namespace App\Livewire;

use App\Models\{CrmAtividade, CrmOportunidade, Pessoa, Usuario};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CrmPipeline extends Component
{
    public string $aba = 'pipeline'; // pipeline | lista | atividades

    // Filtros gerais
    public string $filtroResponsavel = '';
    public string $filtroOrigem      = '';
    public string $filtroEtapa       = '';
    public string $filtroBusca       = '';

    // Modal oportunidade
    public bool    $modalOp      = false;
    public ?int    $opId         = null;
    public string  $opNome       = '';
    public string  $opTelefone   = '';
    public string  $opEmail      = '';
    public string  $opCpfCnpj    = '';
    public string  $opOrigem     = 'indicacao';
    public string  $opTitulo     = '';
    public string  $opArea       = '';
    public string  $opEtapa      = 'novo_contato';
    public string  $opValor      = '';
    public string  $opResponsavel= '';
    public string  $opPrevisao   = '';
    public string  $opDescricao  = '';
    public string  $opMotivo     = '';
    public bool    $modalPerda   = false;

    // Atividades dentro do modal
    public string $atTipo        = 'tarefa';
    public string $atDescricao   = '';
    public string $atData        = '';

    protected $queryString = [
        'aba'              => ['except' => 'pipeline'],
        'filtroResponsavel'=> ['except' => ''],
        'filtroOrigem'     => ['except' => ''],
        'filtroEtapa'      => ['except' => ''],
        'filtroBusca'      => ['except' => ''],
    ];

    protected function rules(): array
    {
        return [
            'opNome'      => 'required|min:2',
            'opTelefone'  => 'nullable',
            'opEmail'     => 'nullable|email',
            'opOrigem'    => 'required',
            'opEtapa'     => 'required',
        ];
    }

    protected $messages = [
        'opNome.required' => 'O nome é obrigatório.',
        'opNome.min'      => 'Nome muito curto.',
        'opEmail.email'   => 'E-mail inválido.',
    ];

    // ── Modal abrir/fechar ────────────────────────────────────

    public function novaOportunidade(string $etapa = 'novo_contato'): void
    {
        $this->resetOp();
        $this->opEtapa = $etapa;
        $this->modalOp = true;
    }

    public function editarOportunidade(int $id): void
    {
        $op = CrmOportunidade::find($id);
        if (! $op) return;

        $this->opId         = $op->id;
        $this->opNome       = $op->nome;
        $this->opTelefone   = $op->telefone ?? '';
        $this->opEmail      = $op->email    ?? '';
        $this->opCpfCnpj    = $op->cpf_cnpj ?? '';
        $this->opOrigem     = $op->origem;
        $this->opTitulo     = $op->titulo    ?? '';
        $this->opArea       = $op->area_direito ?? '';
        $this->opEtapa      = $op->etapa;
        $this->opValor      = $op->valor_estimado ? number_format($op->valor_estimado, 2, ',', '.') : '';
        $this->opResponsavel= $op->responsavel_id ?? '';
        $this->opPrevisao   = $op->data_previsao?->format('Y-m-d') ?? '';
        $this->opDescricao  = $op->descricao ?? '';
        $this->opMotivo     = $op->motivo_perda ?? '';
        $this->atTipo       = 'tarefa';
        $this->atDescricao  = '';
        $this->atData       = '';
        $this->modalOp      = true;
    }

    public function fecharModal(): void
    {
        $this->modalOp    = false;
        $this->modalPerda = false;
        $this->resetOp();
    }

    private function resetOp(): void
    {
        $this->opId = null;
        $this->opResponsavel = '';
        $this->opNome = $this->opTelefone = $this->opEmail = $this->opCpfCnpj = '';
        $this->opOrigem = 'indicacao';
        $this->opTitulo = $this->opArea = $this->opValor = '';
        $this->opEtapa = 'novo_contato';
        $this->opPrevisao = $this->opDescricao = $this->opMotivo = '';
        $this->atTipo = 'tarefa';
        $this->atDescricao = $this->atData = '';
        $this->resetValidation();
    }

    // ── CRUD Oportunidade ─────────────────────────────────────

    public function salvarOportunidade(): void
    {
        $this->validate();

        $valor = $this->opValor
            ? (float) str_replace(['.', ','], ['', '.'], $this->opValor)
            : null;

        $dados = [
            'nome'           => $this->opNome,
            'telefone'       => $this->opTelefone ?: null,
            'email'          => $this->opEmail    ?: null,
            'cpf_cnpj'       => $this->opCpfCnpj  ?: null,
            'origem'         => $this->opOrigem,
            'titulo'         => $this->opTitulo   ?: null,
            'area_direito'   => $this->opArea      ?: null,
            'etapa'          => $this->opEtapa,
            'valor_estimado' => $valor,
            'responsavel_id' => $this->opResponsavel ?: null,
            'data_previsao'  => $this->opPrevisao  ?: null,
            'descricao'      => $this->opDescricao ?: null,
        ];

        if ($this->opId) {
            CrmOportunidade::find($this->opId)?->update($dados);
            $msg = 'Oportunidade atualizada.';
        } else {
            $dados['usuario_id'] = auth('usuarios')->id();
            $dados['data_fechamento'] = in_array($this->opEtapa, ['ganho','perdido']) ? today() : null;
            CrmOportunidade::create($dados);
            $msg = 'Oportunidade criada.';
        }

        $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: $msg);
    }

    public function excluirOportunidade(int $id): void
    {
        CrmOportunidade::destroy($id);
        if ($this->opId === $id) $this->fecharModal();
        $this->dispatch('toast', tipo: 'success', msg: 'Oportunidade excluída.');
    }

    // ── Mudança de etapa rápida ───────────────────────────────

    public function moverEtapa(string $etapa): void
    {
        // Sempre atualiza a propriedade local (funciona para nova e existente)
        $this->opEtapa = $etapa;

        if ($etapa === 'perdido') {
            $this->opMotivo   = '';
            $this->modalPerda = true;
            return;
        }

        // Persiste no banco apenas se estiver editando uma existente
        if ($this->opId) {
            $op = CrmOportunidade::find($this->opId);
            if ($op) {
                $op->update([
                    'etapa'           => $etapa,
                    'data_fechamento' => in_array($etapa, ['ganho', 'perdido']) ? today() : null,
                ]);
            }
            $this->dispatch('toast', tipo: 'success', msg: 'Etapa atualizada.');
        }
    }

    public function confirmarPerda(): void
    {
        $op = CrmOportunidade::find($this->opId);
        if ($op) {
            $op->update([
                'etapa'           => 'perdido',
                'motivo_perda'    => $this->opMotivo ?: null,
                'data_fechamento' => today(),
            ]);
            if ($this->modalOp) $this->opEtapa = 'perdido';
        }
        $this->modalPerda = false;
        $this->dispatch('toast', tipo: 'info', msg: 'Oportunidade marcada como perdida.');
    }

    // ── Atividades ────────────────────────────────────────────

    public function salvarAtividade(): void
    {
        if (! $this->opId || ! $this->atDescricao) return;

        CrmAtividade::create([
            'oportunidade_id' => $this->opId,
            'tipo'            => $this->atTipo,
            'descricao'       => $this->atDescricao,
            'data_prevista'   => $this->atData ?: null,
            'usuario_id'      => auth('usuarios')->id(),
        ]);

        $this->atDescricao = '';
        $this->atData      = '';
        $this->dispatch('toast', tipo: 'success', msg: 'Atividade registrada.');
    }

    public function concluirAtividade(int $id): void
    {
        CrmAtividade::where('id', $id)->update([
            'concluida'      => true,
            'data_realizada' => today(),
        ]);
        $this->dispatch('toast', tipo: 'success', msg: 'Atividade concluída.');
    }

    public function excluirAtividade(int $id): void
    {
        CrmAtividade::destroy($id);
    }

    // ── Conversão em cliente ──────────────────────────────────

    public function converterCliente(int $id): void
    {
        $op = CrmOportunidade::find($id);
        if (! $op || $op->convertido) return;

        // Cria ou localiza pessoa pelo e-mail/cpf
        $pessoa = null;

        if ($op->email) {
            $pessoa = Pessoa::where('email', $op->email)->first();
        }
        if (! $pessoa && $op->cpf_cnpj) {
            $pessoa = Pessoa::where('cpf_cnpj', $op->cpf_cnpj)->first();
        }

        if (! $pessoa) {
            $pessoa = Pessoa::create([
                'nome'     => $op->nome,
                'telefone' => $op->telefone,
                'email'    => $op->email,
                'cpf_cnpj' => $op->cpf_cnpj,
                'ativo'    => true,
            ]);
        }

        $pessoa->sincronizarTipos(
            array_unique(array_merge($pessoa->listaTipos(), ['Cliente']))
        );

        $op->update([
            'convertido' => true,
            'pessoa_id'  => $pessoa->id,
            'etapa'      => 'ganho',
            'data_fechamento' => today(),
        ]);

        if ($this->opId === $id) {
            $this->opEtapa = 'ganho';
        }

        $this->dispatch('toast', tipo: 'success',
            msg: "Lead convertido! {$op->nome} cadastrado como cliente.");
    }

    // ── Render ────────────────────────────────────────────────

    public function render()
    {
        $etapasAtivas = ['novo_contato', 'qualificacao', 'reuniao', 'proposta', 'negociacao'];

        // KPIs
        $totalAtivas   = CrmOportunidade::whereIn('etapa', $etapasAtivas)->count();
        $ganhasmes     = CrmOportunidade::where('etapa', 'ganho')
            ->where('data_fechamento', '>=', today()->startOfMonth())->count();
        $perdidasmes   = CrmOportunidade::where('etapa', 'perdido')
            ->where('data_fechamento', '>=', today()->startOfMonth())->count();
        $valorPipeline = CrmOportunidade::whereIn('etapa', $etapasAtivas)
            ->sum('valor_estimado');

        // Kanban: oportunidades agrupadas por etapa
        $kanban = [];
        foreach ($etapasAtivas as $etapa) {
            $q = CrmOportunidade::where('etapa', $etapa)
                ->with('responsavel');

            if ($this->filtroResponsavel) {
                $q->where('responsavel_id', $this->filtroResponsavel);
            }
            if ($this->filtroOrigem) {
                $q->where('origem', $this->filtroOrigem);
            }

            $kanban[$etapa] = $q->latest()->get();
        }

        // Lista
        $lista = collect();
        if ($this->aba === 'lista') {
            $q = CrmOportunidade::with('responsavel')->latest();

            if ($this->filtroEtapa)       $q->where('etapa', $this->filtroEtapa);
            if ($this->filtroResponsavel) $q->where('responsavel_id', $this->filtroResponsavel);
            if ($this->filtroOrigem)      $q->where('origem', $this->filtroOrigem);
            if ($this->filtroBusca) {
                $termo = $this->filtroBusca;
                $q->where(fn($s) => $s
                    ->where('nome', 'ilike', "%{$termo}%")
                    ->orWhere('email', 'ilike', "%{$termo}%")
                    ->orWhere('telefone', 'ilike', "%{$termo}%")
                );
            }

            $lista = $q->limit(100)->get();
        }

        // Atividades pendentes
        $atividades = collect();
        if ($this->aba === 'atividades') {
            $q = CrmAtividade::with('oportunidade')
                ->where('concluida', false)
                ->orderBy('data_prevista');

            if ($this->filtroResponsavel) {
                $q->whereHas('oportunidade', fn($s) =>
                    $s->where('responsavel_id', $this->filtroResponsavel)
                );
            }

            $atividades = $q->limit(100)->get();
        }

        // Atividades do modal
        $opAtividades = collect();
        $opDados = null;
        if ($this->modalOp && $this->opId) {
            $opDados     = CrmOportunidade::find($this->opId);
            $opAtividades = CrmAtividade::where('oportunidade_id', $this->opId)
                ->orderByDesc('created_at')
                ->get();
        }

        $usuarios = Usuario::orderBy('nome')->get();

        return view('livewire.crm-pipeline', compact(
            'kanban', 'lista', 'atividades',
            'totalAtivas', 'ganhasmes', 'perdidasmes', 'valorPipeline',
            'opAtividades', 'opDados', 'usuarios',
        ));
    }
}
