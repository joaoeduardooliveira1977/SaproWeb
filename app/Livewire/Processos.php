<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Processo, Fase, GrauRisco};
use Illuminate\Support\Facades\Auth;

class Processos extends Component
{
    use WithPagination;

    public string  $busca      = '';
    public string  $status     = '';
    public string  $fase_id    = '';
    public string  $risco_id   = '';
    public bool    $confirmandoExclusao = false;
    public ?int    $processoParaExcluir = null;

    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    protected $queryString = ['busca', 'status', 'fase_id', 'risco_id'];

    public function updatingBusca():   void { $this->resetPage(); }
    public function updatingStatus():  void { $this->resetPage(); }
    public function updatingFaseId():  void { $this->resetPage(); }
    public function updatingRiscoId(): void { $this->resetPage(); }

    public function confirmarArquivar(int $id): void
    {
        $this->processoParaExcluir  = $id;
        $this->confirmandoExclusao  = true;
    }

    public function arquivar(): void
    {
        abort_unless(Auth::user()->temAcao('processos.arquivar'), 403, 'Sem permissão.');

        $processo = Processo::findOrFail($this->processoParaExcluir);

        $processo->update(['status' => 'Arquivado']);
        Auth::user()->registrarAuditoria('Arquivou processo', 'processos', $processo->id);

        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
        $this->dispatch('toast', message: "Processo {$processo->numero} arquivado.", type: 'success');
    }

    public function cancelarExclusao(): void
    {
        $this->confirmandoExclusao  = false;
        $this->processoParaExcluir  = null;
    }

    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $totalAtivos     = Processo::where('status', 'Ativo')->count();
        $totalArquivados = Processo::where('status', 'Arquivado')->count();
        $riscoAlto       = Processo::where('status', 'Ativo')
            ->whereHas('risco', fn($q) => $q->where('descricao', 'ilike', '%alto%'))
            ->count();
        $valorTotal = number_format(
            (float) Processo::where('status', 'Ativo')->sum('valor_causa'),
            2, ',', '.'
        );

        // Fases com contagem (Fase não tem relação inversa, usar pluck)
        $faseCounts = Processo::where('status', 'Ativo')
            ->whereNotNull('fase_id')
            ->selectRaw('fase_id, count(*) as total')
            ->groupBy('fase_id')
            ->pluck('total', 'fase_id');
        $porFase = Fase::orderBy('ordem')->get()
            ->filter(fn($f) => ($faseCounts[$f->id] ?? 0) > 0)
            ->map(fn($f) => $f->descricao . ': ' . $faseCounts[$f->id])
            ->join(', ');

        // Top advogados (relacionamento correto: processosComoAdvogado)
        $porAdvogado = \App\Models\Pessoa::whereHas(
                'processosComoAdvogado', fn($q) => $q->where('status', 'Ativo')
            )
            ->withCount(['processosComoAdvogado' => fn($q) => $q->where('status', 'Ativo')])
            ->orderByDesc('processos_como_advogado_count')
            ->take(5)->get()
            ->map(fn($a) => $a->nome . ': ' . $a->processos_como_advogado_count)
            ->join(', ');

        $contexto = "Você é um analista jurídico do sistema SAPRO. Responda de forma objetiva e direta em português.

Dados atuais do escritório:
- Total processos ativos: {$totalAtivos}
- Total arquivados: {$totalArquivados}
- Processos risco alto: {$riscoAlto}
- Valor total em causa: R\$ {$valorTotal}
- Por fase: {$porFase}
- Top advogados por processos: {$porAdvogado}

Pergunta do usuário: {$this->perguntaIA}

Responda em 1-3 frases objetivas. Se a pergunta pedir para filtrar ou mostrar algo específico, além de responder, termine com: FILTRO:campo=valor (ex: FILTRO:risco=Alto ou FILTRO:fase=Recursal ou FILTRO:busca=nome)";

        $resposta = app(\App\Services\AIService::class)->gerar($contexto, 300);

        if ($resposta === null) {
            $this->respostaIA = 'IA temporariamente indisponível. Tente novamente em instantes.';
            return;
        }

        // Verificar se a IA sugeriu um filtro
        if (str_contains($resposta, 'FILTRO:')) {
            preg_match('/FILTRO:(\w+)=(.+)/', $resposta, $matches);
            if (count($matches) === 3) {
                $campo = trim($matches[1]);
                $valor = trim($matches[2]);

                if ($campo === 'busca')  $this->busca    = $valor;
                if ($campo === 'status') $this->status   = $valor;
                if ($campo === 'fase') {
                    $fase = Fase::where('descricao', 'ilike', "%{$valor}%")->first();
                    if ($fase) $this->fase_id = (string) $fase->id;
                }
                if ($campo === 'risco') {
                    $risco = GrauRisco::where('descricao', 'ilike', "%{$valor}%")->first();
                    if ($risco) $this->risco_id = (string) $risco->id;
                }

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

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Processo::with(['cliente', 'advogado', 'fase', 'risco'])
            ->when($this->busca,    fn($q) => $q->busca($this->busca))
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->fase_id,  fn($q) => $q->where('fase_id', $this->fase_id))
            ->when($this->risco_id, fn($q) => $q->where('risco_id', $this->risco_id))
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // BOM UTF-8 para Excel
            fputcsv($out, ['Número','Cliente','Parte Contrária','Advogado','Fase','Risco','Status','Valor Causa','Data Distribuição'], ';');
            foreach ($rows as $p) {
                fputcsv($out, [
                    $p->numero,
                    $p->cliente?->nome ?? '',
                    $p->parteContraria?->nome ?? ($p->parte_contraria ?? ''),
                    $p->advogado?->nome ?? '',
                    $p->fase?->descricao ?? '',
                    $p->risco?->descricao ?? '',
                    $p->status,
                    $p->valor_causa ? number_format($p->valor_causa, 2, ',', '.') : '',
                    $p->data_distribuicao?->format('d/m/Y') ?? '',
                ], ';');
            }
            fclose($out);
        }, 'processos-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $processos = Processo::with(['cliente', 'advogado', 'fase', 'risco'])
            ->when($this->busca,    fn($q) => $q->busca($this->busca))
            ->when($this->status,   fn($q) => $q->where('status', $this->status))
            ->when($this->fase_id,  fn($q) => $q->where('fase_id', $this->fase_id))
            ->when($this->risco_id, fn($q) => $q->where('risco_id', $this->risco_id))
            ->orderByDesc('created_at')
            ->paginate(15);

        // Métricas
        $totalAtivos = Processo::where('status', 'Ativo')->count();
        $valorTotal  = Processo::where('status', 'Ativo')->sum('valor_causa');
        $riscoAlto   = Processo::where('status', 'Ativo')
            ->whereHas('risco', fn($q) => $q->where('descricao', 'ilike', '%alto%'))
            ->count();
        $parados = Processo::where('status', 'Ativo')
            ->whereDoesntHave('andamentos', fn($q) => $q->where('data', '>=', now()->subDays(30)))
            ->count();

        // Contagens por fase e risco (apenas processos ativos)
        $faseCounts  = Processo::where('status', 'Ativo')
            ->whereNotNull('fase_id')
            ->selectRaw('fase_id, count(*) as total')
            ->groupBy('fase_id')
            ->pluck('total', 'fase_id');

        $riscoCounts = Processo::where('status', 'Ativo')
            ->whereNotNull('risco_id')
            ->selectRaw('risco_id, count(*) as total')
            ->groupBy('risco_id')
            ->pluck('total', 'risco_id');

        return view('livewire.processos', [
            'processos'   => $processos,
            'fases'       => Fase::orderBy('ordem')->get(),
            'riscos'      => GrauRisco::all(),
            'totalAtivos' => $totalAtivos,
            'valorTotal'  => (float) $valorTotal,
            'riscoAlto'   => $riscoAlto,
            'parados'     => $parados,
            'faseCounts'  => $faseCounts,
            'riscoCounts' => $riscoCounts,
        ]);
    }
}
