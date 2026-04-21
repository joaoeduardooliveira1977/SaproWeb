<?php

namespace App\Livewire;

use App\Models\{Contrato, ContratoServico, ContratoRepasse, FinanceiroLancamento, Pessoa, Processo};
use Illuminate\Support\Facades\{Auth, DB, Storage};
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class Contratos extends Component
{
    use WithPagination, WithFileUploads;

    // ── Filtros ───────────────────────────────────────────────
    public string $busca          = '';
    public string $filtroTipo     = '';
    public string $filtroStatus   = 'ativo';

    protected $queryString = [
        'busca'        => ['except' => ''],
        'filtroTipo'   => ['except' => ''],
        'filtroStatus' => ['except' => 'ativo'],
    ];

    // ── Modal principal ───────────────────────────────────────
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

    // ── Serviços (itens do contrato) ──────────────────────────
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

    // ── Modal detalhe (visualizar contrato) ───────────────────
    public bool   $modalDetalhe    = false;
    public ?int   $contratoDetalhe = null;

    // ── Validação (admin/financeiro) ──────────────────────────
    public bool   $podeValidar    = false;

    // ── Repasses ──────────────────────────────────────────────
    public bool   $modalRepasse      = false;
    public ?int   $repasseId         = null;
    public ?int   $repasseContratoId = null;
    public int    $repasseIndicadorId = 0;
    public string $repasseTipoCalculo = 'percentual';
    public string $repassePercentual  = '';
    public string $repasseValorFixo   = '';
    public string $repasseFrequencia  = 'mensal';

    // ── Dados auxiliares ──────────────────────────────────────
    public array  $clientes    = [];
    public array  $processos   = [];
    public array  $processosContrato = [];
    public array  $advogados   = [];
    public array  $indicadores = []; // pessoas que podem receber repasse

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

        // Indicadores: qualquer pessoa ativa (síndico, corretor, advogado parceiro)
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
    }

    public function updatedClienteId(): void
    {
        $this->carregarOpcoesDoCliente();
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

        if (!$this->servicoDescricao || str_starts_with($this->servicoDescricao, 'Serviço:')) {
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
            'consultoria' => 'Serviço: mensalidade de assessoria',
            'exito'       => 'Serviço: honorários de êxito',
            'avulso'      => 'Serviço: atendimento avulso',
            'repasse'     => 'Serviço: repasse financeiro',
            'outro'       => 'Serviço: ajuste complementar',
            default       => 'Serviço: parcela de honorários',
        };
    }

    private function contextoTiposServico(): array
    {
        return [
            'honorario' => [
                'descricao'   => 'Use para entrada, parcelas ou honorários fixos do contrato.',
                'label_valor' => 'Valor da parcela (R$) *',
                'placeholder' => 'Ex: Entrada contratual ou Parcela 1/3',
            ],
            'consultoria' => [
                'descricao'   => 'Use para contratos mensais ou assessoria recorrente.',
                'label_valor' => 'Valor mensal (R$) *',
                'placeholder' => 'Ex: Mensalidade de assessoria jurídica',
            ],
            'exito' => [
                'descricao'   => 'Use para honorários condicionados ao ganho. Informe o percentual e, se quiser, um valor-base estimado.',
                'label_valor' => 'Valor-base estimado (R$)',
                'placeholder' => 'Ex: Honorários sobre êxito da ação',
            ],
            'avulso' => [
                'descricao'   => 'Use para serviços pontuais cobrados uma única vez.',
                'label_valor' => 'Valor do serviço (R$) *',
                'placeholder' => 'Ex: Elaboração de parecer ou reunião extraordinária',
            ],
            'repasse' => [
                'descricao'   => 'Use apenas se o contrato precisar registrar um serviço ligado a repasse específico.',
                'label_valor' => 'Valor do repasse (R$) *',
                'placeholder' => 'Ex: Repasse operacional',
            ],
            'outro' => [
                'descricao'   => 'Use para ajustes complementares fora dos tipos principais.',
                'label_valor' => 'Valor do ajuste (R$) *',
                'placeholder' => 'Ex: Complemento de honorários',
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
            ? "Contrato de honorários - {$tipoAcao} ({$processo->numero})"
            : "Contrato de honorários - processo {$processo->numero}";

        if (!$this->observacoes) {
            $clienteNome = $processo->cliente?->nome;
            $this->observacoes = $clienteNome
                ? "Contrato gerado a partir do processo {$processo->numero} do cliente {$clienteNome}."
                : "Contrato gerado a partir do processo {$processo->numero}.";
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

    // ── Abrir / fechar modal contrato ─────────────────────────
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

    // ── Salvar contrato ───────────────────────────────────────
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
            'advogadoResponsavelId.min' => 'Selecione o advogado responsável.',
            'descricao.required' => 'A descrição é obrigatória.',
            'valor.required'   => 'Informe o valor.',
            'dataInicio.required' => 'Informe a data de início.',
        ]);

        $valorNum = (float) str_replace(['.', ','], ['', '.'], $this->valor);

        $dados = [
            'cliente_id'     => $this->clienteId,
            'advogado_responsavel_id' => $this->advogadoResponsavelId,
            'tipo'           => $this->tipo,
            'descricao'      => $this->descricao,
            'processo_id'    => $this->processoContratoId ?: null,
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
            $msg = 'Contrato atualizado.';
        } else {
            $novoId = DB::table('contratos')->insertGetId(array_merge($dados, [
                'tenant_id'  => Auth::guard('usuarios')->user()?->tenant_id,
                'validado'   => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]));

            // Gerar lançamentos financeiros automaticamente se contrato ativo
            if ($dados['status'] === 'ativo') {
                $contrato = Contrato::find($novoId);
                if ($contrato) {
                    FinanceiroLancamento::gerarDoContrato($contrato);
                }
            }

            $msg = 'Contrato criado com sucesso!';
        }

        $this->fecharModal();
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    // ── Validar contrato (admin/financeiro) ───────────────────
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

    // ── Abrir detalhe ─────────────────────────────────────────
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

    // ── Serviços ──────────────────────────────────────────────
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
            'servicoDescricao.required'  => 'A descrição é obrigatória.',
            'servicoValor.required'      => 'Informe o valor.',
            'servicoPercentual.required' => 'Informe o percentual de êxito.',
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
        } else {
            DB::table('contrato_servicos')
                ->insert(array_merge($dados, ['created_at' => now(), 'updated_at' => now()]));

            if (!$semValor && $this->servicoVencimento && $valor > 0) {
                $this->gerarLancamentosServico($dados);
            }
        }

        $this->fecharServico();
        $msg = $this->servicoId ? 'Serviço atualizado!' : 'Serviço salvo e lançamentos gerados!';
        $this->dispatch('toast', message: $msg, type: 'success');
    }

    private function gerarLancamentosServico(array $servico): void
    {
        $contrato  = Contrato::findOrFail($servico['contrato_id']);
        $tenantId  = Auth::guard('usuarios')->user()?->tenant_id;
        $parcelas  = max(1, (int) $servico['numero_parcelas']);
        $valorParc = round($servico['valor'] / $parcelas, 2);
        $vencimento = \Carbon\Carbon::parse($servico['vencimento']);

        $tipoLanc = $servico['tipo'] === 'consultoria' ? 'receita' : 'receita';

        for ($i = 1; $i <= $parcelas; $i++) {
            $venc = $parcelas === 1
                ? $vencimento
                : $vencimento->copy()->addMonths($i - 1);

            DB::table('financeiro_lancamentos')->insert([
                'tenant_id'       => $tenantId,
                'cliente_id'      => $contrato->cliente_id,
                'contrato_id'     => $contrato->id,
                'processo_id'     => $servico['processo_id'] ?? null,
                'tipo'            => $tipoLanc,
                'descricao'       => $parcelas > 1
                    ? $servico['descricao'] . " ({$i}/{$parcelas})"
                    : $servico['descricao'],
                'valor'           => $valorParc,
                'vencimento'      => $venc->format('Y-m-d'),
                'status'          => 'previsto',
                'numero_parcela'  => $parcelas > 1 ? $i : null,
                'total_parcelas'  => $parcelas > 1 ? $parcelas : null,
                'observacoes'     => $servico['observacoes'] ?? null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }

    public function excluirServico(int $id): void
    {
        DB::table('contrato_servicos')->where('id', $id)->delete();
        $this->dispatch('toast', message: 'Serviço removido.', type: 'success');
    }

    // ── Repasses ──────────────────────────────────────────────

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

            // Gerar lançamentos de repasse para os lançamentos já existentes do contrato
            $this->gerarLancamentosRepasse($this->repasseContratoId, $dados);
        }

        $this->fecharRepasse();
        $this->dispatch('toast', message: 'Repasse salvo!', type: 'success');
    }

    private function gerarLancamentosRepasse(int $contratoId, array $repasse): void
    {
        $contrato = Contrato::find($contratoId);
        if (!$contrato) return;

        // Buscar lançamentos de receita deste contrato ainda não liquidados
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
                'descricao'   => "Repasse — {$indicador} — " . \Carbon\Carbon::parse($lanc->vencimento)->format('m/Y'),
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

    // ── Encerrar contrato ─────────────────────────────────────
    public function encerrar(int $id): void
    {
        DB::table('contratos')->where('id', $id)->update(['status' => 'encerrado', 'updated_at' => now()]);
        $this->dispatch('toast', message: 'Contrato encerrado.', type: 'success');
    }

    public function updatingBusca(): void      { $this->resetPage(); }
    public function updatingFiltroTipo(): void  { $this->resetPage(); }
    public function updatingFiltroStatus(): void { $this->resetPage(); }

    // ── Render ────────────────────────────────────────────────
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

        // Métricas
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
