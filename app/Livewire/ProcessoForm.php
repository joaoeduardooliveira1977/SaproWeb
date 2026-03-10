<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Processo;
use App\Models\Pessoa;
use App\Models\Fase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcessoForm extends Component
{
    public ?int $processoId = null;

    // Campos do formulário
    public string  $numero            = '';
    public string  $data_distribuicao = '';
    public ?int    $cliente_id        = null;
    public string  $parte_contraria   = '';
    public ?int    $advogado_id       = null;
    public ?int    $juiz_id           = null;
    public ?int    $tipo_acao_id      = null;
    public ?int    $tipo_processo_id  = null;
    public ?int    $fase_id           = null;
    public ?int    $assunto_id        = null;
    public ?int    $risco_id          = null;
    public ?int    $secretaria_id     = null;
    public ?int    $reparticao_id     = null;
    public string  $vara              = '';
    public string  $valor_causa       = '';
    public string  $valor_risco       = '';
    public string  $observacoes       = '';
    public string  $status            = 'Ativo';

    public function mount(?int $processoId = null): void
	{
    		$this->processoId = $processoId;

        if ($processoId) {
            $processo = Processo::findOrFail($processoId);
            $this->numero            = $processo->numero ?? '';
            $this->data_distribuicao = $processo->data_distribuicao ? $processo->data_distribuicao->format('Y-m-d') : '';
            $this->cliente_id        = $processo->cliente_id;
            $this->parte_contraria   = $processo->parte_contraria ?? '';
            $this->advogado_id       = $processo->advogado_id;
            $this->juiz_id           = $processo->juiz_id;
            $this->tipo_acao_id      = $processo->tipo_acao_id;
            $this->tipo_processo_id  = $processo->tipo_processo_id;
            $this->fase_id           = $processo->fase_id;
            $this->assunto_id        = $processo->assunto_id;
            $this->risco_id          = $processo->risco_id;
            $this->secretaria_id     = $processo->secretaria_id;
            $this->reparticao_id     = $processo->reparticao_id;
            $this->vara              = $processo->vara ?? '';
            $this->valor_causa       = $processo->valor_causa ?? '';
            $this->valor_risco       = $processo->valor_risco ?? '';
            $this->observacoes       = $processo->observacoes ?? '';
            $this->status            = $processo->status ?? 'Ativo';
        }
    }

    public function salvar(): void
    {
        $this->validate([
            'numero'            => 'required|string|max:50',
            'data_distribuicao' => 'nullable|date',
            'cliente_id'        => 'nullable|integer',
            'advogado_id'       => 'nullable|integer',
            'status'            => 'required|string',
        ]);

        $dados = [
            'numero'            => $this->numero,
            'data_distribuicao' => $this->data_distribuicao ?: null,
            'cliente_id'        => $this->cliente_id,
            'parte_contraria'   => $this->parte_contraria ?: null,
            'advogado_id'       => $this->advogado_id,
            'juiz_id'           => $this->juiz_id,
            'tipo_acao_id'      => $this->tipo_acao_id,
            'tipo_processo_id'  => $this->tipo_processo_id,
            'fase_id'           => $this->fase_id,
            'assunto_id'        => $this->assunto_id,
            'risco_id'          => $this->risco_id,
            'secretaria_id'     => $this->secretaria_id,
            'reparticao_id'     => $this->reparticao_id,
            'vara'              => $this->vara ?: null,
            'valor_causa'       => $this->valor_causa ?: null,
            'valor_risco'       => $this->valor_risco ?: null,
            'observacoes'       => $this->observacoes ?: null,
            'status'            => $this->status,
        ];

        if ($this->processoId) {
            $processo = Processo::findOrFail($this->processoId);
            $processo->update($dados);
            session()->flash('sucesso', 'Processo atualizado com sucesso!');
        } else {
            $dados['criado_por'] = Auth::id();
            $processo = Processo::create($dados);
            session()->flash('sucesso', 'Processo cadastrado com sucesso!');
        }

        $this->redirect(route('processos.show', $processo->id));
    }

    public function render()
    {
        $clientes  = Pessoa::doTipo('Cliente')->orderBy('nome')->get();
        $advogados = Pessoa::doTipo('Advogado')->orderBy('nome')->get();
        $juizes    = Pessoa::doTipo('Juiz')->orderBy('nome')->get();
        $fases     = \App\Models\Fase::orderBy('descricao')->get();
	$riscos        = DB::table('graus_risco')->orderBy('descricao')->get();
	$tiposAcao     = DB::table('tipos_acao')->orderBy('descricao')->get();
	$tiposProcesso = DB::table('tipos_processo')->orderBy('descricao')->get();
	$assuntos      = DB::table('assuntos')->orderBy('descricao')->get();
	$secretarias   = DB::table('secretarias')->orderBy('descricao')->get();
	$reparticoes   = DB::table('reparticoes')->orderBy('descricao')->get();

        return view('livewire.processo-form', compact(
            'clientes', 'advogados', 'juizes', 'fases',
            'riscos', 'tiposAcao', 'tiposProcesso', 'assuntos',
            'secretarias', 'reparticoes'
        ));
    }
}
