<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pessoa;
use Illuminate\Support\Facades\{Auth, DB};

class Pessoas extends Component
{
    use WithPagination;

    public string $busca = '';
    public string $tipo  = '';

    // Formulário (modal)
    public bool   $modalAberto = false;
    public ?int   $pessoaId    = null;
    public string $nome             = '';
    public string $cpf_cnpj         = '';
    public string $rg               = '';
    public string $data_nascimento  = '';
    public string $telefone         = '';
    public string $celular          = '';
    public string $email            = '';
    public string $logradouro       = '';
    public string $cidade           = '';
    public string $estado           = '';
    public string $cep              = '';
    public string $oab              = '';
    public string $observacoes      = '';
    public array  $tipos_selecionados = [];

    public const TIPOS = ['Cliente', 'Advogado', 'Juiz', 'Parte Contrária', 'Usuário'];

    protected function rules(): array
    {
        return [
            'nome'     => 'required|string|max:150',
            'cpf_cnpj' => 'nullable|string|max:18|unique:pessoas,cpf_cnpj' . ($this->pessoaId ? ",{$this->pessoaId}" : ''),
            'email'    => 'nullable|email|max:150',
            'tipos_selecionados' => 'required|array|min:1',
        ];
    }

    protected $messages = [
        'nome.required'                => 'O nome é obrigatório.',
        'tipos_selecionados.required'  => 'Selecione ao menos um tipo.',
        'tipos_selecionados.min'       => 'Selecione ao menos um tipo.',
    ];

    public function updatingBusca(): void { $this->resetPage(); }
    public function updatingTipo():  void { $this->resetPage(); }

    public function abrirModal(?int $id = null): void
    {
        $this->limparFormulario();
        $this->pessoaId    = $id;
        $this->modalAberto = true;

        if ($id) {
            $p = Pessoa::findOrFail($id);
            $this->nome            = $p->nome;
            $this->cpf_cnpj        = $p->cpf_cnpj ?? '';
            $this->rg              = $p->rg ?? '';
            $this->data_nascimento = $p->data_nascimento?->format('Y-m-d') ?? '';
            $this->telefone        = $p->telefone ?? '';
            $this->celular         = $p->celular ?? '';
            $this->email           = $p->email ?? '';
            $this->logradouro      = $p->logradouro ?? '';
            $this->cidade          = $p->cidade ?? '';
            $this->estado          = $p->estado ?? '';
            $this->cep             = $p->cep ?? '';
            $this->oab             = $p->oab ?? '';
            $this->observacoes     = $p->observacoes ?? '';
            $this->tipos_selecionados = $p->listaTipos();
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limparFormulario();
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'nome'           => $this->nome,
            'cpf_cnpj'       => $this->cpf_cnpj ?: null,
            'rg'             => $this->rg ?: null,
            'data_nascimento'=> $this->data_nascimento ?: null,
            'telefone'       => $this->telefone ?: null,
            'celular'        => $this->celular ?: null,
            'email'          => $this->email ?: null,
            'logradouro'     => $this->logradouro ?: null,
            'cidade'         => $this->cidade ?: null,
            'estado'         => $this->estado ?: null,
            'cep'            => $this->cep ?: null,
            'oab'            => $this->oab ?: null,
            'observacoes'    => $this->observacoes ?: null,
        ];

        if ($this->pessoaId) {
            $pessoa = Pessoa::findOrFail($this->pessoaId);
            $pessoa->update($dados);
            $acao = 'Editou pessoa';
        } else {
            $pessoa = Pessoa::create($dados);
            $acao   = 'Criou pessoa';
        }

        $pessoa->sincronizarTipos($this->tipos_selecionados);
        Auth::user()->registrarAuditoria($acao, 'pessoas', $pessoa->id, null, ['nome' => $this->nome, 'tipos' => $this->tipos_selecionados]);

        $this->fecharModal();
        session()->flash('sucesso', "Pessoa \"{$this->nome}\" salva com sucesso!");
    }

    public function desativar(int $id): void
    {
        $pessoa = Pessoa::findOrFail($id);
        $pessoa->update(['ativo' => false]);
        Auth::user()->registrarAuditoria('Desativou pessoa', 'pessoas', $id);
        session()->flash('sucesso', "Pessoa \"{$pessoa->nome}\" desativada.");
    }

    private function limparFormulario(): void
    {
        $this->pessoaId = null;
        $this->nome = $this->cpf_cnpj = $this->rg = $this->data_nascimento = '';
        $this->telefone = $this->celular = $this->email = '';
        $this->logradouro = $this->cidade = $this->estado = $this->cep = '';
        $this->oab = $this->observacoes = '';
        $this->tipos_selecionados = [];
        $this->resetErrorBag();
    }

    public function render()
    {
        $pessoas = Pessoa::ativos()
            ->when($this->busca, fn($q) => $q->busca($this->busca))
            ->when($this->tipo,  fn($q) => $q->doTipo($this->tipo))
            ->orderBy('nome')
            ->paginate(15);

        // Busca os tipos de cada pessoa listada
        $ids = $pessoas->pluck('id');
        $tiposPorPessoa = DB::table('pessoa_tipos')
            ->whereIn('pessoa_id', $ids)
            ->get()
            ->groupBy('pessoa_id')
            ->map(fn($g) => $g->pluck('tipo')->toArray());

        return view('livewire.pessoas', [
            'pessoas'       => $pessoas,
            'tiposPorPessoa'=> $tiposPorPessoa,
            'tiposDisponiveis' => self::TIPOS,
        ]);
    }
}
