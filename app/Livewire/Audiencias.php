<?php

namespace App\Livewire;

use App\Models\Audiencia;
use App\Models\Pessoa;
use App\Models\Processo;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Audiencias extends Component
{
    use WithPagination;

    // ── Filtros ──────────────────────────────────────────────
    public string $filtroStatus   = '';
    public string $filtroTipo     = '';
    public string $filtroProcesso = '';
    public string $filtroBusca    = '';
    public string $filtroDataIni  = '';
    public string $filtroDataFim  = '';

    // ── Modal ────────────────────────────────────────────────
    public bool  $modalAberto   = false;
    public ?int  $audienciaId   = null;

    // ── Formulário ───────────────────────────────────────────
    public string  $processo_id          = '';
    public string  $data_hora            = '';
    public string  $tipo                 = 'outra';
    public string  $sala                 = '';
    public string  $local                = '';
    public string  $juiz_id              = '';
    public string  $advogado_id          = '';
    public string  $preposto             = '';
    public string  $pauta                = '';
    public string  $status               = 'agendada';
    public string  $resultado            = '';
    public string  $resultado_descricao  = '';
    public string  $proximo_passo        = '';
    public string  $data_proximo         = '';

    // ── Modal resultado ──────────────────────────────────────
    public bool  $modalResultado  = false;
    public ?int  $resultadoId     = null;

    protected function rules(): array
    {
        return [
            'processo_id' => 'required|exists:processos,id',
            'data_hora'   => 'required|date',
            'tipo'        => 'required|in:conciliacao,instrucao,instrucao_julgamento,julgamento,una,outra',
            'status'      => 'required|in:agendada,realizada,cancelada,redesignada',
            'resultado'   => 'nullable|in:acordo,condenacao,improcedente,extincao,nao_realizada,outra',
        ];
    }

    public function updatedFiltroStatus(): void  { $this->resetPage(); }
    public function updatedFiltroTipo(): void     { $this->resetPage(); }
    public function updatedFiltroProcesso(): void { $this->resetPage(); }
    public function updatedFiltroBusca(): void    { $this->resetPage(); }

    // ── Modal principal ──────────────────────────────────────

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();
        $this->audienciaId = $id;
        $this->modalAberto = true;

        if ($id) {
            $a = Audiencia::findOrFail($id);
            $this->processo_id         = (string) $a->processo_id;
            $this->data_hora           = $a->data_hora->format('Y-m-d\TH:i');
            $this->tipo                = $a->tipo;
            $this->sala                = $a->sala ?? '';
            $this->local               = $a->local ?? '';
            $this->juiz_id             = (string) ($a->juiz_id ?? '');
            $this->advogado_id         = (string) ($a->advogado_id ?? '');
            $this->preposto            = $a->preposto ?? '';
            $this->pauta               = $a->pauta ?? '';
            $this->status              = $a->status;
            $this->resultado           = $a->resultado ?? '';
            $this->resultado_descricao = $a->resultado_descricao ?? '';
            $this->proximo_passo       = $a->proximo_passo ?? '';
            $this->data_proximo        = $a->data_proximo?->format('Y-m-d') ?? '';
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── CRUD ─────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'processo_id'         => (int) $this->processo_id,
            'juiz_id'             => $this->juiz_id ?: null,
            'advogado_id'         => $this->advogado_id ?: null,
            'data_hora'           => $this->data_hora,
            'tipo'                => $this->tipo,
            'sala'                => $this->sala ?: null,
            'local'               => $this->local ?: null,
            'preposto'            => $this->preposto ?: null,
            'pauta'               => $this->pauta ?: null,
            'status'              => $this->status,
            'resultado'           => $this->resultado ?: null,
            'resultado_descricao' => $this->resultado_descricao ?: null,
            'proximo_passo'       => $this->proximo_passo ?: null,
            'data_proximo'        => $this->data_proximo ?: null,
        ];

        if ($this->audienciaId) {
            Audiencia::findOrFail($this->audienciaId)->update($dados);
            session()->flash('sucesso', 'Audiência atualizada.');
        } else {
            $dados['criado_por'] = Auth::id();
            Audiencia::create($dados);
            session()->flash('sucesso', 'Audiência cadastrada.');
        }

        $this->fecharModal();
    }

    public function excluir(int $id): void
    {
        Audiencia::findOrFail($id)->delete();
        session()->flash('sucesso', 'Audiência excluída.');
    }

    // ── Registrar resultado rápido ───────────────────────────

    public function abrirResultado(int $id): void
    {
        $a = Audiencia::findOrFail($id);
        $this->resultadoId          = $id;
        $this->resultado            = $a->resultado ?? '';
        $this->resultado_descricao  = $a->resultado_descricao ?? '';
        $this->proximo_passo        = $a->proximo_passo ?? '';
        $this->data_proximo         = $a->data_proximo?->format('Y-m-d') ?? '';
        $this->modalResultado       = true;
    }

    public function salvarResultado(): void
    {
        Audiencia::findOrFail($this->resultadoId)->update([
            'status'              => 'realizada',
            'resultado'           => $this->resultado ?: null,
            'resultado_descricao' => $this->resultado_descricao ?: null,
            'proximo_passo'       => $this->proximo_passo ?: null,
            'data_proximo'        => $this->data_proximo ?: null,
        ]);

        $this->modalResultado = false;
        $this->resultadoId    = null;
        session()->flash('sucesso', 'Resultado registrado.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->audienciaId = null;
        $this->processo_id = $this->sala = $this->local = '';
        $this->juiz_id = $this->advogado_id = $this->preposto = '';
        $this->pauta = $this->resultado = $this->resultado_descricao = '';
        $this->proximo_passo = $this->data_proximo = '';
        $this->tipo    = 'outra';
        $this->status  = 'agendada';
        $this->data_hora = now()->addDays(7)->format('Y-m-d\TH:i');
        $this->resetErrorBag();
    }

    // ── Render ───────────────────────────────────────────────

    public function render()
    {
        $audiencias = Audiencia::with(['processo.cliente', 'juiz', 'advogado'])
            ->when($this->filtroStatus,   fn($q) => $q->where('status', $this->filtroStatus))
            ->when($this->filtroTipo,     fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroProcesso, fn($q) => $q->where('processo_id', $this->filtroProcesso))
            ->when($this->filtroBusca,    fn($q) => $q->whereHas('processo', fn($p) =>
                $p->where('numero', 'ilike', "%{$this->filtroBusca}%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->filtroBusca}%"))
            ))
            ->when($this->filtroDataIni,  fn($q) => $q->whereDate('data_hora', '>=', $this->filtroDataIni))
            ->when($this->filtroDataFim,  fn($q) => $q->whereDate('data_hora', '<=', $this->filtroDataFim))
            ->orderByDesc('data_hora')
            ->paginate(20);

        $processos  = Processo::where('status', 'Ativo')->orderBy('numero')->get();
        $juizes     = Pessoa::doTipo('Juiz')->orderBy('nome')->get();
        $advogados  = Pessoa::doTipo('Advogado')->ativos()->orderBy('nome')->get();

        // KPIs
        $kpis = [
            'agendadas'   => Audiencia::where('status', 'agendada')->whereDate('data_hora', '>=', today())->count(),
            'hoje'        => Audiencia::where('status', 'agendada')->whereDate('data_hora', today())->count(),
            'realizadas'  => Audiencia::where('status', 'realizada')->whereMonth('data_hora', now()->month)->count(),
            'canceladas'  => Audiencia::where('status', 'cancelada')->whereMonth('data_hora', now()->month)->count(),
        ];

        return view('livewire.audiencias', compact(
            'audiencias', 'processos', 'juizes', 'advogados', 'kpis'
        ));
    }
}
