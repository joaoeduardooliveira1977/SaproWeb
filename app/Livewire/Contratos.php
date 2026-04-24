<?php

namespace App\Livewire;

use App\Models\{Contrato, ContratoServico, ContratoRepasse, FinanceiroLancamento, ModeloContrato, Pessoa, Processo};
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Contratos extends Component
{
    use WithPagination, WithFileUploads;

    // â”€â”€ Filtros â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public string $busca          = '';
    public string $filtroTipo     = '';
    public string $filtroStatus   = 'ativo';

    protected $queryString = [
        'busca'        => ['except' => ''],
        'filtroTipo'   => ['except' => ''],
        'filtroStatus' => ['except' => 'ativo'],
    ];

    // â”€â”€ Modal principal â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $modal         = false;
    public ?int   $contratoId    = null;

    // Campos do contrato
    public int    $clienteId     = 0;
    public int    $advogadoResponsavelId = 0;
    public int    $processoContratoId = 0;
    public string $tipo          = 'honorario_processo';
    public string $descricao     = '';
    public string $observacoes   = '';
    public string $formaCobranca = 'parcelado';
    public string $valor         = '';
    public string $percentualExito = '';
    public string $diaVencimento   = '';
    public string $dataInicio      = '';
    public string $dataFim         = '';
    public string $status          = 'ativo';
    public        $arquivo         = null;
    public ?string $arquivoAtual   = null;
    public ?string $arquivoNome    = null;

    // â”€â”€ ServiÃ§os (itens do contrato) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $modalServico   = false;
    public ?int   $servicoId      = null;
    public ?int   $contratoIdServico = null;
    public string $servicoDescricao = '';
    public string $servicoTipo      = 'honorario';
    public string $servicoValor     = '';
    public string $servicoPercentual = '';
    public int    $servicoProcessoId = 0;
    public string $servicoObs        = '';
    public string $servicoVencimento  = '';
    public int    $servicoParcelas    = 1;
    public bool   $modalExito         = false;
    public ?int   $exitoServicoId     = null;
    public string $exitoValor         = '';
    public string $exitoVencimento    = '';
    public string $exitoObs           = '';

    // â”€â”€ Modal detalhe (visualizar contrato) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $modalDetalhe    = false;
    public ?int   $contratoDetalhe = null;

    // â”€â”€ ValidaÃ§Ã£o (admin/financeiro) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $podeValidar    = false;

    // â”€â”€ Repasses â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public bool   $modalRepasse      = false;
    public ?int   $repasseId         = null;
    public ?int   $repasseContratoId = null;
    public int    $repasseIndicadorId = 0;
    public string $repasseTipoCalculo = 'percentual';
    public string $repassePercentual  = '';
    public string $repasseValorFixo   = '';
    public string $repasseFrequencia  = 'mensal';

    // â”€â”€ Dados auxiliares â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    // â”€â”€ Modelo de contrato â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public int    $modeloId       = 0;
    public string $textoContrato  = '';

    // â”€â”€ Dados auxiliares â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public array  $clientes    = [];
    public array  $processos   = [];
    public array  $processosContrato = [];
    public array  $advogados   = [];
    public array  $indicadores = [];
    public array  $modelos     = [];

    public function mount(): void
    {
        $this->dataInicio  = now()->format('Y-m-d');
        $this->carregarAuxiliares();

        $usuario = Auth::guard('usuarios')->user();
        $this->podeValidar = $usuario && ($usuario->isAdmin() || $usuario->perfil === 'financeiro');

        $this->prepararContratoInicial();
        $this->prepararDetalheInicial();
    }

    private function carregarAuxiliares(): void
    {
        $this->clientes = DB::select("
            SELECT p.id, p.nome
            FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Cliente' AND p.ativo = true
            ORDER BY p.nome
        ");

        $this->advogados = DB::select("
            SELECT p.id, p.nome
            FROM pessoas p
            JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
            WHERE pt.tipo = 'Advogado' AND p.ativo = true
            ORDER BY p.nome
        ");

        // Indicadores: qualquer pessoa ativa (sÃ­ndico, corretor, advogado parceiro)
        $this->indicadores = DB::select("
            SELECT id, nome FROM pessoas WHERE ativo = true ORDER BY nome
        ");

        $this->processos = DB::select("
            SELECT p.id, p.numero, pe.nome as cliente_nome
            FROM processos p
            JOIN pessoas pe ON pe.id = p.cliente_id
            WHERE p.status = 'Ativo'
            ORDER BY p.numero
        ");

        $this->processosContrato = [];

        $this->modelos = ModeloContrato::where('tenant_id', tenant_id())
            ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id', 'nome', 'tipo', 'texto'])
            ->toArray();
    }

    public function updatedClienteId(): void
    {
        $this->carregarOpcoesDoCliente();
    }

    public function updatedModeloId(): void
    {
        if (! $this->modeloId) {
            $this->textoContrato = '';
            return;
        }

        $modelo = collect($this->modelos)->firstWhere('id', $this->modeloId);
        if (! $modelo) return;

        $this->textoContrato = (new ModeloContrato($modelo))->mesclar($this->variaveisContrato());
    }

    private function variaveisContrato(): array
    {
        $cliente   = $this->clienteId   ? DB::table('pessoas')->where('id', $this->clienteId)->first()   : null;
        $advogado  = $this->advogadoResponsavelId ? DB::table('pessoas')->where('id', $this->advogadoResponsavelId)->first() : null;
        $processo  = $this->processoContratoId    ? DB::table('processos')->where('id', $this->processoContratoId)->first()  : null;
        $tipoAcao  = $processo ? DB::table('tipo_acoes')->where('id', $processo->tipo_acao_id)->value('descricao') : null;
        $tenant    = DB::table('tenants')->where('id', tenant_id())->first();

        $valor = $this->valor ? 'R$ ' . $this->valor : '{{valor}}';
        $parcelas = $this->diaVencimento ?: '{{parcelas}}';

        return [
            'cliente'     => $cliente?->nome     ?? '{{cliente}}',
            'cpf_cnpj'    => $cliente?->cpf_cnpj ?? '{{cpf_cnpj}}',
            'advogado'    => $advogado?->nome     ?? '{{advogado}}',
            'oab'         => $advogado?->oab      ?? '{{oab}}',
            'processo'    => $processo?->numero   ?? '{{processo}}',
            'tipo_acao'   => $tipoAcao            ?? '{{tipo_acao}}',
            'vara'        => $processo?->vara     ?? '{{vara}}',
            'valor'       => $valor,
            'parcelas'    => $parcelas,
            'data_inicio' => $this->dataInicio ? \Carbon\Carbon::parse($this->dataInicio)->format('d/m/Y') : '{{data_inicio}}',
            'escritorio'  => $tenant?->nome      ?? '{{escritorio}}',
            'data_hoje'   => now()->format('d/m/Y'),
        ];
    }

    private function prepararContratoInicial(): void
    {
        if (!request()->boolean('novo') && !request()->boolean('novo_contrato')) {
            return;
        }

        $processoId = (int) request()->integer('processo');
        if ($processoId > 0) {
            $this->abrirContratoDoProcesso($processoId);
            return;
        }

        $clienteId = (int) request()->integer('cliente');
        if ($clienteId > 0) {
            $this->abrirModal();
            $this->clienteId = $clienteId;
            $this->carregarOpcoesDoCliente();
        }
    }

    private function prepararDetalheInicial(): void
    {
        $contratoId = (int) request()->integer('detalhe');

        if ($contratoId > 0) {
            $this->abrirDetalhe($contratoId);
        }
    }

    public function updatedServicoTipo(): void
    {
        if ($this->servicoTipo !== 'exito') {
            $this->servicoPercentual = '';
        }

        if (!$this->servicoDescricao || str_starts_with($this->servicoDescricao, 'ServiÃ§o:')) {
            $this->servicoDescricao = $this->descricaoServicoPadrao($this->servicoTipo);
        }
    }

    private function tipoServicoPadraoParaContrato(Contrato $contrato): string
    {
        return match ($contrato->forma_cobranca) {
            'mensal_recorrente' => 'consultoria',
            'exito'             => 'exito',
            'avulso'            => 'avulso',
            default             => 'honorario',
        };
    }

    private function descricaoServicoPadrao(string $tipo): string
    {
        return match ($tipo) {
            'consultoria' => 'ServiÃ§o: mensalidade de assessoria',
            'exito'       => 'ServiÃ§o: honorÃ¡rios de Ãªxito',
            'avulso'      => 'ServiÃ§o: atendimento avulso',
            'repasse'     => 'ServiÃ§o: repasse financeiro',
            'outro'       => 'ServiÃ§o: ajuste complementar',
            default       => 'ServiÃ§o: parcela de honorÃ¡rios',
        };
    }

    private function contextoTiposServico(): array
    {
        return [
            'honorario' => [
                'descricao'   => 'Use para entrada, parcelas ou honorÃ¡rios fixos do contrato.',
                'label_valor' => 'Valor da parcela (R$) *',
                'placeholder' => 'Ex: Entrada contratual ou Parcela 1/3',
            ],
            'consultoria' => [
                'descricao'   => 'Use para contratos mensais ou assessoria recorrente.',
                'label_valor' => 'Valor mensal (R$) *',
                'placeholder' => 'Ex: Mensalidade de assessoria jurÃ­dica',
            ],
            'exito' => [
                'descricao'   => 'Use para honorÃ¡rios condicionados ao ganho. Informe o percentual e, se quiser, um valor-base estimado.',
                'label_valor' => 'Valor-base estimado (R$)',
                'placeholder' => 'Ex: HonorÃ¡rios sobre Ãªxito da aÃ§Ã£o',
            ],
            'avulso' => [
                'descricao'   => 'Use para serviÃ§os pontuais cobrados uma Ãºnica vez.',
                'label_valor' => 'Valor do serviÃ§o (R$) *',
                'placeholder' => 'Ex: ElaboraÃ§Ã£o de parecer ou reuniÃ£o extraordinÃ¡ria',
            ],
            'repasse' => [
                'descricao'   => 'Use apenas se o contrato precisar registrar um serviÃ§o ligado a repasse especÃ­fico.',
                'label_valor' => 'Valor do repasse (R$) *',
                'placeholder' => 'Ex: Repasse operacional',
            ],
            'outro' => [
                'descricao'   => 'Use para ajustes complementares fora dos tipos principais.',
                'label_valor' => 'Valor do ajuste (R$) *',
                'placeholder' => 'Ex: Complemento de honorÃ¡rios',
            ],
        ];
    }

    private function abrirContratoDoProcesso(int $processoId): void
    {
        $processo = Processo::with(['cliente', 'advogado', 'tipoAcao'])->find($processoId);

        if (!$processo) {
            return;
        }

        $this->abrirModal();
        $this->clienteId = (int) $processo->cliente_id;
        $this->carregarOpcoesDoCliente();
        $this->processoContratoId = (int) $processo->id;

        $advogadoId = (int) ($processo->advogado_id ?? 0);
        if ($advogadoId > 0) {
            $this->advogadoResponsavelId = $advogadoId;
        } elseif (count($this->advogados) === 1) {
            $this->advogadoResponsavelId = (int) $this->advogados[0]->id;
        }

        $tipoAcao = $processo->tipoAcao?->descricao;
        $this->descricao = $tipoAcao
            ? "Contrato de honorÃ¡rios - {$tipoAcao} ({$processo->numero})"
            : "Contrato de honorÃ¡rios - processo {$processo->numero}";

        if (!$this->observacoes) {
            $clienteNome = $processo->cliente?->nome;
            $this->observacoes = $clienteNome
                ? "Contrato gerado a partir do processo {$processo->numero} do cliente {$clienteNome}."
                : "Contrato gerado a partir do processo {$processo->numero}.";
        }

        // Aplica o primeiro modelo de honorÃ¡rio_processo disponÃ­vel automaticamente
        $modeloPadrao = collect($this->modelos)->firstWhere('tipo', 'honorario_processo');
        if ($modeloPadrao) {
            $this->modeloId = $modeloPadrao['id'];
            $this->updatedModeloId();
        }
    }

    private function carregarOpcoesDoCliente(): void
    {
        if (!$this->clienteId) {
            $this->processosContrato = [];
            $this->processoContratoId = 0;
            return;
        }

        $this->processosContrato = DB::select("
            SELECT id, numero, COALESCE(parte_contraria, numero) AS titulo
            FROM processos
            WHERE cliente_id = ? AND status = 'Ativo'
            ORDER BY numero
        ", [$this->clienteId]);

        $processoIds = array_map(static fn ($processo) => (int) $processo->id, $this->processosContrato);

        if ($this->processoContratoId && !in_array($this->processoContratoId, $processoIds, true)) {
            $this->processoContratoId = 0;
        }

        $advogadosCliente = DB::select("
            SELECT p.id, p.nome
            FROM cliente_advogado ca
            JOIN pessoas p ON p.id = ca.advogado_id
            WHERE ca.cliente_id = ? AND p.ativo = true
            ORDER BY p.nome
        ", [$this->clienteId]);

        if (!empty($advogadosCliente)) {
            $this->advogados = $advogadosCliente;
        } else {
            $this->advogados = DB::select("
                SELECT p.id, p.nome
                FROM pessoas p
                JOIN pessoa_tipos pt ON pt.pessoa_id = p.id
                WHERE pt.tipo = 'Advogado' AND p.ativo = true
                ORDER BY p.nome
            ");
        }

        $advogadoIds = array_map(static fn ($advogado) => (int) $advogado->id, $this->advogados);

        if ($this->advogadoResponsavelId && !in_array($this->advogadoResponsavelId, $advogadoIds, true)) {
            $this->advogadoResponsavelId = 0;
        }
    }

    // â”€â”€ Abrir / fechar modal contrato â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function abrirModal(?int $id = null): void
    {
        $this->resetErrorBag();
        $this->contratoId = $id;
        $this->arquivo    = null;

        if ($id) {
            $c = Contrato::with('servicos')->findOrFail($id);
            $this->clienteId      = $c->cliente_id;
            $this->advogadoResponsavelId = (int) ($c->advogado_responsavel_id ?? 0);
            $this->tipo           = $c->tipo;
            $this->descricao      = $c->descricao;
            $this->processoContratoId = (int) ($c->processo_id ?? 0);
            $this->observacoes    = $c->observacoes ?? '';
            $this->formaCobranca  = $c->forma_cobranca;
            $this->valor          = number_format($c->valor, 2, ',', '.');
            $this->percentualExito = $c->percentual_exito ? number_format($c->percentual_exito, 2, ',', '.') : '';
            $this->diaVencimento  = $c->dia_vencimento ?? '';
            $this->dataInicio     = $c->data_inicio->format('Y-m-d');
            $this->dataFim        = $c->data_fim?->format('Y-m-d') ?? '';
            $this->status         = $c->status;
            $this->arquivoAtual   = $c->arquivo;
            $this->arquivoNome    = $c->arquivo_original;
            $this->modeloId       = (int) ($c->modelo_id ?? 0);
            $this->textoContrato  = $c->texto_contrato ?? '';
            $this->carregarOpcoesDoCliente();
        } else {
            $this->clienteId      = 0;
            $this->advogadoResponsavelId = 0;
            $this->tipo           = 'honorario_processo';
            $this->descricao      = '';
            $this->processoContratoId = 0;
            $this->observacoes    = '';
            $this->formaCobranca  = 'parcelado';
            $this->valor          = '';
            $this->percentualExito = '';
            $this->diaVencimento  = '';
            $this->dataInicio     = now()->format('Y-m-d');
            $this->dataFim        = '';
            $this->status         = 'ativo';
            $this->arquivoAtual   = null;
            $this->arquivoNome    = null;
            $this->modeloId       = 0;
            $this->textoContrato  = '';
            $this->processosContrato = [];
        }

        $this->modal = true;
    }

    public function fecharModal(): void
    {
        $this->modal      = false;
        $this->arquivo    = null;
        $this->resetErrorBag();
    }

    // â”€â”€ Salvar contrato â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function salvar(): void
    {
        $this->validate([
            'clienteId'    => 'required|integer|min:1',
            'advogadoResponsavelId' => 'required|integer|min:1',
            'tipo'         => 'required|string',
            'descricao'    => 'required|string|max:300',
            'formaCobranca'=> 'required|string',
            'valor'        => 'required',
            'dataInicio'   => 'required|date',
            'arquivo'      => 'nullable|file|max:20480',
        ], [
            'clienteId.min'    => 'Selecione o cliente.',
            'advogadoResponsavelId.min' => 'Selecione o advogado responsÃ¡vel.',
            'descricao.required' => 'A descriÃ§Ã£o Ã© obrigatÃ³ria.',
            'valor.required'   => 'Informe o valor.',
            'dataInicio.required' => 'Informe a data de inÃ­cio.',
        ]);

        $valorNum = (float) str_replace(['.', ','], ['', '.'], $this->valor);

        $dados = [
            'cliente_id'     => $this->clienteId,
            'advogado_responsavel_id' => $this->advogadoResponsavelId,
            'tipo'           => $this->tipo,
            'descricao'      => $this->descricao,
            'processo_id'    => $this->processoContratoId ?: null,
            'modelo_id'      => $this->modeloId ?: null,
            'texto_contrato' => $this->textoContrato ?: null,
            'observacoes'    => $this->observacoes ?: null,
            'forma_cobranca' => $this->formaCobranca,
            'valor'          => $valorNum,
            'percentual_exito' => $this->percentualExito ? (float) str_replace(',', '.', $this->percentualExito) : null,
            'dia_vencimento' => $this->diaVencimento ?: null,
            'data_inicio'    => $this->dataInicio,
            'data_fim'       => $this->dataFim ?: null,
            'status'         => $this->status,
        ];

        if ($this->arquivo) {
            if ($this->arquivoAtual) {
                Storage::disk('public')->delete($this->arquivoAtual);
            }
            $dados['arquivo']          = $this->arquivo->store('contratos', 'public');
            $dados['arquivo_original'] = $this->arquivo->getClientOriginalName();
        }

        if ($this->contratoId) {
            DB::table('contratos')->where('id', $this->contratoId)->update(array_merge($dados, ['updated_at' => now()]));
            if ($dados['status'] === 'ativo') {
                $contrato = Contrato::with('servicos')->find($this->contratoId);
                if ($contrato && $contrato->servicos->isNotEmpty()) {
                    FinanceiroLancamento::gerarDoContrato($contrato);
                }
            }

            $msg = 'Contrato atualizado.';
        } else {
            DB::table('contratos')->insertGetId(array_merge($dados, [
                'tenant_id'  => Auth::guard('usuarios')->user()?->tenant_id,
                'validado'   => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
            $msg = 'Contrato criado com sucesso! Agora adicione os serviÃ§os para gerar o financeiro.';
        }

        $this->fecharModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    // â”€â”€ Validar contrato (admin/financeiro) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function validar(int $id): void
    {
        if (!$this->podeValidar) return;
        $usuario = Auth::guard('usuarios')->user();
        DB::table('contratos')->where('id', $id)->update([
            'validado'    => true,
            'validado_em' => now(),
            'validado_por'=> $usuario->nome ?? 'Sistema',
            'updated_at'  => now(),
        ]);
        $this->dispatch('toast', message: 'Contrato validado!', type: 'success');
    }

    // â”€â”€ Abrir detalhe â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function abrirDetalhe(int $id): void
    {
        $this->contratoDetalhe = $id;
        $this->modalDetalhe    = true;
    }

    public function fecharDetalhe(): void
    {
        $this->modalDetalhe    = false;
        $this->contratoDetalhe = null;
    }

    // â”€â”€ ServiÃ§os â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function abrirServico(int $contratoId, ?int $servicoId = null): void
    {
        $this->resetErrorBag();
        $this->contratoIdServico  = $contratoId;
        $this->servicoId          = $servicoId;

        if ($servicoId) {
            $s = ContratoServico::findOrFail($servicoId);
            $this->servicoDescricao  = $s->descricao;
            $this->servicoTipo       = $s->tipo;
            $this->servicoValor      = number_format($s->valor, 2, ',', '.');
            $this->servicoPercentual = $s->percentual ? number_format($s->percentual, 2, ',', '.') : '';
            $this->servicoProcessoId = $s->processo_id ?? 0;
            $this->servicoObs        = $s->observacoes ?? '';
            $this->servicoVencimento = $s->vencimento?->format('Y-m-d') ?? '';
            $this->servicoParcelas   = $s->numero_parcelas ?? 1;
        } else {
            $contrato = Contrato::find($contratoId);
            $this->servicoTipo       = $contrato ? $this->tipoServicoPadraoParaContrato($contrato) : 'honorario';
            $this->servicoDescricao  = $this->descricaoServicoPadrao($this->servicoTipo);
            $this->servicoValor      = '';
            $this->servicoPercentual = '';
            $this->servicoProcessoId = (int) ($contrato->processo_id ?? 0);
            $this->servicoObs        = '';
            $this->servicoVencimento = now()->format('Y-m-d');
            $this->servicoParcelas   = 1;
        }

        $this->modalDetalhe = false;
        $this->modalServico = true;
    }

    public function fecharServico(): void
    {
        $this->modalServico = false;
        $this->resetErrorBag();
        if ($this->contratoDetalhe) $this->modalDetalhe = true;
    }

    public function salvarServico(): void
    {
        $semValor = in_array($this->servicoTipo, ['exito', 'repasse']);
        $this->validate([
            'servicoDescricao'  => 'required|string|max:300',
            'servicoTipo'       => 'required|string',
            'servicoValor'      => $semValor ? 'nullable' : 'required',
            'servicoPercentual' => $this->servicoTipo === 'exito' ? 'required' : 'nullable',
            'servicoVencimento' => $semValor ? 'nullable|date' : 'required|date',
            'servicoParcelas'   => 'integer|min:1|max:120',
        ], [
            'servicoDescricao.required'  => 'A descriÃ§Ã£o Ã© obrigatÃ³ria.',
            'servicoValor.required'      => 'Informe o valor.',
            'servicoPercentual.required' => 'Informe o percentual de Ãªxito.',
            'servicoVencimento.required' => 'Informe a data do primeiro vencimento.',
        ]);

        $valor = $this->servicoValor !== ''
            ? (float) str_replace(['.', ','], ['', '.'], $this->servicoValor)
            : 0.0;
        $perc  = $this->servicoPercentual ? (float) str_replace(',', '.', $this->servicoPercentual) : null;

        $dados = [
            'contrato_id'     => $this->contratoIdServico,
            'processo_id'     => $this->servicoProcessoId ?: null,
            'descricao'       => $this->servicoDescricao,
            'tipo'            => $this->servicoTipo,
            'valor'           => $valor,
            'percentual'      => $perc,
            'vencimento'      => $this->servicoVencimento ?: null,
            'numero_parcelas' => $this->servicoParcelas,
            'observacoes'     => $this->servicoObs ?: null,
            'status'          => 'ativo',
        ];

        if ($this->servicoId) {
            DB::table('contrato_servicos')->where('id', $this->servicoId)
                ->update(array_merge($dados, ['updated_at' => now()]));

            $servico = ContratoServico::find($this->servicoId);
            if ($servico) {
                FinanceiroLancamento::sincronizarServico($servico);
            }
        } else {
            $servicoId = DB::table('contrato_servicos')
                ->insertGetId(array_merge($dados, ['created_at' => now(), 'updated_at' => now()]));

            $servico = ContratoServico::find($servicoId);
            if ($servico) {
                FinanceiroLancamento::sincronizarServico($servico);
            }
        }

        $this->fecharServico();
        $msg = $this->servicoId
            ? 'Serviço atualizado e financeiro sincronizado!'
            : ($semValor ? 'Serviço salvo. Esse tipo não gera cobrança automática agora.' : 'Serviço salvo e financeiro gerado!');
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function excluirServico(int $id): void
    {
        DB::table('financeiro_lancamentos')
            ->where('contrato_servico_id', $id)
            ->whereIn('status', ['previsto', 'atrasado'])
            ->delete();

        DB::table('contrato_servicos')->where('id', $id)->delete();
        $this->dispatch('toast', message: 'Serviço removido e financeiro previsto excluído.', type: 'success');
    }

    public function abrirExito(int $servicoId): void
    {
        $servico = ContratoServico::with('contrato')->findOrFail($servicoId);

        $this->resetErrorBag();
        $this->exitoServicoId  = $servico->id;
        $this->exitoValor      = $servico->valor_realizado
            ? number_format((float) $servico->valor_realizado, 2, ',', '.')
            : ($servico->valor > 0 ? number_format((float) $servico->valor, 2, ',', '.') : '');
        $this->exitoVencimento = now()->format('Y-m-d');
        $this->exitoObs        = '';
        $this->modalDetalhe    = false;
        $this->modalExito      = true;
    }

    public function fecharExito(): void
    {
        $this->modalExito   = false;
        $this->exitoServicoId = null;
        $this->exitoValor     = '';
        $this->exitoVencimento = '';
        $this->exitoObs        = '';
        $this->resetErrorBag();

        if ($this->contratoDetalhe) {
            $this->modalDetalhe = true;
        }
    }

    public function realizarExito(): void
    {
        $this->validate([
            'exitoServicoId'  => 'required|integer|min:1',
            'exitoValor'      => 'required',
            'exitoVencimento' => 'required|date',
        ], [
            'exitoValor.required' => 'Informe o valor do êxito realizado.',
            'exitoVencimento.required' => 'Informe o vencimento da cobrança.',
        ]);

        $servico = ContratoServico::with('contrato')->findOrFail($this->exitoServicoId);
        $valor   = (float) str_replace(['.', ','], ['', '.'], $this->exitoValor);

        if ($servico->tipo !== 'exito') {
            $this->addError('exitoValor', 'Apenas serviços de êxito podem ser realizados por este fluxo.');
            return;
        }

        if ($valor <= 0) {
            $this->addError('exitoValor', 'Informe um valor maior que zero para realizar o êxito.');
            return;
        }

        $recebido = DB::table('financeiro_lancamentos')
            ->where('contrato_servico_id', $servico->id)
            ->where('status', 'recebido')
            ->exists();

        if ($recebido) {
            $this->addError('exitoValor', 'Este êxito já possui lançamento recebido. Ajuste manualmente no financeiro.');
            return;
        }

        DB::table('financeiro_lancamentos')
            ->where('contrato_servico_id', $servico->id)
            ->whereIn('status', ['previsto', 'atrasado'])
            ->delete();

        DB::table('financeiro_lancamentos')->insert([
            'tenant_id'           => $servico->contrato->tenant_id,
            'cliente_id'          => $servico->contrato->cliente_id,
            'contrato_id'         => $servico->contrato->id,
            'contrato_servico_id' => $servico->id,
            'processo_id'         => $servico->processo_id,
            'tipo'                => 'receita',
            'descricao'           => 'Êxito realizado - ' . $servico->descricao,
            'valor'               => $valor,
            'vencimento'          => $this->exitoVencimento,
            'status'              => 'previsto',
            'observacoes'         => $this->exitoObs ?: $servico->observacoes,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        DB::table('contrato_servicos')
            ->where('id', $servico->id)
            ->update([
                'valor_realizado' => $valor,
                'realizado_em'    => now(),
                'realizado_por'   => Auth::guard('usuarios')->user()?->nome ?? 'Sistema',
                'updated_at'      => now(),
            ]);

        $this->fecharExito();
        $this->dispatch('toast', message: 'Êxito realizado e cobrança gerada no financeiro.', type: 'success');
    }

    // â”€â”€ Repasses â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function abrirRepasse(int $contratoId, ?int $repasseId = null): void
    {
        $this->resetErrorBag();
        $this->repasseContratoId = $contratoId;
        $this->repasseId         = $repasseId;

        if ($repasseId) {
            $r = ContratoRepasse::findOrFail($repasseId);
            $this->repasseIndicadorId  = $r->indicador_id;
            $this->repasseTipoCalculo  = $r->tipo_calculo;
            $this->repassePercentual   = $r->percentual ? number_format($r->percentual, 2, ',', '.') : '';
            $this->repasseValorFixo    = $r->valor_fixo  ? number_format($r->valor_fixo, 2, ',', '.') : '';
            $this->repasseFrequencia   = $r->frequencia;
        } else {
            $this->repasseIndicadorId  = 0;
            $this->repasseTipoCalculo  = 'percentual';
            $this->repassePercentual   = '';
            $this->repasseValorFixo    = '';
            $this->repasseFrequencia   = 'mensal';
        }

        $this->modalDetalhe = false;
        $this->modalRepasse = true;
    }

    public function fecharRepasse(): void
    {
        $this->modalRepasse = false;
        $this->resetErrorBag();
        if ($this->contratoDetalhe) $this->modalDetalhe = true;
    }

    public function salvarRepasse(): void
    {
        $this->validate([
            'repasseIndicadorId' => 'required|integer|min:1',
            'repasseTipoCalculo' => 'required|string',
            'repasseFrequencia'  => 'required|string',
        ], [
            'repasseIndicadorId.min' => 'Selecione o indicador.',
        ]);

        $dados = [
            'contrato_id'   => $this->repasseContratoId,
            'indicador_id'  => $this->repasseIndicadorId,
            'tipo_calculo'  => $this->repasseTipoCalculo,
            'percentual'    => $this->repasseTipoCalculo === 'percentual'
                ? (float) str_replace(',', '.', $this->repassePercentual) : null,
            'valor_fixo'    => $this->repasseTipoCalculo === 'fixo'
                ? (float) str_replace(['.', ','], ['', '.'], $this->repasseValorFixo) : null,
            'frequencia'    => $this->repasseFrequencia,
            'status'        => 'ativo',
        ];

        if ($this->repasseId) {
            DB::table('contrato_repasses')->where('id', $this->repasseId)
                ->update(array_merge($dados, ['updated_at' => now()]));
        } else {
            DB::table('contrato_repasses')
                ->insert(array_merge($dados, ['created_at' => now(), 'updated_at' => now()]));

            // Gerar lanÃ§amentos de repasse para os lanÃ§amentos jÃ¡ existentes do contrato
            $this->gerarLancamentosRepasse($this->repasseContratoId, $dados);
        }

        $this->fecharRepasse();
        $this->dispatch('toast', message: 'Repasse salvo!', type: 'success');
    }

    private function gerarLancamentosRepasse(int $contratoId, array $repasse): void
    {
        $contrato = Contrato::find($contratoId);
        if (!$contrato) return;

        // Buscar lanÃ§amentos de receita deste contrato ainda nÃ£o liquidados
        $lancamentos = DB::table('financeiro_lancamentos')
            ->where('contrato_id', $contratoId)
            ->where('tipo', 'receita')
            ->get();

        foreach ($lancamentos as $lanc) {
            $valor = $repasse['tipo_calculo'] === 'percentual'
                ? round($lanc->valor * ($repasse['percentual'] / 100), 2)
                : ($repasse['valor_fixo'] ?? 0);

            if ($valor <= 0) continue;

            // Buscar nome do indicador
            $indicador = DB::table('pessoas')->where('id', $repasse['indicador_id'])->value('nome');

            DB::table('financeiro_lancamentos')->insert([
                'tenant_id'   => $contrato->tenant_id,
                'cliente_id'  => $contrato->cliente_id,
                'contrato_id' => $contratoId,
                'tipo'        => 'repasse',
                'descricao'   => "Repasse â€” {$indicador} â€” " . \Carbon\Carbon::parse($lanc->vencimento)->format('m/Y'),
                'valor'       => $valor,
                'vencimento'  => $lanc->vencimento,
                'status'      => 'previsto',
                'numero_parcela' => $lanc->numero_parcela,
                'total_parcelas' => $lanc->total_parcelas,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function excluirRepasse(int $id): void
    {
        DB::table('contrato_repasses')->where('id', $id)->delete();
        $this->dispatch('toast', message: 'Repasse removido.', type: 'success');
    }

    // â”€â”€ Encerrar contrato â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function encerrar(int $id): void
    {
        DB::table('contratos')->where('id', $id)->update(['status' => 'encerrado', 'updated_at' => now()]);

        // Cancela lançamentos futuros ainda não recebidos
        $cancelados = DB::table('financeiro_lancamentos')
            ->where('contrato_id', $id)
            ->whereIn('status', ['previsto', 'atrasado'])
            ->where('vencimento', '>', now()->toDateString())
            ->update(['status' => 'cancelado', 'updated_at' => now()]);

        $msg = 'Contrato encerrado.';
        if ($cancelados > 0) {
            $msg .= " {$cancelados} lançamento(s) futuro(s) cancelado(s) automaticamente.";
        }

        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function updatingBusca(): void      { $this->resetPage(); }
    public function updatingFiltroTipo(): void  { $this->resetPage(); }
    public function updatingFiltroStatus(): void { $this->resetPage(); }

    // â”€â”€ Render â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
    public function render(): \Illuminate\View\View
    {
        $contratos = Contrato::with(['cliente', 'servicos', 'advogadoResponsavel', 'processo'])
            ->when($this->busca, fn($q) => $q->whereHas('cliente', fn($c) =>
                $c->where('nome', 'ilike', "%{$this->busca}%")
            )->orWhere('descricao', 'ilike', "%{$this->busca}%"))
            ->when($this->filtroTipo,   fn($q) => $q->where('tipo', $this->filtroTipo))
            ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
            ->orderByDesc('created_at')
            ->paginate(15);

        // MÃ©tricas
        $totalAtivos     = Contrato::where('status', 'ativo')->count();
        $totalValor      = Contrato::where('status', 'ativo')->sum('valor');
        $totalNaoValid   = Contrato::where('status', 'ativo')->where('validado', false)->count();

        $detalhe = null;
        if ($this->contratoDetalhe) {
            $detalhe = Contrato::with(['cliente', 'advogadoResponsavel', 'processo', 'servicos.processo', 'repasses.indicador'])
                ->find($this->contratoDetalhe);
        }

        return view('livewire.contratos', [
            'contratos'     => $contratos,
            'totalAtivos'   => $totalAtivos,
            'totalValor'    => $totalValor,
            'totalNaoValid' => $totalNaoValid,
            'tiposLabels'   => Contrato::tiposLabels(),
            'formasLabels'  => Contrato::formasLabels(),
            'servicosTipos' => ContratoServico::tiposLabels(),
            'servicosContexto' => $this->contextoTiposServico(),
            'detalhe'       => $detalhe,
        ]);
    }
}
