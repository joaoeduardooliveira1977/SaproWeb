<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Indicador;
use Illuminate\Support\Facades\Auth;

class Indicadores extends Component
{
    use WithPagination;

    public string $busca = '';

    // Formulário
    public bool   $modalAberto  = false;
    public ?int   $indicadorId  = null;
    public string $nome              = '';
    public string $email             = '';
    public string $celular           = '';
    public string $cpf               = '';
    public string $percentualComissao = '';
    public string $observacoes       = '';
    public bool   $ativo             = true;

    protected $queryString = ['busca' => ['except' => '']];

    protected function rules(): array
    {
        return [
            'nome'              => 'required|string|max:200',
            'email'             => 'nullable|email|max:200',
            'celular'           => 'nullable|string|max:20',
            'cpf'               => 'nullable|string|max:20',
            'percentualComissao'=> 'required|numeric|min:0|max:100',
            'observacoes'       => 'nullable|string',
        ];
    }

    protected $messages = [
        'nome.required'               => 'O nome é obrigatório.',
        'percentualComissao.required' => 'Informe o percentual de comissão.',
        'percentualComissao.numeric'  => 'Percentual inválido.',
    ];

    public function updatingBusca(): void { $this->resetPage(); }

    public function abrirModal(?int $id = null): void
    {
        $this->limpar();
        $this->indicadorId = $id;
        $this->modalAberto = true;

        if ($id) {
            $ind = Indicador::findOrFail($id);
            $this->nome               = $ind->nome;
            $this->email              = $ind->email ?? '';
            $this->celular            = $ind->celular ?? '';
            $this->cpf                = $ind->cpf ?? '';
            $this->percentualComissao = number_format((float) $ind->percentual_comissao, 2, ',', '');
            $this->observacoes        = $ind->observacoes ?? '';
            $this->ativo              = $ind->ativo;
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limpar();
    }

    public function salvar(): void
    {
        $usuario = Auth::guard('usuarios')->user();
        abort_unless($usuario?->temAcao('pessoas.editar'), 403);
        $this->validate();

        $percentual = (float) str_replace(',', '.', $this->percentualComissao);

        $dados = [
            'nome'               => $this->nome,
            'email'              => $this->email ?: null,
            'celular'            => $this->celular ?: null,
            'cpf'                => $this->cpf ?: null,
            'percentual_comissao'=> $percentual,
            'observacoes'        => $this->observacoes ?: null,
            'ativo'              => $this->ativo,
        ];

        if ($this->indicadorId) {
            Indicador::findOrFail($this->indicadorId)->update($dados);
            $msg = "Indicador \"{$this->nome}\" atualizado.";
        } else {
            Indicador::create($dados);
            $msg = "Indicador \"{$this->nome}\" criado.";
        }

        $this->fecharModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function toggleAtivo(int $id): void
    {
        $ind = Indicador::findOrFail($id);
        $ind->update(['ativo' => !$ind->ativo]);
        $status = $ind->ativo ? 'ativado' : 'desativado';
        $this->dispatch('toast', message: "Indicador {$status}.", type: 'success');
    }

    private function limpar(): void
    {
        $this->indicadorId        = null;
        $this->nome               = '';
        $this->email              = '';
        $this->celular            = '';
        $this->cpf                = '';
        $this->percentualComissao = '';
        $this->observacoes        = '';
        $this->ativo              = true;
        $this->resetErrorBag();
    }

    public function render()
    {
        $indicadores = Indicador::when($this->busca, fn($q) => $q->busca($this->busca))
            ->withCount('pessoas')
            ->orderBy('nome')
            ->paginate(15);

        return view('livewire.indicadores', compact('indicadores'));
    }
}
