<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Processo;
use App\Services\GeminiService;

class MinutaIA extends Component
{
    public int     $processoId;
    public string  $tipoDocumento    = '';
    public string  $instrucoes       = '';
    public ?string $minutaGerada     = null;
    public bool    $gerando          = false;
    public ?string $erro             = null;
    public bool    $mostrarFormulario = false;

    public array $tipos = [
        'Petição Inicial',
        'Contestação',
        'Recurso de Apelação',
        'Agravo de Instrumento',
        'Embargos de Declaração',
        'Notificação Extrajudicial',
        'Acordo / Proposta de Transação',
        'Procuração Ad Judicia',
    ];

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
    }

    public function gerarMinuta(): void
    {
        if ($this->gerando) return;
        if (empty($this->tipoDocumento)) {
            $this->erro = 'Selecione o tipo de documento.';
            return;
        }

        $this->gerando     = true;
        $this->erro        = null;
        $this->minutaGerada = null;

        $processo = Processo::with([
            'cliente', 'parteContraria', 'advogado',
            'tipoAcao', 'fase', 'vara',
        ])->find($this->processoId);

        if (!$processo) {
            $this->erro    = 'Processo não encontrado.';
            $this->gerando = false;
            return;
        }

        $contexto = implode("\n", array_filter([
            "Número do Processo: {$processo->numero}",
            "Cliente (requerente/requerido): " . ($processo->cliente?->nome ?? '–'),
            "Parte Contrária: "    . ($processo->parte_contraria ?? $processo->parteContraria?->nome ?? '–'),
            "Advogado Responsável: " . ($processo->advogado?->nome ?? '–'),
            "Tipo de Ação: "       . ($processo->tipoAcao?->descricao ?? '–'),
            "Fase Atual: "         . ($processo->fase?->descricao ?? '–'),
            "Vara/Órgão: "         . ($processo->vara ?? '–'),
            "Valor da Causa: R$ "  . number_format((float)($processo->valor_causa ?? 0), 2, ',', '.'),
            $processo->observacoes ? "Observações relevantes: {$processo->observacoes}" : null,
        ]));

        $instrucoes = $this->instrucoes
            ? "\n\nInstruções adicionais do advogado: {$this->instrucoes}"
            : '';

        $prompt = <<<PROMPT
Você é um advogado experiente no direito brasileiro. Redija um rascunho de {$this->tipoDocumento} com base nos dados do processo abaixo.

O documento deve:
- Seguir a estrutura formal jurídica brasileira
- Conter cabeçalho, qualificação das partes, corpo e pedidos
- Usar [CAMPO] para indicar informações que precisam ser preenchidas
- Ser objetivo e tecnicamente correto

Dados do processo:
{$contexto}{$instrucoes}

Gere apenas o texto do documento, sem explicações adicionais.
PROMPT;

        $result = app(GeminiService::class)->gerar($prompt, 1500);

        if ($result === null) {
            $this->erro    = 'IA temporariamente indisponível. Tente novamente.';
            $this->gerando = false;
            return;
        }

        $this->minutaGerada      = $result;
        $this->gerando           = false;
        $this->mostrarFormulario = false;
    }

    public function abrirFormulario(): void
    {
        $this->mostrarFormulario = true;
        $this->minutaGerada      = null;
        $this->erro              = null;
    }

    public function fechar(): void
    {
        $this->mostrarFormulario = false;
        $this->minutaGerada      = null;
        $this->erro              = null;
    }

    public function render()
    {
        return view('livewire.minuta-ia');
    }
}
