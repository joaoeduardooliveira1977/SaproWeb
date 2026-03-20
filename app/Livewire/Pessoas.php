<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Pessoa, Administradora};
use Illuminate\Support\Facades\{Auth, DB};

class Pessoas extends Component
{
    use WithPagination;

    public string $busca = '';
    public string $tipo  = '';

    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    protected $queryString = [
        'busca' => ['except' => ''],
        'tipo'  => ['except' => ''],
    ];

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
    public ?int   $administradoraId = null;

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
            $this->administradoraId = $p->administradora_id;
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->limparFormulario();
    }

    public function salvar(): void
    {
        abort_unless(Auth::user()->temAcao('pessoas.editar'), 403, 'Sem permissão.');
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
            'oab'             => $this->oab ?: null,
            'observacoes'     => $this->observacoes ?: null,
            'administradora_id' => $this->administradoraId ?: null,
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
        $this->dispatch('toast', message: "Pessoa \"{$this->nome}\" salva com sucesso!", type: 'success');
    }

    public function desativar(int $id): void
    {
        abort_unless(Auth::user()->temAcao('pessoas.desativar'), 403, 'Sem permissão.');
        $pessoa = Pessoa::findOrFail($id);
        $pessoa->update(['ativo' => false]);
        Auth::user()->registrarAuditoria('Desativou pessoa', 'pessoas', $id);
        $this->dispatch('toast', message: "Pessoa \"{$pessoa->nome}\" desativada.", type: 'success');
    }

    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $total     = Pessoa::ativos()->count();
        $clientes  = Pessoa::ativos()->doTipo('Cliente')->count();
        $advogados = Pessoa::ativos()->doTipo('Advogado')->count();
        $juizes    = Pessoa::ativos()->doTipo('Juiz')->count();
        $partes    = Pessoa::ativos()->doTipo('Parte Contrária')->count();

        $contexto = "Você é um assistente jurídico do sistema SAPRO. Responda de forma objetiva em português.

Dados do cadastro de pessoas:
- Total de pessoas ativas: {$total}
- Clientes: {$clientes}
- Advogados: {$advogados}
- Juízes: {$juizes}
- Partes Contrárias: {$partes}

Pergunta: {$this->perguntaIA}

Responda em 1-3 frases objetivas. Se pedir para filtrar, termine com: FILTRO:tipo=Valor ou FILTRO:busca=texto";

        $resposta = app(\App\Services\GeminiService::class)->gerar($contexto, 300);

        if ($resposta === null) {
            $this->respostaIA = 'IA temporariamente indisponível.';
            return;
        }

        if (str_contains($resposta, 'FILTRO:')) {
            preg_match('/FILTRO:(\w+)=(.+)/', $resposta, $matches);
            if (count($matches) === 3) {
                $campo = trim($matches[1]);
                $valor = trim($matches[2]);
                if ($campo === 'busca') $this->busca = $valor;
                if ($campo === 'tipo')  $this->tipo  = $valor;
                $this->resetPage();
                $resposta = trim(preg_replace('/FILTRO:\w+=.+/', '', $resposta));
            }
        }

        $this->respostaIA = $resposta;
    }

    public function limparIA(): void
    {
        $this->perguntaIA = '';
        $this->respostaIA = null;
    }

    private function limparFormulario(): void
    {
        $this->pessoaId = null;
        $this->nome = $this->cpf_cnpj = $this->rg = $this->data_nascimento = '';
        $this->telefone = $this->celular = $this->email = '';
        $this->logradouro = $this->cidade = $this->estado = $this->cep = '';
        $this->oab = $this->observacoes = '';
        $this->tipos_selecionados = [];
        $this->administradoraId = null;
        $this->resetErrorBag();
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Pessoa::ativos()
            ->with('tipos')
            ->when($this->busca, fn($q) => $q->busca($this->busca))
            ->when($this->tipo,  fn($q) => $q->doTipo($this->tipo))
            ->orderBy('nome')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Nome','CPF/CNPJ','RG','Tipos','Email','Telefone','Celular','Cidade','Estado','CEP','OAB'], ';');
            foreach ($rows as $p) {
                $tipos = implode(', ', $p->listaTipos());
                fputcsv($out, [
                    $p->nome,
                    $p->cpf_cnpj ?? '',
                    $p->rg ?? '',
                    $tipos,
                    $p->email ?? '',
                    $p->telefone ?? '',
                    $p->celular ?? '',
                    $p->cidade ?? '',
                    $p->estado ?? '',
                    $p->cep ?? '',
                    $p->oab ?? '',
                ], ';');
            }
            fclose($out);
        }, 'pessoas-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
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

        $administradoras = Administradora::ativas()->orderBy('nome')->get();

        // Métricas
        $totalPessoas  = Pessoa::ativos()->count();
        $totalClientes = Pessoa::ativos()->doTipo('Cliente')->count();
        $totalAdvogados= Pessoa::ativos()->doTipo('Advogado')->count();
        $totalPartes   = Pessoa::ativos()->doTipo('Parte Contrária')->count();

        // Contagem por tipo para os filtros
        $tipoCounts = [];
        foreach (self::TIPOS as $t) {
            $tipoCounts[$t] = Pessoa::ativos()->doTipo($t)->count();
        }

        return view('livewire.pessoas', [
            'pessoas'          => $pessoas,
            'tiposPorPessoa'   => $tiposPorPessoa,
            'tiposDisponiveis' => self::TIPOS,
            'administradoras'  => $administradoras,
            'totalPessoas'     => $totalPessoas,
            'totalClientes'    => $totalClientes,
            'totalAdvogados'   => $totalAdvogados,
            'totalPartes'      => $totalPartes,
            'tipoCounts'       => $tipoCounts,
        ]);
    }
}
