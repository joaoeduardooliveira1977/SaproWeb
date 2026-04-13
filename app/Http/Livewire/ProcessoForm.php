<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\{Processo, Pessoa, Fase, GrauRisco, TipoAcao, TipoProcesso, Reparticao};
use Illuminate\Support\Facades\Auth;

class ProcessoForm extends Component
{
    public ?int $processoId = null;

    // Campos do formulário
    public string  $numero             = '';
    public string  $data_distribuicao  = '';
    public string  $cliente_id         = '';
    public string  $parte_contraria    = '';
    public string  $advogado_id        = '';
    public string  $tipo_acao_id       = '';
    public string  $tipo_processo_id   = '';
    public string  $fase_id            = '';
    public string  $risco_id           = '';
    public string  $reparticao_id      = '';
    public string  $vara               = '';
    public string  $valor_causa        = '0,00';
    public string  $valor_risco        = '0,00';
    public string  $observacoes        = '';
    public string  $status             = 'Ativo';

    protected $rules = [
        'numero'   => 'required|string|max:30',
        'cliente_id' => 'required|exists:pessoas,id',
        'status'   => 'required|in:Ativo,Arquivado,Encerrado,Suspenso',
        'valor_causa' => 'nullable',
        'valor_risco' => 'nullable',
    ];

    protected $messages = [
        'numero.required'     => 'O número do processo é obrigatório.',
        'cliente_id.required' => 'O cliente é obrigatório.',
    ];

    public function mount(?int $id = null): void
    {
        if ($id) {
            $this->processoId = $id;
            $p = Processo::findOrFail($id);
            $this->numero           = $p->numero;
            $this->data_distribuicao= $p->data_distribuicao?->format('Y-m-d') ?? '';
            $this->cliente_id       = (string) $p->cliente_id;
            $this->parte_contraria  = $p->parte_contraria ?? '';
            $this->advogado_id      = (string) ($p->advogado_id ?? '');
            $this->tipo_acao_id     = (string) ($p->tipo_acao_id ?? '');
            $this->tipo_processo_id = (string) ($p->tipo_processo_id ?? '');
            $this->fase_id          = (string) ($p->fase_id ?? '');
            $this->risco_id         = (string) ($p->risco_id ?? '');
            $this->reparticao_id    = (string) ($p->reparticao_id ?? '');
            $this->vara             = $p->vara ?? '';
            $this->valor_causa      = number_format($p->valor_causa, 2, ',', '.');
            $this->valor_risco      = number_format($p->valor_risco, 2, ',', '.');
            $this->observacoes      = $p->observacoes ?? '';
            $this->status           = $p->status;
        }
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'numero'           => $this->numero,
            'data_distribuicao'=> $this->data_distribuicao ?: null,
            'cliente_id'       => $this->cliente_id,
            'parte_contraria'  => $this->parte_contraria ?: null,
            'advogado_id'      => $this->advogado_id ?: null,
            'tipo_acao_id'     => $this->tipo_acao_id ?: null,
            'tipo_processo_id' => $this->tipo_processo_id ?: null,
            'fase_id'          => $this->fase_id ?: null,
            'risco_id'         => $this->risco_id ?: null,
            'reparticao_id'    => $this->reparticao_id ?: null,
            'vara'             => $this->vara ?: null,
            'valor_causa'      => str_replace(['.', ','], ['', '.'], $this->valor_causa),
            'valor_risco'      => str_replace(['.', ','], ['', '.'], $this->valor_risco),
            'observacoes'      => $this->observacoes ?: null,
            'status'           => $this->status,
        ];

        if ($this->processoId) {
            $processo = Processo::findOrFail($this->processoId);
            $processo->update($dados);
            Auth::user()->registrarAuditoria('Editou processo', 'processos', $processo->id, null, ['numero' => $this->numero]);
            session()->flash('sucesso', 'Processo atualizado com sucesso!');
        } else {
            $dados['criado_por'] = Auth::id();
            $processo = Processo::create($dados);
            Auth::user()->registrarAuditoria('Criou processo', 'processos', $processo->id, null, ['numero' => $this->numero]);
            session()->flash('sucesso', 'Processo cadastrado com sucesso!');
            $this->redirect(route('processos.show', $processo->id));
        }
    }

    public function render()
    {
        return view('livewire.processo-form', [
            'clientes'      => Pessoa::ativos()->doTipo('Cliente')->orderBy('nome')->get(),
            'advogados'     => Pessoa::ativos()->doTipo('Advogado')->orderBy('nome')->get(),
            'fases'         => Fase::orderBy('ordem')->get(),
            'riscos'        => GrauRisco::all(),
            'tiposAcao'     => TipoAcao::where('ativo', true)->orderBy('descricao')->get(),
            'tiposProcesso' => TipoProcesso::where('ativo', true)->orderBy('descricao')->get(),
            'reparticoes'   => Reparticao::where('ativo', true)->orderBy('descricao')->get(),
        ]);
    }
}
