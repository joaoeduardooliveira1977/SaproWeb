<?php

namespace App\Livewire\ContratosMensais;

use App\Models\ContratoMensal;
use App\Models\Mensalidade;
use Livewire\Component;

class MensalidadesCliente extends Component
{
    public int $clienteId;

    public string $filtroAno    = '';
    public string $filtroStatus = '';

    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->filtroAno = (string) now()->year;
    }

    public function render()
    {
        $tid = auth('usuarios')->user()->tenant_id;

        $contratos = ContratoMensal::where('tenant_id', $tid)
            ->where('cliente_id', $this->clienteId)
            ->with(['mensalidades' => function ($q) use ($tid) {
                $q->where('tenant_id', $tid)
                  ->when($this->filtroAno, fn($q2) => $q2->where('competencia', 'like', $this->filtroAno . '-%'))
                  ->when($this->filtroStatus, fn($q2) => $q2->where('status', $this->filtroStatus))
                  ->orderBy('competencia', 'desc');
            }])
            ->orderBy('status')
            ->get();

        $anos = range(now()->year - 1, now()->year + 1);

        return view('livewire.contratos-mensais.mensalidades-cliente', compact('contratos', 'anos'));
    }
}
