<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Processo;
use App\Models\Pessoa;
use App\Services\TribunalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProcessoForm extends Component
{
    public ?int $processoId = null;

    // Detecção de tribunal ao vivo
    public string $tribunalDetectado = '';
    public bool   $numeroValido      = false;

    // Identificação
    public string  $numero            = '';
    public string  $data_distribuicao = '';
    public bool    $extrajudicial     = false;

    // Cliente — autocomplete
    public ?int    $cliente_id       = null;
    public string  $clienteBusca     = '';
    public string  $clienteNome      = '';
    public array   $clienteSugestoes = [];

    // Autor/Réu + Unidade
    public string  $autorReu = '';
    public string  $unidade  = '';

    // Conflito de interesses
    public array   $conflitos = [];

    // Sugestão de risco por IA
    public string  $sugestaoRisco      = '';
    public bool    $mostrarSugestaoRisco = false;
    public bool    $gerandoRisco        = false;

    // Parte Contrária — autocomplete
    public ?int    $parteContrariaId         = null;
    public string  $parteContrariaBusca      = '';
    public string  $parte_contraria          = ''; // coluna original, mantida
    public array   $parteContrariaSugestoes  = [];

    // Classificação
    public array   $advogados_selecionados = [];
    public ?int    $advogado_id            = null; // legado — mantido
    public ?int    $juiz_id               = null;
    public ?int    $tipo_acao_id          = null;
    public ?int    $tipo_processo_id      = null;
    public ?int    $fase_id               = null;
    public ?int    $assunto_id            = null;
    public ?int    $risco_id              = null;
    public ?int    $secretaria_id         = null;
    public ?int    $reparticao_id         = null;
    public string  $vara                  = '';
    public string  $valor_causa           = '';
    public string  $valor_risco           = '';
    public string  $observacoes           = '';
    public string  $status                = 'Ativo';

    // ── Tribunal ─────────────────────────────────

    public function updatedNumero(): void
    {
        $service  = new TribunalService();
        $tribunal = $service->detectarTribunal($this->numero);

        if ($tribunal) {
            $this->tribunalDetectado = $tribunal['nome'];
            $this->numeroValido      = true;
        } else {
            $this->tribunalDetectado = '';
            $this->numeroValido      = false;
        }
    }

    // ── Cliente autocomplete ──────────────────────

    public function updatedClienteBusca(): void
    {
        if (strlen($this->clienteBusca) < 2) {
            $this->clienteSugestoes = [];
            return;
        }

        $this->clienteSugestoes = Pessoa::doTipo('Cliente')
            ->where('nome', 'ilike', "%{$this->clienteBusca}%")
            ->orderBy('nome')
            ->limit(10)
            ->get(['id', 'nome'])
            ->toArray();
    }

    public function selecionarCliente(int $id, string $nome): void
    {
        $this->cliente_id       = $id;
        $this->clienteNome      = $nome;
        $this->clienteBusca     = $nome;
        $this->clienteSugestoes = [];
        $this->verificarConflito();
    }

    public function limparCliente(): void
    {
        $this->cliente_id       = null;
        $this->clienteNome      = '';
        $this->clienteBusca     = '';
        $this->clienteSugestoes = [];
        $this->conflitos        = [];
    }

    // ── Parte Contrária autocomplete ─────────────

    public function updatedParteContrariaBusca(): void
    {
        if (strlen($this->parteContrariaBusca) < 2) {
            $this->parteContrariaSugestoes = [];
            return;
        }

        $this->parteContrariaSugestoes = Pessoa::doTipo('Parte Contrária')
            ->where('nome', 'ilike', "%{$this->parteContrariaBusca}%")
            ->orderBy('nome')
            ->limit(10)
            ->get(['id', 'nome'])
            ->toArray();
    }

    public function selecionarParteContraria(int $id, string $nome): void
    {
        $this->parteContrariaId         = $id;
        $this->parte_contraria          = $nome;
        $this->parteContrariaBusca      = $nome;
        $this->parteContrariaSugestoes  = [];
        $this->verificarConflito();
    }

    public function limparParteContraria(): void
    {
        $this->parteContrariaId         = null;
        $this->parte_contraria          = '';
        $this->parteContrariaBusca      = '';
        $this->parteContrariaSugestoes  = [];
        $this->conflitos                = [];
    }

    // ── Sugestão de Risco por IA ─────────────────

    public function sugerirRisco(): void
    {
        if ($this->gerandoRisco) return;

        $this->gerandoRisco        = true;
        $this->sugestaoRisco       = '';
        $this->mostrarSugestaoRisco = false;

        $tipoAcao = $this->tipo_acao_id
            ? \App\Models\TipoAcao::find($this->tipo_acao_id)?->descricao
            : null;
        $fase = $this->fase_id
            ? \App\Models\Fase::find($this->fase_id)?->descricao
            : null;

        if (!$tipoAcao && !$fase && empty($this->valor_causa)) {
            $this->sugestaoRisco       = 'Preencha ao menos Tipo de Ação, Fase ou Valor da Causa para sugerir o risco.';
            $this->gerandoRisco        = false;
            $this->mostrarSugestaoRisco = true;
            return;
        }

        $dados = implode(' | ', array_filter([
            $tipoAcao             ? "Tipo de Ação: {$tipoAcao}"         : null,
            $fase                 ? "Fase: {$fase}"                      : null,
            $this->valor_causa    ? "Valor da Causa: R$ {$this->valor_causa}" : null,
            $this->observacoes    ? "Observações: {$this->observacoes}"  : null,
        ]));

        $prompt = "Você é um advogado experiente no direito brasileiro. "
            . "Com base nos dados do processo abaixo, classifique o grau de risco como Baixo, Médio ou Alto "
            . "e forneça uma justificativa de no máximo 2 linhas. "
            . "Responda exatamente neste formato: RISCO: [nível] — [justificativa breve]. "
            . "Dados: {$dados}";

        $result = app(\App\Services\GeminiService::class)->gerar($prompt, 200);

        $this->sugestaoRisco       = $result ?? 'IA temporariamente indisponível. Tente novamente.';
        $this->gerandoRisco        = false;
        $this->mostrarSugestaoRisco = true;
    }

    // ── Conflito de Interesses ────────────────────

    public function verificarConflito(): void
    {
        $this->conflitos = [];

        if (! $this->cliente_id && ! $this->parteContrariaId) {
            return;
        }

        $exc = $this->processoId ? " AND id != {$this->processoId}" : '';

        // Cliente selecionado é parte contrária em outro processo ativo?
        if ($this->cliente_id) {
            $rows = DB::select(
                "SELECT numero FROM processos WHERE status = 'Ativo' AND parte_contraria_id = ?{$exc}",
                [$this->cliente_id]
            );
            foreach ($rows as $r) {
                $this->conflitos[] = "O cliente figura como parte contrária no processo {$r->numero}.";
            }
        }

        // Parte contrária selecionada é cliente em outro processo ativo?
        if ($this->parteContrariaId) {
            $rows = DB::select(
                "SELECT numero FROM processos WHERE status = 'Ativo' AND cliente_id = ?{$exc}",
                [$this->parteContrariaId]
            );
            foreach ($rows as $r) {
                $this->conflitos[] = "A parte contrária é cliente no processo {$r->numero}.";
            }
        }
    }

    // ── Mount ─────────────────────────────────────

    public function mount(?int $processoId = null): void
    {
        $this->processoId = $processoId;

        if ($processoId) {
            $processo = Processo::with(['advogados', 'cliente'])->findOrFail($processoId);

            $this->numero            = $processo->numero ?? '';
            $this->updatedNumero();
            $this->data_distribuicao = $processo->data_distribuicao?->format('Y-m-d') ?? '';
            $this->extrajudicial     = (bool) ($processo->extrajudicial ?? false);

            // Cliente
            $this->cliente_id   = $processo->cliente_id;
            $this->clienteNome  = $processo->cliente?->nome ?? '';
            $this->clienteBusca = $processo->cliente?->nome ?? '';

            // Autor/Réu + Unidade
            $this->autorReu = $processo->autor_reu ?? '';
            $this->unidade  = $processo->unidade ?? '';

            // Parte Contrária
            $this->parte_contraria     = $processo->parte_contraria ?? '';
            $this->parteContrariaBusca = $processo->parte_contraria ?? '';
            $this->parteContrariaId    = $processo->parte_contraria_id;

            // Advogados (pivot)
            $this->advogados_selecionados = $processo->advogados->pluck('id')->toArray();
            $this->advogado_id            = $processo->advogado_id; // legado

            $this->juiz_id          = $processo->juiz_id;
            $this->tipo_acao_id     = $processo->tipo_acao_id;
            $this->tipo_processo_id = $processo->tipo_processo_id;
            $this->fase_id          = $processo->fase_id;
            $this->assunto_id       = $processo->assunto_id;
            $this->risco_id         = $processo->risco_id;
            $this->secretaria_id    = $processo->secretaria_id;
            $this->reparticao_id    = $processo->reparticao_id;
            $this->vara             = $processo->vara ?? '';
            $this->valor_causa      = $processo->valor_causa ?? '';
            $this->valor_risco      = $processo->valor_risco ?? '';
            $this->observacoes      = $processo->observacoes ?? '';
            $this->status           = $processo->status ?? 'Ativo';
        }
    }

    // ── Salvar ────────────────────────────────────

    public function salvar(): void
    {
        // Verificar limite de processos
        $tenant = tenant();
        if ($tenant && !$this->processoId && $tenant->atingiuLimiteProcessos()) {
            $this->dispatch('toast',
                message: 'Limite de processos atingido! Faça upgrade do seu plano.',
                type: 'error'
            );
            return;
        }

        $this->validate([
            'numero'     => 'required|string|max:50',
            'cliente_id' => 'required|integer',
            'data_distribuicao' => 'nullable|date',
        ], [
            'numero.required'     => 'O número do processo é obrigatório.',
            'cliente_id.required' => 'O cliente é obrigatório.',
        ]);

        // parte_contraria: usar o texto do campo de busca se nenhum ID foi selecionado
        $parteContraria = $this->parte_contraria ?: ($this->parteContrariaBusca ?: null);

        $dados = [
            'numero'             => $this->numero,
            'data_distribuicao'  => $this->data_distribuicao ?: null,
            'extrajudicial'      => $this->extrajudicial,
            'cliente_id'         => $this->cliente_id,
            'parte_contraria'    => $parteContraria,
            'parte_contraria_id' => $this->parteContrariaId,
            'autor_reu'         => $this->autorReu ?: null,
            'unidade'           => $this->unidade ?: null,
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
            'status'            => 'Ativo',
        ];

        if ($this->processoId) {
            $processo = Processo::findOrFail($this->processoId);
            $processo->update($dados);
            $this->dispatch('toast', message: 'Processo atualizado com sucesso!', type: 'success');
        } else {
            $dados['criado_por'] = Auth::id();
            $processo = Processo::create($dados);
            $this->dispatch('toast', message: 'Processo cadastrado com sucesso!', type: 'success');
        }

        // Sincronizar advogados na tabela pivot
        $processo->advogados()->sync($this->advogados_selecionados);

        $this->redirect(route('processos.show', $processo->id));
    }

    // ── Render ───────────────────────────────────

    public function render()
    {
        $advogados     = Pessoa::doTipo('Advogado')->orderBy('nome')->get();
        $juizes        = Pessoa::doTipo('Juiz')->orderBy('nome')->get();
        $fases         = \App\Models\Fase::orderBy('descricao')->get();
        $riscos        = DB::table('graus_risco')->orderBy('descricao')->get();
        $tiposAcao     = DB::table('tipos_acao')->orderBy('descricao')->get();
        $tiposProcesso = DB::table('tipos_processo')->orderBy('descricao')->get();
        $assuntos      = DB::table('assuntos')->orderBy('descricao')->get();
        $secretarias   = DB::table('secretarias')->orderBy('descricao')->get();
        $reparticoes   = DB::table('reparticoes')->orderBy('descricao')->get();

        return view('livewire.processo-form', compact(
            'advogados', 'juizes', 'fases',
            'riscos', 'tiposAcao', 'tiposProcesso', 'assuntos',
            'secretarias', 'reparticoes'
        ));
    }
}
