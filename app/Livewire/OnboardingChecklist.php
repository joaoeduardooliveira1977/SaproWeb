<?php

namespace App\Livewire;

use App\Services\OnboardingService;
use Livewire\Component;

class OnboardingChecklist extends Component
{
    public bool $visivel   = true;
    public bool $modalFim  = false;

    protected $listeners = [
        'onboarding:marcar' => 'marcarStep',
    ];

    public function marcarStep(string $step): void
    {
        $tenantId = auth('usuarios')->user()?->tenant_id;
        if (!$tenantId) return;

        OnboardingService::marcar($tenantId, $step);

        if (OnboardingService::todosCompletos($tenantId)) {
            $this->modalFim = true;
        }
    }

    public function ocultar(): void
    {
        $this->visivel = false;
    }

    public function fecharModalFim(): void
    {
        $this->modalFim = false;
    }

    public function render()
    {
        $tenantId = auth('usuarios')->user()?->tenant_id;

        if (!$tenantId) {
            return view('livewire.onboarding-checklist', [
                'steps'       => [],
                'total'       => 0,
                'concluidos'  => 0,
                'percentual'  => 0,
                'exibir'      => false,
                'diasRestantes' => 0,
            ]);
        }

        $tenant = tenant();
        $exibir = $tenant && ($tenant->plano === 'demo' || ($tenant->onboarding_concluido === false));

        if (!$exibir) {
            return view('livewire.onboarding-checklist', ['exibir' => false, 'steps' => [], 'total' => 0, 'concluidos' => 0, 'percentual' => 0, 'diasRestantes' => 0]);
        }

        $steps      = OnboardingService::getSteps($tenantId);
        $total      = count($steps);
        $concluidos = collect($steps)->where('concluido', true)->count();
        $percentual = $total > 0 ? (int) round($concluidos / $total * 100) : 0;

        return view('livewire.onboarding-checklist', compact('steps', 'total', 'concluidos', 'percentual', 'exibir'))
            ->with('diasRestantes', $tenant?->diasRestantesTrial() ?? 0);
    }
}
