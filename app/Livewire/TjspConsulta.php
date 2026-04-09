<?php

namespace App\Livewire;

use App\Jobs\VerificarAndamentosTjsp;
use App\Models\Andamento;
use App\Models\ConsultaHistorico;
use App\Models\Monitoramento;
use App\Models\Notificacao;
use App\Models\Processo;
use App\Models\Pessoa;
use App\Models\TjspVerificacao;
use App\Services\TribunalService;
use Livewire\Component;

class TjspConsulta extends Component
{
    // ── Estado geral ─────────────────────────────────────────────
    public string  $abaAtiva       = 'verificar';
    public ?int    $verificacaoId  = null;
    public bool    $consultando    = false;

    // ── Filtros da verificação em lote ────────────────────────────
    public string $filtroCliente    = '';
    public string $filtroNumero     = '';
    public string $filtroFase       = '';
    public string $filtroAdvogado   = '';
    public string $filtroStatus     = 'Ativo';
    public string $filtroConsulta   = '';
    public string $filtroDataIni    = '';
    public string $filtroDataFim    = '';

    protected $queryString = [
        'filtroCliente'  => ['except' => ''],
        'filtroNumero'   => ['except' => ''],
        'filtroFase'     => ['except' => ''],
        'filtroAdvogado' => ['except' => ''],
        'filtroStatus'   => ['except' => 'Ativo'],
        'filtroConsulta' => ['except' => ''],
        'filtroDataIni'  => ['except' => ''],
        'filtroDataFim'  => ['except' => ''],
        'abaAtiva'       => ['except' => 'verificar'],
    ];

    // ── Consulta rápida ───────────────────────────────────────────
    public string  $numeroBusca       = '';
    public ?array  $resultadoConsulta = null;
    public bool    $consultandoNumero = false;
    public int     $andamentosSalvos  = 0;

    // ── Analista IA ───────────────────────────────────────────────
    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    // ── Ações de monitoramento ────────────────────────────────────
    public function trocarAba(string $aba): void
    {
        $this->abaAtiva          = $aba;
        $this->resultadoConsulta = null;
        $this->andamentosSalvos  = 0;
    }

    // ── Verificação em lote ───────────────────────────────────────
    public function iniciarVerificacao(): void
    {
        $this->consultando = true;

        try {
            TjspVerificacao::whereIn('status', ['pendente', 'rodando'])
                ->update(['status' => 'erro', 'concluido_em' => now()]);

            $processos = $this->queryProcessos();

            if ($processos->isEmpty()) {
                $this->dispatch('toast', message: 'Nenhum processo encontrado com os filtros selecionados.', type: 'error');
                return;
            }

            $service     = new TribunalService();
            $semTribunal = $processos->filter(
                fn($p) => $service->detectarTribunal($p->numero) === null
            );

            if ($semTribunal->count() === $processos->count()) {
                $this->dispatch('toast', message: 'Nenhum processo selecionado possui número CNJ reconhecível pelo DATAJUD.', type: 'error');
                return;
            }

            if ($semTribunal->isNotEmpty()) {
                $processos = $processos->diff($semTribunal);
            }

            $processoIds = $processos->pluck('id')->toArray();

            $verificacao = TjspVerificacao::create([
                'status'      => 'pendente',
                'total'       => count($processoIds),
                'processado'  => 0,
                'iniciado_em' => now(),
                'filtros'     => json_encode($processoIds),
            ]);

            $this->verificacaoId = $verificacao->id;
            VerificarAndamentosTjsp::dispatch($verificacao->id, $processoIds);

        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Erro ao iniciar a verificação: ' . $e->getMessage(), type: 'error');
            $this->verificacaoId = null;
        } finally {
            $this->consultando = false;
        }
    }

    // ── Consulta rápida (processo único) ──────────────────────────
    public function consultarNumero(): void
    {
        $numero = trim($this->numeroBusca);
        if (empty($numero)) return;

        $this->consultandoNumero = true;
        $this->resultadoConsulta = null;
        $this->andamentosSalvos  = 0;

        try {
            $service   = new TribunalService();
            $resultado = $service->consultarProcesso($numero);

            // Registra no histórico
            ConsultaHistorico::create([
                'numero_processo' => $numero,
                'tribunal'        => $resultado['tribunal'] ?? null,
                'usuario_id'      => auth('usuarios')->id(),
                'resultado_count' => count($resultado['andamentos'] ?? []),
            ]);

            if (!$resultado['sucesso']) {
                $this->dispatch('toast', message: $resultado['erro'] ?? 'Processo não encontrado no DATAJUD.', type: 'error');
            } else {
                $this->resultadoConsulta = $resultado;
            }

        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Erro na consulta: ' . $e->getMessage(), type: 'error');
        } finally {
            $this->consultandoNumero = false;
        }
    }

    public function salvarAndamentos(): void
    {
        if (!$this->resultadoConsulta) return;

        $processo = Processo::where('numero', $this->resultadoConsulta['numero'])->first();

        if (!$processo) {
            $this->dispatch('toast', message: 'Processo não encontrado no SAPRO. Cadastre o processo antes de importar andamentos.', type: 'error');
            return;
        }

        $salvos = 0;
        foreach ($this->resultadoConsulta['andamentos'] as $a) {
            if (!$a['data'] || !$a['descricao']) continue;

            $existe = Andamento::where('processo_id', $processo->id)
                ->whereDate('data', $a['data'])
                ->where('descricao', $a['descricao'])
                ->exists();

            if (!$existe) {
                Andamento::create([
                    'processo_id' => $processo->id,
                    'data'        => $a['data'],
                    'descricao'   => $a['descricao'],
                    'usuario_id'  => auth('usuarios')->id(),
                ]);
                $salvos++;
            }
        }

        $this->andamentosSalvos = $salvos;
        $total = count($this->resultadoConsulta['andamentos']);

        if ($salvos === 0) {
            $this->dispatch('toast', message: "Todos os {$total} andamentos já existem no processo.", type: 'info');
        } else {
            $this->dispatch('toast', message: "{$salvos} andamento(s) importado(s) com sucesso!", type: 'success');
        }
    }

    // ── Monitoramento ─────────────────────────────────────────────
    public function monitorar(string $numero, ?int $processoId = null): void
    {
        $numero   = trim($numero);
        $existente = Monitoramento::where('numero_processo', $numero)->first();

        if ($existente) {
            if (!$existente->ativo) {
                $existente->update(['ativo' => true]);
                $this->dispatch('toast', message: 'Monitoramento reativado!', type: 'success');
            } else {
                $this->dispatch('toast', message: 'Processo já está sendo monitorado.', type: 'info');
            }
            return;
        }

        $tribunalData = (new TribunalService())->detectarTribunal($numero);

        Monitoramento::create([
            'processo_id'     => $processoId,
            'numero_processo' => $numero,
            'tribunal'        => $tribunalData['nome'] ?? null,
            'ativo'           => true,
            'notificar_email' => true,
        ]);

        $this->dispatch('toast', message: '📡 Processo adicionado ao monitoramento automático!', type: 'success');
    }

    public function toggleMonitoramento(int $id): void
    {
        $mon = Monitoramento::findOrFail($id);
        $mon->update(['ativo' => !$mon->ativo]);
        $status = $mon->ativo ? 'Ativo' : 'Pausado';
        $this->dispatch('toast', message: "Monitoramento {$status}.", type: 'success');
    }

    public function toggleEmail(int $id): void
    {
        $mon = Monitoramento::findOrFail($id);
        $mon->update(['notificar_email' => !$mon->notificar_email]);
    }

    public function removerMonitoramento(int $id): void
    {
        Monitoramento::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Monitoramento removido.', type: 'success');
    }

    // ── Analista IA ───────────────────────────────────────────────
    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $totalAtivos      = Processo::where('status', 'Ativo')->count();
        $nuncaConsultados = Processo::where('status', 'Ativo')->whereNull('tjsp_ultima_consulta')->count();
        $consultadosHoje  = Processo::where('status', 'Ativo')->whereDate('tjsp_ultima_consulta', today())->count();
        $ultimaVerif      = TjspVerificacao::where('status', 'concluido')->latest()->first();
        $monitoramentos   = Monitoramento::where('ativo', true)->count();

        $contexto = "Você é um assistente jurídico do sistema SAPRO. Responda de forma objetiva em português.

Dados da Consulta Judicial:
- Total de processos ativos: {$totalAtivos}
- Nunca consultados no DATAJUD: {$nuncaConsultados}
- Consultados hoje: {$consultadosHoje}
- Processos monitorados automaticamente: {$monitoramentos}
- Última verificação: " . ($ultimaVerif ? $ultimaVerif->concluido_em->format('d/m/Y H:i') . " ({$ultimaVerif->novos_total} andamentos novos)" : 'Nenhuma ainda') . "

Pergunta: {$this->perguntaIA}

Responda em 1-3 frases objetivas.";

        $resposta = app(\App\Services\AIService::class)->gerar($contexto, 300);

        if ($resposta === '__IA_BLOQUEADA__') {
            $this->respostaIA = 'IA disponível nos planos Starter e Pro. Faça upgrade para acessar este recurso.';
            return;
        }

        $this->respostaIA = $resposta ?? 'IA temporariamente indisponível.';
    }

    public function limparIA(): void
    {
        $this->perguntaIA = '';
        $this->respostaIA = null;
    }

    public function limparFiltros(): void
    {
        $this->filtroCliente  = '';
        $this->filtroNumero   = '';
        $this->filtroFase     = '';
        $this->filtroAdvogado = '';
        $this->filtroStatus   = 'Ativo';
        $this->filtroConsulta = '';
        $this->filtroDataIni  = '';
        $this->filtroDataFim  = '';
    }

    // ── Render ────────────────────────────────────────────────────
    public function render()
    {
        $verificacao = $this->verificacaoId
            ? TjspVerificacao::find($this->verificacaoId)
            : TjspVerificacao::latest()->first();

        if ($verificacao) {
            $this->verificacaoId = $verificacao->id;
        }

        $totalFiltrado = $this->queryProcessos()->count();

        $fases     = \App\Models\Fase::orderBy('descricao')->get();
        $advogados = Pessoa::doTipo('Advogado')->orderBy('nome')->get();

        $metricas = [
            'total_ativos'      => Processo::where('status', 'Ativo')->count(),
            'nunca_consultados' => Processo::where('status', 'Ativo')->whereNull('tjsp_ultima_consulta')->count(),
            'consultados_hoje'  => Processo::where('status', 'Ativo')->whereDate('tjsp_ultima_consulta', today())->count(),
            'novos_andamentos'  => TjspVerificacao::where('status', 'concluido')->whereDate('concluido_em', today())->sum('novos_total'),
        ];

        $monitoramentos      = Monitoramento::with('processo')
            ->latest()
            ->take(100)
            ->get();
        $monitoramentosAtivos = $monitoramentos->where('ativo', true)->count();

        $consultasRecentes = ConsultaHistorico::with('usuario')
            ->latest()
            ->take(10)
            ->get();

        // Processo correspondente ao resultado da consulta rápida
        $processoDoResultado = null;
        if ($this->resultadoConsulta) {
            $processoDoResultado = Processo::where('numero', $this->resultadoConsulta['numero'])->first();
        }

        return view('livewire.tjsp-consulta', [
            'verificacao'          => $verificacao,
            'totalFiltrado'        => $totalFiltrado,
            'fases'                => $fases,
            'advogados'            => $advogados,
            'metricas'             => $metricas,
            'monitoramentos'       => $monitoramentos,
            'monitoramentosAtivos' => $monitoramentosAtivos,
            'consultasRecentes'    => $consultasRecentes,
            'processoDoResultado'  => $processoDoResultado,
        ]);
    }

    // ── Helpers privados ──────────────────────────────────────────
    private function queryProcessos()
    {
        return Processo::with(['cliente', 'fase', 'advogado'])
            ->when($this->filtroStatus, fn($q) =>
                $q->where('status', $this->filtroStatus)
            )
            ->when($this->filtroCliente, fn($q) =>
                $q->whereHas('cliente', fn($q2) =>
                    $q2->where('nome', 'ilike', "%{$this->filtroCliente}%")
                )
            )
            ->when($this->filtroFase, fn($q) =>
                $q->whereHas('fase', fn($q2) =>
                    $q2->where('descricao', 'ilike', "%{$this->filtroFase}%")
                )
            )
            ->when($this->filtroAdvogado, fn($q) =>
                $q->whereHas('advogado', fn($q2) =>
                    $q2->where('nome', 'ilike', "%{$this->filtroAdvogado}%")
                )
            )
            ->when($this->filtroConsulta === 'nunca', fn($q) =>
                $q->whereNull('tjsp_ultima_consulta')
            )
            ->when($this->filtroConsulta === 'hoje', fn($q) =>
                $q->whereDate('tjsp_ultima_consulta', today())
            )
            ->when($this->filtroConsulta === 'semana', fn($q) =>
                $q->where('tjsp_ultima_consulta', '<', now()->subWeek())
                  ->orWhereNull('tjsp_ultima_consulta')
            )
            ->when($this->filtroConsulta === 'mes', fn($q) =>
                $q->where('tjsp_ultima_consulta', '<', now()->subMonth())
                  ->orWhereNull('tjsp_ultima_consulta')
            )
            ->when($this->filtroNumero, fn($q) =>
                $q->where('numero', 'ilike', "%{$this->filtroNumero}%")
            )
            ->when($this->filtroDataIni, fn($q) =>
                $q->where('tjsp_ultima_consulta', '>=', $this->filtroDataIni)
            )
            ->when($this->filtroDataFim, fn($q) =>
                $q->where('tjsp_ultima_consulta', '<=', $this->filtroDataFim . ' 23:59:59')
            )
            ->get();
    }
}
