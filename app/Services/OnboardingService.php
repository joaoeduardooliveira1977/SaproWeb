<?php

namespace App\Services;

use App\Models\OnboardingStep;
use Illuminate\Support\Facades\Cache;

class OnboardingService
{
    public static function marcar(int $tenantId, string $step): void
    {
        try {
            $registro = OnboardingStep::where('tenant_id', $tenantId)
                ->where('step', $step)
                ->first();

            if ($registro && $registro->concluido) return;

            if ($registro) {
                $registro->update(['concluido' => true, 'concluido_em' => now()]);
            } else {
                OnboardingStep::create([
                    'tenant_id'    => $tenantId,
                    'step'         => $step,
                    'concluido'    => true,
                    'concluido_em' => now(),
                ]);
            }

            Cache::forget("onboarding_steps_{$tenantId}");
        } catch (\Throwable) {
            // Nunca deixar onboarding derrubar a operação principal
        }
    }

    public static function getSteps(int $tenantId): array
    {
        return Cache::remember("onboarding_steps_{$tenantId}", 300, function () use ($tenantId) {
            $todos = config('onboarding.steps', []);
            $feitos = OnboardingStep::where('tenant_id', $tenantId)
                ->where('concluido', true)
                ->pluck('concluido_em', 'step');

            $resultado = [];
            foreach ($todos as $key => $descricao) {
                $resultado[$key] = [
                    'descricao'    => $descricao,
                    'concluido'    => isset($feitos[$key]),
                    'concluido_em' => $feitos[$key] ?? null,
                ];
            }
            return $resultado;
        });
    }

    public static function totalConcluidos(int $tenantId): int
    {
        $steps = self::getSteps($tenantId);
        return collect($steps)->where('concluido', true)->count();
    }

    public static function todosCompletos(int $tenantId): bool
    {
        $steps = self::getSteps($tenantId);
        return collect($steps)->every(fn($s) => $s['concluido']);
    }

    public static function inicializarParaTenant(int $tenantId): void
    {
        // Marcar 'primeiro_acesso' automaticamente ao criar o tenant
        self::marcar($tenantId, 'primeiro_acesso');
    }
}
