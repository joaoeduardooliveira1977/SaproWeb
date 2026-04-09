<?php

namespace App\Livewire\Processos;

use App\Models\Monitoramento as MonitoramentoModel;
use App\Jobs\VerificarProcessoDatajud;
use App\Models\{LoteVerificacao, Notificacao, Processo};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Monitoramento extends Component
{
    use WithFileUploads, WithPagination;

    // ── Aba ativa ─────────────────────────────────────
    public string $aba = 'feed'; // feed | lote | monitoramentos | historico

    protected $queryString = [
        'aba' => ['except' => 'feed'],
    ];

    // ── Aba 1: Feed ───────────────────────────────────
    public string $filtroPrazo = 'todos'; // todos | critico | atencao | normal

    // ── Aba 2: Verificar em Lote ──────────────────────
    public ?object $fileLote     = null;
    public string  $numerosBrutos = '';

    // ── Aba 3: Monitoramentos ─────────────────────────
    public bool   $modalMonitoramento = false;
    public string $buscaMonitoramento = '';
    public ?int   $processoMonitId    = null;
    public string $frequenciaSelect   = 'diario';

    // ── Aba 4: Histórico ──────────────────────────────
    public string $filtroHistorico = 'todos'; // todos | sentencas | prazos | andamentos | decisoes
    public int    $pagina          = 1;

    // ── Notificações ──────────────────────────────────
    public bool $painelNotificacoes = false;

    // ── Última atualização ────────────────────────────
    public string $ultimaAtualizacao = '';

    public function mount(): void
    {
        $this->ultimaAtualizacao = now()->toDateTimeString();
    }




	// Outros Filtros
	public bool   $painelFiltros    = false;
	public string $buscaFeed        = '';
	public string $filtroNumero     = '';
	public string $filtroStatus     = 'Ativo';
	public string $filtroAdvogado   = '';
	public string $filtroFase       = '';
	public string $dataInicio       = '';
	public string $dataFim          = '';



    // ── Listeners ─────────────────────────────────────
    protected $listeners = ['abrirProcesso'];

    public function updatingAba(): void
    {
        $this->resetPage();
        $this->pagina = 1;
    }

    // ─────────────────────────────────────────────────
    //  ABA 1 — FEED
    // ─────────────────────────────────────────────────

    public function setFiltroFeed(string $filtro): void
    {
        $this->filtroPrazo = $filtro;
        $this->resetPage();
    }

    public function abrirProcesso(int $id): void
    {
        $this->redirect(route('processos.show', $id));
    }

    // ─────────────────────────────────────────────────
    //  ABA 2 — VERIFICAR EM LOTE
    // ─────────────────────────────────────────────────

    public function verificarLote(): void
    {
        $linhas = preg_split('/\r\n|\r|\n/', trim($this->numerosBrutos));
        $linhas = array_filter(array_map('trim', $linhas));
        $linhas = array_unique($linhas);

        if (count($linhas) > 500) {
            $linhas = array_slice($linhas, 0, 500);
            $this->dispatch('toast', message: 'Limite de 500 processos. Apenas os primeiros 500 foram enviados.', type: 'warning');
        }

        if (empty($linhas)) {
            $this->dispatch('toast', message: 'Informe ao menos um número de processo.', type: 'error');
            return;
        }

        $userId = Auth::id();

        foreach ($linhas as $numero) {
            $lote = LoteVerificacao::create([
                'processo_numero' => $numero,
                'status'          => 'aguardando',
                'user_id'         => $userId,
            ]);

            VerificarProcessoDatajud::dispatch($numero, $lote->id);
        }

        $this->numerosBrutos = '';
        $this->dispatch('toast', message: count($linhas) . ' processo(s) enviado(s) para verificação.', type: 'success');
    }

    // ─────────────────────────────────────────────────
    //  ABA 3 — MONITORAMENTOS
    // ─────────────────────────────────────────────────

    public function abrirModalMonitoramento(): void
    {
        $this->modalMonitoramento = true;
        $this->buscaMonitoramento = '';
        $this->processoMonitId    = null;
        $this->frequenciaSelect   = 'diario';
    }

    public function fecharModalMonitoramento(): void
    {
        $this->modalMonitoramento = false;
    }

    public function selecionarProcessoMonit(int $id): void
    {
        $this->processoMonitId = $id;
    }

    public function confirmarMonitoramento(): void
{
    if (!$this->processoMonitId) {
        $this->dispatch('toast', message: 'Selecione um processo.', type: 'error');
        return;
    }

    $processo = Processo::findOrFail($this->processoMonitId);

    MonitoramentoModel::firstOrCreate(
        ['processo_id' => $processo->id],
        [
            'numero_processo' => $processo->numero,
            'ativo'           => true,
            'notificar_email' => false,
            'tenant_id'       => $processo->tenant_id ?? null,
        ]
    );

    MonitoramentoModel::where('processo_id', $processo->id)
        ->update(['ativo' => true]);

    $this->modalMonitoramento = false;
    $this->dispatch('toast', message: 'Monitoramento ativado.', type: 'success');
}


    public function toggleMonitoramento(int $id): void
	{
    $mon = MonitoramentoModel::findOrFail($id);
    $mon->update(['ativo' => !$mon->ativo]);
    $this->dispatch('toast', message: $mon->ativo ? 'Monitoramento ativado.' : 'Monitoramento pausado.', type: 'success');
	}

    // ─────────────────────────────────────────────────
    //  ABA 4 — HISTÓRICO
    // ─────────────────────────────────────────────────

    public function setFiltroHistorico(string $filtro): void
    {
        $this->filtroHistorico = $filtro;
        $this->pagina          = 1;
    }

    public function carregarMais(): void
    {
        $this->pagina++;
    }

    // ─────────────────────────────────────────────────
    //  NOTIFICAÇÕES
    // ─────────────────────────────────────────────────

    public function atualizarAgora(): void
    {
        $this->ultimaAtualizacao = now()->toDateTimeString();
        $this->dispatch('toast', message: 'Notificações atualizadas.', type: 'success');
    }


    // ─────────────────────────────────────────────────
    //  OUTROS FILTROS
    // ─────────────────────────────────────────────────

	public function toggleFiltros(): void
	{
    		$this->painelFiltros = !$this->painelFiltros;
	}

	public function limparFiltros(): void
	{
    		$this->buscaFeed      = '';
    		$this->filtroNumero   = '';
    		$this->filtroStatus   = 'Ativo';
    		$this->filtroAdvogado = '';
    		$this->filtroFase     = '';
    		$this->dataInicio     = '';
    		$this->dataFim        = '';
	}



    // ─────────────────────────────────────────────────
    //  RENDER
    // ─────────────────────────────────────────────────

   public function render()
{
    $userId = auth('usuarios')->id();

   
$feedQuery = Processo::with(['cliente', 'andamentos' => fn($q) => $q->latest()->limit(1)])
    ->when($this->filtroStatus, fn($q) => $q->where('status', $this->filtroStatus))
    ->when($this->filtroPrazo !== 'todos', fn($q) => $q->where('score', $this->filtroPrazo))
    ->when($this->buscaFeed, fn($q) => $q
        ->where('numero', 'ilike', "%{$this->buscaFeed}%")
        ->orWhereHas('cliente', fn($c) => $c->where('nome', 'ilike', "%{$this->buscaFeed}%"))
    )
    ->when($this->filtroNumero, fn($q) => $q->where('numero', 'ilike', "%{$this->filtroNumero}%"))
    ->when($this->filtroAdvogado, fn($q) => $q->where('advogado_id', $this->filtroAdvogado))
    ->when($this->filtroFase, fn($q) => $q->where('fase_id', $this->filtroFase))
    ->when($this->dataInicio, fn($q) => $q->whereHas('andamentos', fn($a) => $a->whereDate('data', '>=', $this->dataInicio)))
    ->when($this->dataFim, fn($q) => $q->whereHas('andamentos', fn($a) => $a->whereDate('data', '<=', $this->dataFim)))
    ->whereHas('andamentos')
    ->latest('updated_at')
    ->paginate(15);



    $filaLote = LoteVerificacao::where('user_id', $userId)
        ->latest()
        ->limit(100)
        ->get();

    $temVerificando = $filaLote->contains('status', 'verificando')
                   || $filaLote->contains('status', 'aguardando');

    $processosBusca = [];
    if ($this->modalMonitoramento && strlen($this->buscaMonitoramento) >= 2) {
        $processosBusca = Processo::where('status', 'Ativo')
            ->where(fn($q) => $q
                ->where('numero', 'like', "%{$this->buscaMonitoramento}%")
                ->orWhereHas('cliente', fn($q2) => $q2->where('nome', 'like', "%{$this->buscaMonitoramento}%"))
            )
            ->limit(10)
            ->get();
    }

    $monitorados = MonitoramentoModel::with('processo.cliente')
    ->where('ativo', true)
    ->orderBy('updated_at', 'desc')
    ->get();

    $historico = \App\Models\Andamento::with(['processo.cliente'])
        ->when($this->filtroHistorico === 'sentencas',  fn($q) => $q->where('descricao', 'ilike', '%sentença%')->orWhere('descricao', 'ilike', '%acórdão%'))
	->when($this->filtroHistorico === 'prazos',     fn($q) => $q->where('descricao', 'ilike', '%prazo%')->orWhere('descricao', 'ilike', '%intimação%'))
	->when($this->filtroHistorico === 'andamentos', fn($q) => $q->where('descricao', 'ilike', '%petição%')->orWhere('descricao', 'ilike', '%publicação%'))
	->when($this->filtroHistorico === 'decisoes',   fn($q) => $q->where('descricao', 'ilike', '%decisão%')->orWhere('descricao', 'ilike', '%conclusão%'))
        ->latest()
        ->paginate(20 * $this->pagina);

    $notificacoes = Notificacao::where(function ($q) use ($userId) {
            $q->where('usuario_id', $userId)
              ->orWhereNull('usuario_id');
        })
        ->latest()
        ->limit(50)
        ->get();

	$notificacoesNaoLidas = $notificacoes->where('lida', false)->count();
	$totalCriticos = Processo::where('status', 'Ativo')->where('score', 'critico')->count();
	$totalAtencao  = Processo::where('status', 'Ativo')->where('score', 'atencao')->count();
	$totalNormal   = Processo::where('status', 'Ativo')->where('score', 'normal')->count();
	$totalPrazos    = \App\Models\Prazo::where('status', 'aberto')->whereDate('data_prazo', '<=', now()->addDays(7))->count();
	$totalAndamentos = $feedQuery->total();
	$totalAtivos    = Processo::where('status', 'Ativo')->count();
	$advogados = \App\Models\Usuario::orderBy('nome')->get();
	$fases = \App\Models\Fase::orderBy('descricao')->get();

    return view('livewire.processos.monitoramento', compact(
    'feedQuery',
    'filaLote',
    'temVerificando',
    'processosBusca',
    'monitorados',
    'historico',
    'notificacoes',
    'notificacoesNaoLidas',
    'totalCriticos',
    'totalPrazos',
    'totalAndamentos',
    'totalAtivos',
    'totalAtencao',
    'totalNormal',
    'advogados',
    'fases',

))->extends('layouts.app')->section('content');

}
}