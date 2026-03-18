<?php

namespace App\Livewire;

use App\Models\Procuracao;
use App\Models\Pessoa;
use App\Models\Processo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class Procuracoes extends Component
{
    use WithPagination, WithFileUploads;

    public string $busca = '';

    // Formulário
    public bool   $mostrarForm  = false;
    public ?int   $editandoId   = null;
    public ?int   $cliente_id   = null;
    public string $clienteBusca = '';
    public string $clienteNome  = '';
    public array  $clienteSugs  = [];
    public ?int   $processo_id  = null;
    public string $processoBusca = '';
    public array  $processoSugs  = [];
    public string $tipo          = 'ad judicia';
    public string $data_emissao  = '';
    public string $data_validade = '';
    public string $poderes       = '';
    public string $observacoes   = '';
    public bool   $ativa         = true;
    public $arquivo = null;

    // Confirmação exclusão
    public ?int $excluindoId = null;

    protected function rules(): array
    {
        return [
            'cliente_id'    => 'required|integer',
            'tipo'          => 'required|string|max:60',
            'data_emissao'  => 'required|date',
            'data_validade' => 'nullable|date|after_or_equal:data_emissao',
            'arquivo'       => 'nullable|file|max:20480',
        ];
    }

    public function placeholder(): \Illuminate\View\View
    {
        return view('livewire.partials.skeleton', ['cards' => 0, 'blocks' => 1, 'blockHeight' => 300]);
    }

    // ── Autocomplete cliente ──────────────────────────────────────────

    public function updatedClienteBusca(): void
    {
        if (strlen($this->clienteBusca) < 2) {
            $this->clienteSugs = [];
            return;
        }
        $this->clienteSugs = Pessoa::where('nome', 'ilike', "%{$this->clienteBusca}%")
            ->orderBy('nome')->limit(10)->get(['id', 'nome'])->toArray();
    }

    public function selecionarCliente(int $id, string $nome): void
    {
        $this->cliente_id   = $id;
        $this->clienteNome  = $nome;
        $this->clienteBusca = $nome;
        $this->clienteSugs  = [];
    }

    // ── Autocomplete processo ─────────────────────────────────────────

    public function updatedProcessoBusca(): void
    {
        if (strlen($this->processoBusca) < 2) {
            $this->processoSugs = [];
            return;
        }
        $this->processoSugs = Processo::where('numero', 'ilike', "%{$this->processoBusca}%")
            ->where('status', 'Ativo')->orderBy('numero')->limit(10)->get(['id', 'numero'])->toArray();
    }

    public function selecionarProcesso(int $id, string $numero): void
    {
        $this->processo_id   = $id;
        $this->processoBusca = $numero;
        $this->processoSugs  = [];
    }

    // ── CRUD ──────────────────────────────────────────────────────────

    public function novo(): void
    {
        $this->resetForm();
        $this->mostrarForm = true;
    }

    public function editar(int $id): void
    {
        $p = Procuracao::with('cliente', 'processo')->findOrFail($id);
        $this->editandoId   = $id;
        $this->cliente_id   = $p->cliente_id;
        $this->clienteNome  = $p->cliente?->nome ?? '';
        $this->clienteBusca = $p->cliente?->nome ?? '';
        $this->processo_id  = $p->processo_id;
        $this->processoBusca= $p->processo?->numero ?? '';
        $this->tipo         = $p->tipo;
        $this->data_emissao = $p->data_emissao->format('Y-m-d');
        $this->data_validade= $p->data_validade?->format('Y-m-d') ?? '';
        $this->poderes      = $p->poderes ?? '';
        $this->observacoes  = $p->observacoes ?? '';
        $this->ativa        = $p->ativa;
        $this->mostrarForm  = true;
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'cliente_id'    => $this->cliente_id,
            'processo_id'   => $this->processo_id ?: null,
            'tipo'          => $this->tipo,
            'data_emissao'  => $this->data_emissao,
            'data_validade' => $this->data_validade ?: null,
            'poderes'       => $this->poderes ?: null,
            'observacoes'   => $this->observacoes ?: null,
            'ativa'         => $this->ativa,
        ];

        if ($this->editandoId) {
            $proc = Procuracao::findOrFail($this->editandoId);
            if ($this->arquivo) {
                if ($proc->arquivo) Storage::disk('public')->delete($proc->arquivo);
                $dados['arquivo'] = $this->arquivo->store('procuracoes', 'public');
            }
            $proc->update($dados);
        } else {
            if ($this->arquivo) {
                $dados['arquivo'] = $this->arquivo->store('procuracoes', 'public');
            }
            Procuracao::create($dados);
        }

        $this->resetForm();
        $this->mostrarForm = false;
        $this->dispatch('toast', message: 'Procuração salva com sucesso!', type: 'success');
    }

    public function confirmarExclusao(int $id): void
    {
        $this->excluindoId = $id;
    }

    public function excluir(): void
    {
        if ($this->excluindoId) {
            $p = Procuracao::findOrFail($this->excluindoId);
            if ($p->arquivo) Storage::disk('public')->delete($p->arquivo);
            $p->delete();
            $this->excluindoId = null;
            $this->dispatch('toast', message: 'Procuração excluída.', type: 'success');
        }
    }

    public function cancelar(): void
    {
        $this->resetForm();
        $this->mostrarForm = false;
        $this->excluindoId = null;
    }

    private function resetForm(): void
    {
        $this->editandoId    = null;
        $this->cliente_id    = null;
        $this->clienteNome   = '';
        $this->clienteBusca  = '';
        $this->clienteSugs   = [];
        $this->processo_id   = null;
        $this->processoBusca = '';
        $this->processoSugs  = [];
        $this->tipo          = 'ad judicia';
        $this->data_emissao  = now()->format('Y-m-d');
        $this->data_validade = '';
        $this->poderes       = '';
        $this->observacoes   = '';
        $this->ativa         = true;
        $this->arquivo       = null;
    }

    public function updatedBusca(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $procuracoes = Procuracao::with(['cliente', 'processo'])
            ->when($this->busca, fn($q) => $q->whereHas('cliente', fn($c) =>
                $c->where('nome', 'ilike', "%{$this->busca}%")))
            ->orderByDesc('data_emissao')
            ->paginate(15);

        // Alerta de vencimento
        $vencendoEm30 = Procuracao::where('ativa', true)
            ->whereNotNull('data_validade')
            ->where('data_validade', '>=', today())
            ->where('data_validade', '<=', today()->addDays(30))
            ->count();

        $vencidas = Procuracao::where('ativa', true)
            ->whereNotNull('data_validade')
            ->where('data_validade', '<', today())
            ->count();

        return view('livewire.procuracoes', compact('procuracoes', 'vencendoEm30', 'vencidas'));
    }
}
