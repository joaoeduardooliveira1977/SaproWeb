<?php

namespace App\Livewire;

use App\Mail\PrazoLembrete;
use App\Models\{Prazo, Processo, Usuario};
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Mail};
use Livewire\Component;
use Livewire\WithPagination;

class Prazos extends Component
{
    use WithPagination;

    // Embed
    public bool $embed      = false;
    public ?int $processoId = null;

    // ── Filtros ──────────────────────────────────────────────────
    public string $filtroStatus      = 'aberto';
    public string $filtroProcesso    = '';
    public string $filtroResponsavel = '';
    public string $filtroTipo        = '';
    public string $filtroBusca       = '';
    public string $filtroDataIni     = '';
    public string $filtroDataFim     = '';

    protected $queryString = [
        'filtroStatus'      => ['except' => 'aberto'],
        'filtroProcesso'    => ['except' => ''],
        'filtroResponsavel' => ['except' => ''],
        'filtroTipo'        => ['except' => ''],
        'filtroBusca'       => ['except' => ''],
        'filtroDataIni'     => ['except' => ''],
        'filtroDataFim'     => ['except' => ''],
    ];

    // ── Modal ────────────────────────────────────────────────────
    public bool   $modalAberto = false;
    public ?int   $prazoid     = null;

    // ── Campos do formulário ─────────────────────────────────────
    public string $titulo         = '';
    public string $descricao      = '';
    public string $tipo           = 'Prazo';
    public string $data_inicio    = '';
    public string $tipo_contagem  = 'corridos';
    public string $dias           = '';
    public string $data_prazo     = '';
    public bool   $prazo_fatal    = false;
    public string $processo_id    = '';
    public string $responsavel_id = '';
    public string $observacoes    = '';

    // ── IA ───────────────────────────────────────────────────────
    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    // ── Confirmação exclusão ─────────────────────────────────────
    public ?int $confirmarExcluir = null;

    protected function rules(): array
    {
        return [
            'titulo'        => 'required|string|max:200',
            'tipo'          => 'required|string',
            'data_inicio'   => 'required|date',
            'data_prazo'    => 'required|date',
            'tipo_contagem' => 'required|in:corridos,uteis',
            'dias'          => 'nullable|integer|min:0',
            'processo_id'   => 'nullable|exists:processos,id',
            'responsavel_id'=> 'nullable|exists:usuarios,id',
        ];
    }

    protected $messages = [
        'titulo.required'     => 'Informe o título do prazo.',
        'data_inicio.required'=> 'Informe a data de início.',
        'data_prazo.required' => 'Informe a data do prazo.',
    ];

    public function mount(bool $embed = false, ?int $processoId = null): void
    {
        $this->embed      = $embed;
        $this->processoId = $processoId;

        if ($embed && $processoId) {
            $this->filtroProcesso = (string) $processoId;
        }

        $this->data_inicio = today()->format('Y-m-d');
    }

    // ── Cálculo automático ───────────────────────────────────────

    public function updatedDias(): void
    {
        $this->recalcularPrazo();
    }

    public function updatedDataInicio(): void
    {
        $this->recalcularPrazo();
    }

    public function updatedTipoContagem(): void
    {
        $this->recalcularPrazo();
    }

    private function recalcularPrazo(): void
    {
        $dias = (int) $this->dias;
        if ($dias > 0 && $this->data_inicio) {
            $this->data_prazo = Prazo::calcularData(
                $this->data_inicio,
                $dias,
                $this->tipo_contagem
            )->format('Y-m-d');
        }
    }

    // ── Modal ────────────────────────────────────────────────────

    public function abrirModal(?int $id = null): void
    {
        $this->resetForm();

        if ($this->embed && $this->processoId && !$id) {
            $this->processo_id = (string) $this->processoId;
        }

        $this->prazoid = $id;
        $this->modalAberto = true;

        if ($id) {
            $p = Prazo::findOrFail($id);
            $this->titulo         = $p->titulo;
            $this->descricao      = $p->descricao ?? '';
            $this->tipo           = $p->tipo;
            $this->data_inicio    = $p->data_inicio->format('Y-m-d');
            $this->tipo_contagem  = $p->tipo_contagem;
            $this->dias           = (string) ($p->dias ?? '');
            $this->data_prazo     = $p->data_prazo->format('Y-m-d');
            $this->prazo_fatal    = $p->prazo_fatal;
            $this->processo_id    = (string) ($p->processo_id ?? '');
            $this->responsavel_id = (string) ($p->responsavel_id ?? '');
            $this->observacoes    = $p->observacoes ?? '';
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto    = false;
        $this->confirmarExcluir = null;
        $this->resetForm();
    }

    // ── CRUD ─────────────────────────────────────────────────────

    public function salvar(): void
    {
        $usuario = Auth::guard('usuarios')->user();
        abort_unless($usuario?->temAcao('prazos.editar'), 403, 'Sem permissão.');
        $this->validate();

        $dados = [
            'titulo'         => trim($this->titulo),
            'descricao'      => trim($this->descricao) ?: null,
            'tipo'           => $this->tipo,
            'data_inicio'    => $this->data_inicio,
            'tipo_contagem'  => $this->tipo_contagem,
            'dias'           => $this->dias !== '' ? (int) $this->dias : null,
            'data_prazo'     => $this->data_prazo,
            'prazo_fatal'    => $this->prazo_fatal,
            'processo_id'    => $this->processo_id ?: null,
            'responsavel_id' => $this->responsavel_id ?: null,
            'observacoes'    => trim($this->observacoes) ?: null,
            'criado_por'     => Auth::guard('usuarios')->id(),
        ];

        if ($this->prazoid) {
            Prazo::findOrFail($this->prazoid)->update($dados);
            $this->dispatch('toast', message: 'Prazo atualizado.', type: 'success');
        } else {
            $dados['status'] = 'aberto';
            $prazo = Prazo::create($dados);
            $this->dispatch('toast', message: 'Prazo cadastrado.', type: 'success');
            $this->enviarLembreteSeNecessario($prazo);
        }

        $this->fecharModal();
    }

    public function marcarCumprido(int $id): void
    {
        abort_unless(Auth::guard('usuarios')->user()?->temAcao('prazos.editar'), 403, 'Sem permissão.');
        Prazo::findOrFail($id)->update([
            'status'           => 'cumprido',
            'data_cumprimento' => today(),
        ]);
        $this->dispatch('toast', message: 'Prazo marcado como cumprido.', type: 'success');
    }

    public function marcarPerdido(int $id): void
    {
        abort_unless(Auth::guard('usuarios')->user()?->temAcao('prazos.editar'), 403, 'Sem permissão.');
        Prazo::findOrFail($id)->update(['status' => 'perdido']);
        $this->dispatch('toast', message: 'Prazo marcado como perdido.', type: 'success');
    }

    public function reabrir(int $id): void
    {
        abort_unless(Auth::guard('usuarios')->user()?->temAcao('prazos.editar'), 403, 'Sem permissão.');
        Prazo::findOrFail($id)->update([
            'status'           => 'aberto',
            'data_cumprimento' => null,
        ]);
    }

    public function confirmarExcluirPrazo(int $id): void
    {
        $this->confirmarExcluir = $id;
    }

    public function excluir(): void
    {
        abort_unless(Auth::guard('usuarios')->user()?->temAcao('prazos.excluir'), 403, 'Sem permissão.');
        if ($this->confirmarExcluir) {
            Prazo::findOrFail($this->confirmarExcluir)->delete();
            $this->confirmarExcluir = null;
            $this->dispatch('toast', message: 'Prazo removido.', type: 'success');
        }
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->buildQuery()->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Título','Tipo','Data Prazo','Dias Restantes','Status','Processo','Cliente','Responsável','Fatal'], ';');
            foreach ($rows as $p) {
                $dias = $p->diasRestantes();
                $urg  = $p->urgencia();
                $diasLabel = match(true) {
                    $urg === 'cumprido' => 'Cumprido',
                    $urg === 'perdido'  => 'Perdido',
                    $urg === 'vencido'  => abs($dias).'d vencido',
                    $dias === 0         => 'Vence hoje',
                    default             => $dias.' dia(s)',
                };
                fputcsv($out, [
                    $p->titulo,
                    $p->tipo,
                    $p->data_prazo->format('d/m/Y'),
                    $diasLabel,
                    $p->status,
                    $p->processo?->numero ?? '',
                    $p->processo?->cliente?->nome ?? '',
                    $p->responsavel?->nome ?? '',
                    $p->prazo_fatal ? 'Sim' : 'Não',
                ], ';');
            }
            fclose($out);
        }, 'prazos-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function exportarPdf(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $prazos = $this->buildQuery()->get();

        $pdf = Pdf::loadView('pdf.prazos-lista', [
            'prazos'       => $prazos,
            'filtroStatus' => $this->filtroStatus,
            'gerado_em'    => now()->format('d/m/Y H:i'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'prazos-' . now()->format('Ymd') . '.pdf'
        );
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->prazoid = null;
        $this->titulo  = $this->descricao = $this->dias = $this->data_prazo = '';
        $this->observacoes = $this->processo_id = $this->responsavel_id = '';
        $this->tipo           = 'Prazo';
        $this->tipo_contagem  = 'corridos';
        $this->data_inicio    = today()->format('Y-m-d');
        $this->prazo_fatal    = false;
        $this->resetErrorBag();
    }

    // ── Email imediato ao criar prazo próximo ────────────────────

    private function enviarLembreteSeNecessario(Prazo $prazo): void
    {
        if (! $prazo->responsavel_id) return;

        $dias = $prazo->diasRestantes();

        // Só envia se vence em até 15 dias ou já vencido
        if ($dias > 15) return;

        $responsavel = Usuario::find($prazo->responsavel_id);
        if (! $responsavel?->email) return;

        try {
            $prazo->load(['processo.cliente']);
            Mail::to($responsavel->email)->send(new PrazoLembrete($prazo, $responsavel));
        } catch (\Exception) {
            // silencia falha de envio
        }
    }

    // ── IA ────────────────────────────────────────────────────────

    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $totalAbertos  = Prazo::where('status', 'aberto')->count();
        $vencendoHoje  = Prazo::where('status', 'aberto')->whereDate('data_prazo', today())->count();
        $vencidos      = Prazo::where('status', 'aberto')->whereDate('data_prazo', '<', today())->count();
        $fatais        = Prazo::where('status', 'aberto')->where('prazo_fatal', true)
                            ->whereDate('data_prazo', '>=', today())
                            ->whereDate('data_prazo', '<=', today()->addDays(5))
                            ->count();
        $semana        = Prazo::where('status', 'aberto')
                            ->whereBetween('data_prazo', [today(), today()->addDays(7)])
                            ->count();

        $proximosPrazos = Prazo::where('status', 'aberto')
            ->where('data_prazo', '>=', today())
            ->orderBy('data_prazo')
            ->with(['processo', 'responsavel'])
            ->take(5)
            ->get()
            ->map(fn($p) => '- '.$p->titulo.' (vence '.$p->data_prazo->format('d/m/Y').')'.($p->prazo_fatal ? ' [FATAL]' : ''))
            ->join("\n");

        $contexto = "Você é um assistente jurídico do sistema Software Jur�dico. Responda de forma objetiva em português.

Dados dos prazos:
- Total em aberto: {$totalAbertos}
- Vencem hoje: {$vencendoHoje}
- Vencidos não cumpridos: {$vencidos}
- Prazos fatais próximos (5 dias): {$fatais}
- Vencem nos próximos 7 dias: {$semana}
- Próximos prazos:
{$proximosPrazos}

Pergunta: {$this->perguntaIA}

Responda em 1-3 frases objetivas. Se pedir para filtrar, termine com: FILTRO:status=aberto|cumprido|perdido ou FILTRO:tipo=Prazo Fatal ou FILTRO:busca=texto";

        $resposta = app(\App\Services\AIService::class)->gerar($contexto, 300);

        if ($resposta === null) {
            $this->respostaIA = 'IA temporariamente indisponível.';
            return;
        }

        if (str_contains($resposta, 'FILTRO:')) {
            preg_match('/FILTRO:(\w+)=(.+)/', $resposta, $matches);
            if (count($matches) === 3) {
                $campo = trim($matches[1]);
                $valor = trim($matches[2]);
                if ($campo === 'status') $this->filtroStatus = $valor;
                if ($campo === 'tipo')   $this->filtroTipo   = $valor;
                if ($campo === 'busca')  $this->filtroBusca  = $valor;
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

    // ── Query builder reutilizável ────────────────────────────────

    private function buildQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $q = Prazo::with(['processo.cliente', 'responsavel'])
            ->orderByRaw("CASE status WHEN 'aberto' THEN 0 WHEN 'perdido' THEN 1 WHEN 'cumprido' THEN 2 END")
            ->orderBy('data_prazo');

        if ($this->filtroStatus !== 'todos') {
            $q->where('status', $this->filtroStatus);
        }
        if ($this->filtroProcesso) {
            $q->where('processo_id', $this->filtroProcesso);
        }
        if ($this->filtroResponsavel) {
            $q->where('responsavel_id', $this->filtroResponsavel);
        }
        if ($this->filtroTipo) {
            $q->where('tipo', $this->filtroTipo);
        }
        if ($this->filtroBusca) {
            $q->where('titulo', 'ILIKE', '%' . $this->filtroBusca . '%');
        }
        if ($this->filtroDataIni) {
            $q->whereDate('data_prazo', '>=', $this->filtroDataIni);
        }
        if ($this->filtroDataFim) {
            $q->whereDate('data_prazo', '<=', $this->filtroDataFim);
        }

        return $q;
    }

    // ── Render ───────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $prazos      = $this->buildQuery()->paginate(25);
        $processos   = Processo::where('status', 'Ativo')->orderBy('numero')->get();
        $usuarios    = Usuario::where('ativo', true)->orderBy('nome')->get();

        // KPIs
        $totalAbertos  = Prazo::where('status', 'aberto')->count();
        $vencendoHoje  = Prazo::where('status', 'aberto')->whereDate('data_prazo', today())->count();
        $vencidos      = Prazo::where('status', 'aberto')->whereDate('data_prazo', '<', today())->count();
        $fatais        = Prazo::where('status', 'aberto')->where('prazo_fatal', true)
                              ->whereDate('data_prazo', '>=', today())
                              ->whereDate('data_prazo', '<=', today()->addDays(5))
                              ->count();

        // Contagem por tipo (abertos)
        $tipoCounts = [];
        foreach (['Prazo', 'Prazo Fatal', 'Audiência', 'Diligência', 'Recurso'] as $t) {
            $tipoCounts[$t] = Prazo::where('status', 'aberto')->where('tipo', $t)->count();
        }

        return view('livewire.prazos', compact(
            'prazos', 'processos', 'usuarios',
            'totalAbertos', 'vencendoHoje', 'vencidos', 'fatais',
            'tipoCounts'
        ));
    }
}
