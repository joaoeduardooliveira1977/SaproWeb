<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\{Agenda as AgendaModel, Processo};
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;

class Agenda extends Component
{
    use WithPagination;

    // Filtros lista
    public string $data_ini     = '';
    public string $data_fim     = '';
    public string $tipo         = '';
    public bool   $so_pendentes  = true;
    public string $responsavel_id = '';

    // IA
    public string  $perguntaIA = '';
    public ?string $respostaIA = null;

    // Vista
    public bool $vistaCalendario = false;
    public int  $mesCalendario;
    public int  $anoCalendario;
    public string $diaSelecionado = '';

    // Modal
    public bool   $modalAberto  = false;
    public ?int   $eventoId     = null;
    public string $titulo       = '';
    public string $data_hora    = '';
    public string $local        = '';
    public string $tipo_evento  = 'Outros';
    public bool   $urgente      = false;
    public string $processo_id  = '';
    public string $observacoes  = '';

    protected $rules = [
        'titulo'     => 'required|string|max:200',
        'data_hora'  => 'required|date',
        'tipo_evento'=> 'required',
    ];

    public function mount(): void
    {
        $this->data_ini        = today()->format('Y-m-d');
        $this->data_fim        = today()->addDays(30)->format('Y-m-d');
        $this->mesCalendario   = (int) now()->format('m');
        $this->anoCalendario   = (int) now()->format('Y');
    }

    // ── Vista ──────────────────────────────────────────────────

    public function toggleVista(): void
    {
        $this->vistaCalendario = ! $this->vistaCalendario;
        $this->diaSelecionado  = '';
    }

    public function mesAnterior(): void
    {
        $d = Carbon::create($this->anoCalendario, $this->mesCalendario, 1)->subMonth();
        $this->anoCalendario = $d->year;
        $this->mesCalendario = $d->month;
        $this->diaSelecionado = '';
    }

    public function proximoMes(): void
    {
        $d = Carbon::create($this->anoCalendario, $this->mesCalendario, 1)->addMonth();
        $this->anoCalendario = $d->year;
        $this->mesCalendario = $d->month;
        $this->diaSelecionado = '';
    }

    public function selecionarDia(string $data): void
    {
        $this->diaSelecionado = ($this->diaSelecionado === $data) ? '' : $data;
        $this->resetPage();
    }

    // ── Modal ──────────────────────────────────────────────────

    public function abrirModal(?int $id = null, string $data = ''): void
    {
        $this->eventoId    = $id;
        $this->modalAberto = true;

        if ($id) {
            $ev = AgendaModel::findOrFail($id);
            $this->titulo       = $ev->titulo;
            $this->data_hora    = $ev->data_hora->format('Y-m-d\TH:i');
            $this->local        = $ev->local ?? '';
            $this->tipo_evento  = $ev->tipo;
            $this->urgente      = $ev->urgente;
            $this->processo_id  = (string) ($ev->processo_id ?? '');
            $this->observacoes  = $ev->observacoes ?? '';
        } else {
            $this->titulo = $this->local = $this->processo_id = $this->observacoes = '';
            $this->tipo_evento = 'Outros';
            $this->urgente = false;
            $this->data_hora = ($data ? $data . 'T09:00' : now()->format('Y-m-d\TH:i'));
        }
    }

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->eventoId    = null;
        $this->resetErrorBag();
    }

    public function exportarCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = AgendaModel::with(['processo', 'responsavel.pessoa'])
            ->when($this->data_ini, fn($q) => $q->where('data_hora', '>=', $this->data_ini))
            ->when($this->data_fim, fn($q) => $q->where('data_hora', '<=', $this->data_fim . ' 23:59:59'))
            ->when($this->tipo,           fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->so_pendentes,   fn($q) => $q->where('concluido', false))
            ->when($this->responsavel_id, fn($q) => $q->where('responsavel_id', $this->responsavel_id))
            ->orderBy('data_hora')
            ->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Título','Data','Hora','Tipo','Local','Processo','Responsável','Urgente','Concluído','Observações'], ';');
            foreach ($rows as $ev) {
                fputcsv($out, [
                    $ev->titulo,
                    $ev->data_hora->format('d/m/Y'),
                    $ev->data_hora->format('H:i'),
                    $ev->tipo,
                    $ev->local ?? '',
                    $ev->processo?->numero ?? '',
                    $ev->responsavel?->pessoa?->nome ?? '',
                    $ev->urgente ? 'Sim' : 'Não',
                    $ev->concluido ? 'Sim' : 'Não',
                    $ev->observacoes ?? '',
                ], ';');
            }
            fclose($out);
        }, 'agenda-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function salvar(): void
    {
        $this->validate();

        $dados = [
            'titulo'         => $this->titulo,
            'data_hora'      => $this->data_hora,
            'local'          => $this->local ?: null,
            'tipo'           => $this->tipo_evento,
            'urgente'        => $this->urgente,
            'processo_id'    => $this->processo_id ?: null,
            'responsavel_id' => Auth::id(),
            'observacoes'    => $this->observacoes ?: null,
        ];

        if ($this->eventoId) {
            AgendaModel::findOrFail($this->eventoId)->update($dados);
        } else {
            AgendaModel::create($dados);
        }

        $this->fecharModal();
        $this->dispatch('toast', message: 'Evento salvo!', type: 'success');
    }

    public function perguntarIA(): void
    {
        if (empty(trim($this->perguntaIA))) return;

        $hoje      = AgendaModel::whereDate('data_hora', today())->where('concluido', false)->count();
        $semana    = AgendaModel::whereBetween('data_hora', [today(), today()->addDays(7)])->where('concluido', false)->count();
        $urgentes  = AgendaModel::where('urgente', true)->where('concluido', false)->count();
        $atrasados = AgendaModel::where('data_hora', '<', now())->where('concluido', false)->count();
        $total     = AgendaModel::where('concluido', false)->count();

        $proximosEventos = AgendaModel::where('concluido', false)
            ->where('data_hora', '>=', now())
            ->orderBy('data_hora')
            ->take(5)
            ->get()
            ->map(fn($e) => '- ' . $e->titulo . ' (' . $e->data_hora->format('d/m H:i') . ') [' . $e->tipo . ']')
            ->join("\n");

        $contexto = "Você é um assistente jurídico do sistema SAPRO. Responda de forma objetiva em português.

Dados da agenda:
- Total de eventos pendentes: {$total}
- Eventos hoje: {$hoje}
- Próximos 7 dias: {$semana}
- Eventos urgentes: {$urgentes}
- Eventos em atraso: {$atrasados}
- Próximos eventos:
{$proximosEventos}

Pergunta: {$this->perguntaIA}

Responda em 1-3 frases objetivas. Se pedir para filtrar, termine com: FILTRO:tipo=Valor ou FILTRO:pendentes=1";

        $resposta = app(\App\Services\GeminiService::class)->gerar($contexto, 300);

        if ($resposta === null) {
            $this->respostaIA = 'IA temporariamente indisponível.';
            return;
        }

        if (str_contains($resposta, 'FILTRO:')) {
            preg_match('/FILTRO:(\w+)=(.+)/', $resposta, $matches);
            if (count($matches) === 3) {
                $campo = trim($matches[1]);
                $valor = trim($matches[2]);
                if ($campo === 'tipo')     $this->tipo        = $valor;
                if ($campo === 'pendentes') $this->so_pendentes = true;
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

    public function concluir(int $id): void
    {
        AgendaModel::findOrFail($id)->update(['concluido' => true]);
    }

    public function excluir(int $id): void
    {
        AgendaModel::findOrFail($id)->delete();
        $this->dispatch('toast', message: 'Evento removido.', type: 'success');
    }

    // ── Render ─────────────────────────────────────────────────

    public function render()
    {
        // ── Lista ──
        $eventos = AgendaModel::with(['processo', 'responsavel.pessoa'])
            ->when($this->diaSelecionado, fn($q) => $q->whereDate('data_hora', $this->diaSelecionado))
            ->when(!$this->diaSelecionado && $this->data_ini, fn($q) => $q->where('data_hora', '>=', $this->data_ini))
            ->when(!$this->diaSelecionado && $this->data_fim, fn($q) => $q->where('data_hora', '<=', $this->data_fim . ' 23:59:59'))
            ->when($this->tipo,           fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->so_pendentes,   fn($q) => $q->where('concluido', false))
            ->when($this->responsavel_id, fn($q) => $q->where('responsavel_id', $this->responsavel_id))
            ->orderBy('data_hora')
            ->paginate(20);

        // ── Calendário ──
        $inicioMes   = Carbon::create($this->anoCalendario, $this->mesCalendario, 1)->startOfMonth();
        $fimMes      = $inicioMes->copy()->endOfMonth();

        $eventosMes  = AgendaModel::whereBetween('data_hora', [$inicioMes, $fimMes])
            ->when($this->so_pendentes,   fn($q) => $q->where('concluido', false))
            ->when($this->tipo,           fn($q) => $q->where('tipo', $this->tipo))
            ->when($this->responsavel_id, fn($q) => $q->where('responsavel_id', $this->responsavel_id))
            ->get()
            ->groupBy(fn($e) => $e->data_hora->format('Y-m-d'));

        $responsaveis = DB::table('usuarios as u')
            ->join('pessoas as p', 'p.id', '=', 'u.pessoa_id')
            ->orderBy('p.nome')
            ->select('u.id', 'p.nome')
            ->get();

        // Métricas
        $metricas = [
            'hoje'      => AgendaModel::whereDate('data_hora', today())->where('concluido', false)->count(),
            'semana'    => AgendaModel::whereBetween('data_hora', [today(), today()->addDays(7)])->where('concluido', false)->count(),
            'urgentes'  => AgendaModel::where('urgente', true)->where('concluido', false)->count(),
            'atrasados' => AgendaModel::where('data_hora', '<', now())->where('concluido', false)->count(),
        ];

        // Contagem por tipo (pendentes)
        $tipoCounts = [];
        foreach (['Audiência', 'Prazo', 'Reunião', 'Consulta', 'Despacho', 'Outros'] as $t) {
            $tipoCounts[$t] = AgendaModel::where('tipo', $t)->where('concluido', false)->count();
        }

        return view('livewire.agenda', [
            'eventos'      => $eventos,
            'eventosMes'   => $eventosMes,
            'inicioMes'    => $inicioMes,
            'processos'    => Processo::where('status', 'Ativo')->orderBy('numero')->get(),
            'responsaveis' => $responsaveis,
            'metricas'     => $metricas,
            'tipoCounts'   => $tipoCounts,
        ]);
    }
}
