<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nome', 'slug', 'email', 'telefone', 'cnpj', 'logo',
        'dominio', 'cor_primaria', 'cor_secundaria',
        'endereco', 'cidade', 'oab',
        'plano', 'trial_expira_em', 'trial_iniciado_em', 'ativo', 'onboarding_concluido',
        'limite_processos', 'limite_usuarios', 'limite_storage_mb',
        'ia_habilitada', 'datajud_habilitado', 'whatsapp_habilitado',
        'timezone', 'gemini_api_key', 'configuracoes',
        'deleted_by', 'delete_reason',
        'origem', 'responsavel_nome', 'responsavel_telefone',
    ];

    protected $casts = [
        'trial_expira_em'      => 'datetime',
        'trial_iniciado_em'    => 'datetime',
        'ativo'                => 'boolean',
        'onboarding_concluido' => 'boolean',
        'ia_habilitada'        => 'boolean',
        'datajud_habilitado'   => 'boolean',
        'whatsapp_habilitado'  => 'boolean',
        'configuracoes'        => 'array',
    ];

    // ── Scopes ───────────────────────────────────────────────────

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // ── Accessors ────────────────────────────────────────────────

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && file_exists(storage_path('app/public/' . $this->logo))) {
            return asset('storage/' . $this->logo);
        }

        return asset('images/logo-default.png');
    }

    // ── Relationships ────────────────────────────────────────────

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'tenant_id');
    }

    public function processos(): HasMany
    {
        return $this->hasMany(Processo::class, 'tenant_id');
    }

    public function trialExpirado(): bool
    {
        if ($this->plano !== 'demo') return false;
        return $this->trial_expira_em && $this->trial_expira_em->isPast();
    }

    public function diasRestantesTrial(): int
    {
        if (!$this->trial_expira_em) return 0;
        return max(0, (int) now()->diffInDays($this->trial_expira_em, false));
    }

    public function onboardingSteps(): HasMany
    {
        return $this->hasMany(OnboardingStep::class, 'tenant_id');
    }

    public function trialEmails(): HasMany
    {
        return $this->hasMany(TrialEmail::class, 'tenant_id');
    }

    public static function limitesPlanoCompleto(string $plano): array
    {
        return match($plano) {
            'demo'       => ['limite_processos' => 20, 'limite_usuarios' => 3,  'limite_storage_mb' => 50,   'ia_habilitada' => false, 'datajud_habilitado' => false, 'whatsapp_habilitado' => true],
            'starter'    => ['limite_processos' => 50, 'limite_usuarios' => 5,  'limite_storage_mb' => 500,  'ia_habilitada' => true,  'datajud_habilitado' => true,  'whatsapp_habilitado' => true],
            'pro'        => ['limite_processos' => 0,  'limite_usuarios' => 0,  'limite_storage_mb' => 2048, 'ia_habilitada' => true,  'datajud_habilitado' => true,  'whatsapp_habilitado' => true],
            'enterprise' => ['limite_processos' => 0,  'limite_usuarios' => 0,  'limite_storage_mb' => 0,    'ia_habilitada' => true,  'datajud_habilitado' => true,  'whatsapp_habilitado' => true],
            default      => ['limite_processos' => 20, 'limite_usuarios' => 3,  'limite_storage_mb' => 50,   'ia_habilitada' => false, 'datajud_habilitado' => false, 'whatsapp_habilitado' => true],
        };
    }

    public function atingiuLimiteProcessos(): bool
    {
        if ($this->limite_processos === 0) return false;
        return $this->processos()->where('status', 'Ativo')->count() >= $this->limite_processos;
    }

    public function atingiuLimiteUsuarios(): bool
    {
        if ($this->limite_usuarios === 0) return false;
        return $this->usuarios()->where('ativo', true)->count() >= $this->limite_usuarios;
    }

    public static function limitesPlano(string $plano): array
    {
        return match($plano) {
            'demo'       => ['processos' => 5,   'usuarios' => 2,  'ia' => false, 'datajud' => false, 'whatsapp' => true],
            'starter'    => ['processos' => 50,  'usuarios' => 5,  'ia' => true,  'datajud' => true,  'whatsapp' => true],
            'pro'        => ['processos' => 0,   'usuarios' => 0,  'ia' => true,  'datajud' => true,  'whatsapp' => true],
            'enterprise' => ['processos' => 0,   'usuarios' => 0,  'ia' => true,  'datajud' => true,  'whatsapp' => true],
            default      => ['processos' => 5,   'usuarios' => 2,  'ia' => false, 'datajud' => false, 'whatsapp' => true],
        };
    }

    public function nomePlano(): string
    {
        return match($this->plano) {
            'demo'       => 'Demo',
            'starter'    => 'Starter',
            'pro'        => 'Pro',
            'enterprise' => 'Enterprise',
            default      => 'Demo',
        };
    }
}
