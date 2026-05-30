<?php

namespace App\Livewire;

use App\Models\{Comunicado, ComunicadoLeitura};
use Livewire\Component;

class ComunicadoBanner extends Component
{
    public array $banners       = [];
    public int   $indiceAtual   = 0;
    public array $dispensados   = [];

    public function mount(): void
    {
        $this->dispensados = session('comunicados_dispensados', []);
        $this->carregarBanners();
    }

    private function carregarBanners(): void
    {
        $usuario = auth('usuarios')->user();
        if (!$usuario) return;

        $tenant = app()->bound('tenant') ? app('tenant') : null;
        if (!$tenant && $usuario->tenant_id) {
            $tenant = \App\Models\Tenant::find($usuario->tenant_id);
        }

        $query = Comunicado::ativos()
            ->prioridade('banner')
            ->orderByRaw("case tipo
                when 'manutencao_emergencial' then 0
                when 'pagamento_pendente'     then 1
                when 'manutencao_programada'  then 2
                when 'trial_expirando'        then 3
                else 4 end");

        if ($tenant) {
            $query->paraTenant($tenant->id, $tenant->plano);
        }

        $this->banners = $query->get()
            ->reject(fn($b) => in_array($b->id, $this->dispensados))
            ->map(fn($b) => [
                'id'       => $b->id,
                'titulo'   => $b->titulo,
                'mensagem' => $b->mensagem,
                'tipo'     => $b->tipo,
                'info'     => $b->tipoInfo(),
            ])
            ->values()
            ->toArray();

        $this->indiceAtual = 0;
    }

    public function dispensar(int $id): void
    {
        $this->dispensados[] = $id;
        session(['comunicados_dispensados' => $this->dispensados]);

        // Marca como lido
        $usuario = auth('usuarios')->user();
        if ($usuario) {
            $c = Comunicado::find($id);
            $c?->marcarLidoPor($usuario->id, $usuario->tenant_id ?? 0);
        }

        $this->carregarBanners();
    }

    public function proximo(): void
    {
        $this->indiceAtual = min($this->indiceAtual + 1, count($this->banners) - 1);
    }

    public function anterior(): void
    {
        $this->indiceAtual = max($this->indiceAtual - 1, 0);
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.comunicado-banner');
    }
}
