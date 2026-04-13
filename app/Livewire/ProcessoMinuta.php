<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuta;
use App\Models\Processo;
use Barryvdh\DomPDF\Facade\Pdf;

class ProcessoMinuta extends Component
{
    public int    $processoId;
    public ?int   $minutaId     = null;
    public string $corpoGerado  = '';
    public string $titulo       = '';

    public function mount(int $processoId): void
    {
        $this->processoId = $processoId;
    }

    public function selecionar(int $id): void
    {
        $minuta  = Minuta::findOrFail($id);
        $processo = Processo::with(['cliente', 'advogado', 'tipoAcao', 'fase'])->findOrFail($this->processoId);

        $this->minutaId    = $id;
        $this->titulo      = $minuta->titulo;
        $this->corpoGerado = $minuta->preencher($processo);
    }

    public function limpar(): void
    {
        $this->minutaId    = null;
        $this->corpoGerado = '';
        $this->titulo      = '';
    }

    public function gerarPdf()
    {
        $processo = Processo::with(['cliente'])->findOrFail($this->processoId);

        $pdf = Pdf::loadView('pdf.minuta', [
            'titulo'  => $this->titulo,
            'corpo'   => $this->corpoGerado,
            'processo'=> $processo,
        ])->setPaper('a4');

        $nomeArquivo = 'minuta-' . str_replace('/', '-', $processo->numero ?? $this->processoId) . '.pdf';

        return response()->streamDownload(
            fn() => print($pdf->output()),
            $nomeArquivo,
            ['Content-Type' => 'application/pdf']
        );
    }

    public function render()
    {
        $minutas = Minuta::where('ativo', true)->orderBy('titulo')->get();

        return view('livewire.processo-minuta', compact('minutas'));
    }
}
