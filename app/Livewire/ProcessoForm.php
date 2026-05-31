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
    public string  $sugestaoRisco       = '';
    public bool    $mostrarSugestaoRisco = false;
    public bool    $gerandoRisco         = false;

    // Parte Contrária — autocomplete
    public ?int    $parteContrariaId        = null;
    public string  $parteContrariaBusca     = '';
    public string  $parte_contraria         = '';
    public array   $parteContrariaSugestoes = [];

    // Classificação
    public array   $advogados_selecionados = [];
    public ?int    $advogado_id            = null;
    public ?int    $tipo_acao_id           = null;
    public ?int    $tipo_processo_id       = null;
    public ?int    $fase_id                = null;
    public ?int    $risco_id               = null;
    public ?int    $reparticao_id          = null;
    public string  $vara                   = '';
    public string  $valor_causa            = '';
    public string  $valor_risco            = '';
    public string  $observacoes            = '';
    public string  $status                 = 'Ativo';

    // ── Helper de tenant ─────────────────────────
    private function tenantId(): int
    {
        return auth('usuarios')->user()->tenant_id;
    }

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
            ->where('tenant_id', $this->tenantId())
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

    // Trazer advogados vinculados ao cliente
    $advogadosDoCliente = DB::table('cliente_advogado')
        ->where('cliente_id', $id)
        ->pluck('advogado_id')
        ->toArray();

    if (count($advogadosDoCliente) > 0) {
        $this->advogados_selecionados = $advogadosDoCliente;
    }
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
            ->where('tenant_id', $this->tenantId())
            ->where('nome', 'ilike', "%{$this->parteContrariaBusca}%")
            ->orderBy('nome')
            ->limit(10)
            ->get(['id', 'nome'])
            ->toArray();
    }

    public function selecionarParteContraria(int $id, string $nome): void
    {
        $this->parteContrariaId        = $id;
        $this->parte_contraria         = $nome;
        $this->parteContrariaBusca     = $nome;
        $this->parteContrariaSugestoes = [];
        $this->verificarConflito();
    }

    public function limparParteContraria(): void
    {
        $this->parteContrariaId        = null;
        $this->parte_contraria         = '';
        $this->parteContrariaBusca     = '';
        $this->parteContrariaSugestoes = [];
        $this->conflitos               = [];
    }

    // ── Sugestão de Risco por IA ─────────────────

    public function sugerirRisco(): void
    {
        if ($this->gerandoRisco) return;

        $this->gerandoRisco         = true;
        $this->sugestaoRisco        = '';
        $this->mostrarSugestaoRisco = false;

        $tipoAcao = $this->tipo_acao_id
            ? \App\Models\TipoAcao::find($this->tipo_acao_id)?->descricao
            : null;
        $fase = $this->fase_id
            ? \App\Models\Fase::find($this->fase_id)?->descricao
            : null;

        if (!$tipoAcao && !$fase && empty($this->valor_causa)) {
            $this->sugestaoRisco        = 'Preencha ao menos Tipo de Ação, Fase ou Valor da Causa para sugerir o risco.';
            $this->gerandoRisco         = false;
            $this->mostrarSugestaoRisco = true;
            return;
        }

        $dados = implode(' | ', array_filter([
            $tipoAcao          ? "Tipo de Ação: {$tipoAcao}"              : null,
            $fase              ? "Fase: {$fase}"                           : null,
            $this->valor_causa ? "Valor da Causa: R$ {$this->valor_causa}" : null,
            $this->observacoes ? "Observações: {$this->observacoes}"       : null,
        ]));

        $prompt = "Você é um advogado experiente no direito brasileiro. "
            . "Com base nos dados do processo abaixo, classifique o grau de risco como Baixo, Médio ou Alto "
            . "e forneça uma justificativa de no máximo 2 linhas. "
            . "Responda exatamente neste formato: RISCO: [nível] — [justificativa breve]. "
            . "Dados: {$dados}";

        $result = app(\App\Services\AIService::class)->gerar($prompt, 200);

        $this->sugestaoRisco        = $result ?? 'IA temporariamente indisponível. Tente novamente.';
        $this->gerandoRisco         = false;
        $this->mostrarSugestaoRisco = true;
    }

    // ── Conflito de Interesses ────────────────────

    public function verificarConflito(): void
    {
        $this->conflitos = [];

        if (! $this->cliente_id && ! $this->parteContrariaId) {
            return;
        }

        $tid = $this->tenantId();
        $exc = $this->processoId ? " AND id != {$this->processoId}" : '';

        if ($this->cliente_id) {
            $rows = DB::select(
                "SELECT numero FROM processos WHERE tenant_id = ? AND status = 'Ativo' AND parte_contraria_id = ?{$exc}",
                [$tid, $this->cliente_id]
            );
            foreach ($rows as $r) {
                $this->conflitos[] = "O cliente figura como parte contrária no processo {$r->numero}.";
            }
        }

        if ($this->parteContrariaId) {
            $rows = DB::select(
                "SELECT numero FROM processos WHERE tenant_id = ? AND status = 'Ativo' AND cliente_id = ?{$exc}",
                [$tid, $this->parteContrariaId]
            );
            foreach ($rows as $r) {
                $this->conflitos[] = "A parte contrária é cliente no processo {$r->numero}.";
            }
        }
    }

    // ── Mount ─────────────────────────────────────

    private function normalizarDecimal(string $valor): string
    {
        $valor = trim(str_replace(['R$', ' '], '', $valor));

        if ($valor === '') {
            return '0';
        }

        if (str_contains($valor, ',')) {
            $valor = str_replace('.', '', $valor);
            $valor = str_replace(',', '.', $valor);
        }

        return is_numeric($valor) ? $valor : '0';
    }

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
            $this->advogado_id            = $processo->advogado_id;

            $this->tipo_acao_id     = $processo->tipo_acao_id;
            $this->tipo_processo_id = $processo->tipo_processo_id;
            $this->fase_id          = $processo->fase_id;
            $this->risco_id         = $processo->risco_id;
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
        $tenant = tenant();
        if ($tenant && !$this->processoId && $tenant->atingiuLimiteProcessos()) {
            $this->dispatch('toast',
                message: 'Limite de processos atingido! Faça upgrade do seu plano.',
                type: 'error'
            );
            return;
        }

        $uniqueNumero = \Illuminate\Validation\Rule::unique('processos', 'numero')
            ->where('tenant_id', tenant_id())
            ->whereNotNull('numero')
            ->ignore($this->processoId);

       

	$regraNumero = $this->extrajudicial
    		? ['nullable', 'string', 'max:30']
    		: ['required', 'string', 'max:30', $uniqueNumero];

	

	$this->validate([
    		'numero'            => $regraNumero,
    		'cliente_id'        => 'required|integer',
    		'data_distribuicao' => 'nullable|date',
	], [
    		'numero.required'     => 'O número do processo é obrigatório.',
    		'numero.max'          => 'O número do processo não pode exceder 30 caracteres.',
    		'numero.unique'       => 'Já existe outro processo com este número.',
    		'cliente_id.required' => 'O cliente é obrigatório.',
	]);



        $parteContraria = $this->parte_contraria ?: ($this->parteContrariaBusca ?: null);

        $dados = [
            'numero'             => $this->numero ?: null,
            'data_distribuicao'  => $this->data_distribuicao ?: null,
            'extrajudicial'      => $this->extrajudicial,
            'cliente_id'         => $this->cliente_id,
            'parte_contraria'    => $parteContraria,
            'parte_contraria_id' => $this->parteContrariaId ?: null,
            'autor_reu'          => $this->autorReu ?: null,
            'unidade'            => $this->unidade ?: null,
            'advogado_id'        => $this->advogado_id ?: null,
            'tipo_acao_id'       => $this->tipo_acao_id ?: null,
            'tipo_processo_id'   => $this->tipo_processo_id ?: null,
            'fase_id'            => $this->fase_id ?: null,
            'risco_id'           => $this->risco_id ?: null,
            'reparticao_id'      => $this->reparticao_id ?: null,
            'vara'               => $this->vara ?: null,
            'valor_causa'        => $this->normalizarDecimal($this->valor_causa),
            'valor_risco'        => $this->normalizarDecimal($this->valor_risco),
            'observacoes'        => $this->observacoes ?: null,
            'status'             => $this->status,
        ];

        if ($this->processoId) {
            $processo = Processo::withoutGlobalScopes()->findOrFail($this->processoId);

            $processo->fill($dados);
            $alterado = $processo->isDirty();

            $advogadosAtuais   = $processo->advogados()->pluck('pessoas.id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $advogadosNovos    = collect($this->advogados_selecionados)->map(fn ($id) => (int) $id)->sort()->values()->all();
            $advogadosAlterados = $advogadosAtuais !== $advogadosNovos;

            if (! $alterado && ! $advogadosAlterados) {
                $this->dispatch('toast', message: 'Nenhuma alteração detectada.', type: 'info');
                return;
            }

            if ($alterado) {
                $processo->save();
            }

            if ($advogadosAlterados) {
                $processo->advogados()->sync($this->advogados_selecionados);
            }

            \Illuminate\Support\Facades\Log::info('ProcessoForm::salvar', [
                'processoId'         => $this->processoId,
                'alterado'           => $alterado,
                'advogadosAlterados' => $advogadosAlterados,
                'changes'            => array_keys($processo->getChanges()),
                'fase_id'            => $this->fase_id,
                'status'             => $this->status,
            ]);

            $this->dispatch('toast', message: "Processo #{$this->processoId} atualizado!", type: 'success');
        } else {
            $dados['criado_por'] = Auth::guard('usuarios')->id();
            $dados['tenant_id']  = tenant_id() ?? Auth::guard('usuarios')->user()?->tenant_id;
            $processo = Processo::create($dados);
            $processo->advogados()->sync($this->advogados_selecionados);
            $this->dispatch('toast', message: 'Processo cadastrado com sucesso!', type: 'success');
        }

        $this->redirect(route('processos.show', $processo->id));
    }

    // ── Render ───────────────────────────────────

    public function render()
    {
        $tid = $this->tenantId();

        $advogados     = Pessoa::doTipo('Advogado')->where('tenant_id', $tid)->orderBy('nome')->get();
        $fases         = \App\Models\Fase::where('tenant_id', $tid)->orderBy('descricao')->get();
        $riscos        = DB::table('graus_risco')->where('tenant_id', $tid)->orderBy('descricao')->get();
        $tiposAcao     = DB::table('tipos_acao')->where('tenant_id', $tid)->orderBy('descricao')->get();
        $tiposProcesso = DB::table('tipos_processo')->where('tenant_id', $tid)->orderBy('descricao')->get();
        $reparticoes   = DB::table('reparticoes')->where('tenant_id', $tid)->orderBy('descricao')->get();
        $varas         = DB::table('varas')->where('tenant_id', $tid)->where('ativo', true)->orderBy('descricao')->get();

        return view('livewire.processo-form', compact(
            'advogados', 'fases',
            'riscos', 'tiposAcao', 'tiposProcesso',
            'reparticoes', 'varas'
        ));
    }
}
