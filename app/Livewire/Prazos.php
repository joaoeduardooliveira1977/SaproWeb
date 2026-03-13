<?php

namespace App\Livewire;

use App\Models\{Prazo, Processo, Usuario};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Prazos extends Component
{
    use WithPagination;

    // ── Filtros ──────────────────────────────────────────────────
    public string $filtroStatus      = 'aberto';
    public string $filtroProcesso    = '';
    public string $filtroResponsavel = '';
    public string $filtroTipo        = '';

    // ── Modal ────────────────────────────────────────────────────
    public bool   $modalAberto = false;
    public ?int   $prazoid     = null;

    // ── Campos do formulário ─────────────────────────────────────
    public string $titulo         = '';
    public string $descricao      = '';
    public string $tipo           = 'Prazo';
    public string $data_inicio    = '';
    public string $tipo_contagem  = 'corridos';
    public string $dias           = '';
    public string $data_prazo     = '';
    public bool   $prazo_fatal    = false;
    public string $processo_id    = '';
    public string $responsavel_id = '';
    public string $observacoes    = '';

    // ── Confirmação exclusão ─────────────────────────────────────
    public ?int $confirmarExcluir = null;

    protected function rules(): array
    {
        return [
            'titulo'        => 'required|string|max:200',
            'tipo'          => 'required|string',
            'data_inicio'   => 'required|date',
            'data_prazo'    => 'required|date',
            'tipo_contagem' => 'required|in:corridos,uteis',
            'dias'          => 'nullable|integer|min:0',
            'processo_id'   => 'nullable|exists:processos,id',
            'responsavel_id'=> 'nullable|exists:usuarios,id',
        ];
    }

    protected $messages = [
        'titulo.required'     => 'Informe o título do prazo.',
        'data_inicio.required'=> 'Informe a data de início.',
        'data_prazo.required' => 'Informe a data do prazo.',
    ];

    public function mount(): void
    {
        $this->data_inicio = today()->format('Y-m-d');
    }

    // ── Cálculo automático ───────────────────────────────────────

    public function updatedDias(): void
    {
        $this->recalcularPrazo();
    }

    public function updatedDataInicio(): void
    {
        $this->recalcularPrazo();
    }

    public function updatedTipoContagem(): void
    {
        $this->recalcularPrazo();
    }

    private function recalcularPrazo(): void
    {
        $dias = (int) $this->dias;
        if ($dias > 0 && $this->data_inicio) {
            $this->data_prazo = Prazo::calcularData(
                $this->data_inicio,
                $dias,
                $this->tipo_contagem
            )->format('Y-m-d');
        }
    }

    // ── Modal ────────────────────────────────────────────────────

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->prazoid = $id;
        $this->modalAberto = true;

        if ($id) {
            $p = Prazo::findOrFail($id);
            $this->titulo         = $p->titulo;
            $this->descricao      = $p->descricao ?? '';
            $this->tipo           = $p->tipo;
            $this->data_inicio    = $p->data_inicio->format('Y-m-d');
            $this->tipo_contagem  = $p->tipo_contagem;
            $this->dias           = (string) ($p->dias ?? '');
            $this->data_prazo     = $p->data_prazo->format('Y-m-d');
            $this->prazo_fatal    = $p->prazo_fatal;
            $this->processo_id    = (string) ($p->processo_id ?? '');
            $this->responsavel_id = (string) ($p->responsavel_id ?? '');
            $this->observacoes    = $p->observacoes ?? '';
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto    = false;
        $this->confirmarExcluir = null;
        $this->resetForm();
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'titulo'         => trim($this->titulo),
            'descricao'      => trim($this->descricao) ?: null,
            'tipo'           => $this->tipo,
            'data_inicio'    => $this->data_inicio,
            'tipo_contagem'  => $this->tipo_contagem,
            'dias'           => $this->dias !== '' ? (int) $this->dias : null,
            'data_prazo'     => $this->data_prazo,
            'prazo_fatal'    => $this->prazo_fatal,
            'processo_id'    => $this->processo_id ?: null,
            'responsavel_id' => $this->responsavel_id ?: null,
            'observacoes'    => trim($this->observacoes) ?: null,
            'criado_por'     => Auth::id(),
        ];

        if ($this->prazoid) {
            Prazo::findOrFail($this->prazoid)->update($dados);
            session()->flash('sucesso', 'Prazo atualizado.');
        } else {
            $dados['status'] = 'aberto';
            Prazo::create($dados);
            session()->flash('sucesso', 'Prazo cadastrado.');
        }

        $this->fecharModal();
    }

    public function marcarCumprido(int $id): void
    {
        Prazo::findOrFail($id)->update([
            'status'           => 'cumprido',
            'data_cumprimento' => today(),
        ]);
        session()->flash('sucesso', 'Prazo marcado como cumprido.');
    }

    public function marcarPerdido(int $id): void
    {
        Prazo::findOrFail($id)->update(['status' => 'perdido']);
        session()->flash('sucesso', 'Prazo marcado como perdido.');
    }

    public function reabrir(int $id): void
    {
        Prazo::findOrFail($id)->update([
            'status'           => 'aberto',
            'data_cumprimento' => null,
        ]);
    }

    public function confirmarExcluirPrazo(int $id): void
    {
        $this->confirmarExcluir = $id;
    }

    public function excluir(): void
    {
        if ($this->confirmarExcluir) {
            Prazo::findOrFail($this->confirmarExcluir)->delete();
            $this->confirmarExcluir = null;
            session()->flash('sucesso', 'Prazo removido.');
        }
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->prazoid = null;
        $this->titulo  = $this->descricao = $this->dias = $this->data_prazo = '';
        $this->observacoes = $this->processo_id = $this->responsavel_id = '';
        $this->tipo           = 'Prazo';
        $this->tipo_contagem  = 'corridos';
        $this->data_inicio    = today()->format('Y-m-d');
        $this->prazo_fatal    = false;
        $this->resetErrorBag();
    }

    // ── Render ───────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $q = Prazo::with(['processo.cliente', 'responsavel'])
            ->orderByRaw("CASE status WHEN 'aberto' THEN 0 WHEN 'perdido' THEN 1 WHEN 'cumprido' THEN 2 END")
            ->orderBy('data_prazo');

        if ($this->filtroStatus !== 'todos') {
            $q->where('status', $this->filtroStatus);
        }

        if ($this->filtroProcesso) {
            $q->where('processo_id', $this->filtroProcesso);
        }

        if ($this->filtroResponsavel) {
            $q->where('responsavel_id', $this->filtroResponsavel);
        }

        if ($this->filtroTipo) {
            $q->where('tipo', $this->filtroTipo);
        }

        $prazos      = $q->paginate(25);
        $processos   = Processo::where('status', 'Ativo')->orderBy('numero')->get();
        $usuarios    = Usuario::where('ativo', true)->orderBy('nome')->get();

        // KPIs
        $totalAbertos  = Prazo::where('status', 'aberto')->count();
        $vencendoHoje  = Prazo::where('status', 'aberto')->whereDate('data_prazo', today())->count();
        $vencidos      = Prazo::where('status', 'aberto')->whereDate('data_prazo', '<', today())->count();
        $fatais        = Prazo::where('status', 'aberto')->where('prazo_fatal', true)
                              ->whereDate('data_prazo', '>=', today())
                              ->whereDate('data_prazo', '<=', today()->addDays(5))
                              ->count();

        return view('livewire.prazos', compact(
            'prazos', 'processos', 'usuarios',
            'totalAbertos', 'vencendoHoje', 'vencidos', 'fatais'
        ));
    }
}
