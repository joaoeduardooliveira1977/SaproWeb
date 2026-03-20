<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Processo;
use App\Services\GeminiService;
use Illuminate\Support\Facades\DB;

class ProcessoAnaliseIA extends Component
{
    public int     $processoId;
    public ?string $analise   = null;
    public ?string $analiseEm = null;
    public bool    $gerando   = false;
    public ?string $erro      = null;

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;

        $p = Processo::select('analise_ia', 'analise_ia_em')->find($processoId);
        if ($p) {
            $this->analise   = $p->analise_ia;
            $this->analiseEm = $p->analise_ia_em?->format('d/m/Y H:i');
        }
    }

    public function gerarAnalise(): void
    {
        if ($this->gerando) return;

        $this->gerando = true;
        $this->erro    = null;

        $processo = Processo::with([
            'cliente', 'tipoAcao', 'fase', 'risco',
            'andamentos' => fn($q) => $q->orderByDesc('data')->limit(5),
        ])->find($this->processoId);

        if (!$processo) {
            $this->erro    = 'Processo não encontrado.';
            $this->gerando = false;
            return;
        }

        $andamentos = $processo->andamentos
            ->map(fn($a) => '- [' . $a->data->format('d/m/Y') . '] ' . $a->descricao)
            ->join("\n");

        $linhas = array_filter([
            "Número: {$processo->numero}",
            "Cliente: "        . ($processo->cliente?->nome   ?? '–'),
            "Tipo de Ação: "   . ($processo->tipoAcao?->descricao ?? '–'),
            "Fase Atual: "     . ($processo->fase?->descricao ?? '–'),
            "Grau de Risco: "  . ($processo->risco?->descricao ?? '–'),
            "Valor da Causa: R$ " . number_format((float)($processo->valor_causa ?? 0), 2, ',', '.'),
            "Status: {$processo->status}",
            $processo->observacoes ? "Observações: {$processo->observacoes}" : null,
            $andamentos         ? "Últimos andamentos:\n{$andamentos}"       : null,
        ]);

        $dados = implode("\n", $linhas);

        $prompt = <<<PROMPT
Você é um assistente jurídico especializado. Com base nos dados do processo abaixo, forneça exatamente estas 4 seções com esses títulos em maiúsculas:

RESUMO: [resumo do processo em 2-3 linhas]

RISCO: [análise do risco atual — Baixo, Médio ou Alto — com justificativa de 1-2 linhas]

PRÓXIMOS PASSOS:
- [ação 1]
- [ação 2]
- [ação 3 se necessário]

ALERTAS: [alertas importantes, ou "Nenhum alerta identificado"]

Use linguagem jurídica objetiva. Não adicione títulos extras nem formatação markdown.

Dados do processo:
{$dados}
PROMPT;

        $result = app(GeminiService::class)->gerar($prompt, 1500);

        if ($result === null) {
            $this->erro    = 'IA temporariamente indisponível. Tente novamente em instantes.';
            $this->gerando = false;
            return;
        }

        $processo->update([
            'analise_ia'    => $result,
            'analise_ia_em' => now(),
        ]);

        $this->analise   = $result;
        $this->analiseEm = now()->format('d/m/Y H:i');
        $this->gerando   = false;
    }

    public function render()
    {
        return view('livewire.processo-analise-ia');
    }
}
