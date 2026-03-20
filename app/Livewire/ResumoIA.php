<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\GeminiService;
use Illuminate\Support\Facades\{DB, Cache};
use App\Models\{Agenda, Prazo, Processo};

class ResumoIA extends Component
{
    public ?string $resumo   = null;
    public bool    $gerando  = false;
    public ?string $erro     = null;
    public bool    $aberto   = false;



    public function mount(): void
    {
        $cached = Cache::get('resumo_ia_' . today()->format('Y-m-d'));
        if ($cached) {
            $this->resumo = $cached;
            $this->aberto = true;
        }
    }

    public function gerar(): void
    {
        if ($this->gerando) return;

        $this->gerando = true;
        $this->erro    = null;

        $audiencias     = Agenda::whereDate('data_hora', today())->where('tipo', 'Audiência')->count();
        $prazos7d       = Prazo::where('status', 'aberto')->whereBetween('data_prazo', [today(), today()->addDays(7)])->count();
        $prazosVencidos = Prazo::where('status', 'aberto')->where('data_prazo', '<', today())->count();
        $riscoAlto      = Processo::where('status', 'Ativo')
                            ->whereHas('risco', fn($q) => $q->where('descricao', 'ilike', '%alto%'))
                            ->count();
        $aReceber       = (float) DB::table('recebimentos')->where('recebido', false)->sum('valor');

        $fatais = Prazo::where('status', 'aberto')
            ->where('prazo_fatal', true)
            ->where('data_prazo', '<=', today()->addDays(7))
            ->orderBy('data_prazo')
            ->take(3)
            ->get()
            ->map(fn($p) => '- ' . $p->titulo . ' (vence ' . $p->data_prazo->format('d/m') . ')')
            ->join("\n");

        $dados = implode("\n", array_filter([
            'Data: ' . today()->format('d/m/Y'),
            "Audiências hoje: {$audiencias}",
            "Prazos nos próximos 7 dias: {$prazos7d}",
            "Prazos vencidos em aberto: {$prazosVencidos}",
            "Processos com risco alto: {$riscoAlto}",
            'Total a receber: R$ ' . number_format($aReceber, 2, ',', '.'),
            $fatais ? "Prazos fatais urgentes:\n{$fatais}" : null,
        ]));

        $prompt = "Você é um assistente jurídico. Crie um briefing matinal objetivo para o advogado com base nos dados abaixo. Destaque urgências, organize por prioridade e sugira o foco do dia. Máximo 150 palavras. Use linguagem direta e profissional, sem markdown.\n\nDados:\n{$dados}";

        $result = app(GeminiService::class)->gerar($prompt, 350);

        if ($result === null) {
            $this->erro    = 'IA temporariamente indisponível.';
            $this->gerando = false;
            return;
        }

        Cache::put('resumo_ia_' . today()->format('Y-m-d'), $result, now()->addHours(4));

        $this->resumo  = $result;
        $this->gerando = false;
        $this->aberto  = true;
    }

    public function fechar(): void
    {
        $this->aberto = false;
    }

    public function render()
    {
        return view('livewire.resumo-ia');
    }
}
