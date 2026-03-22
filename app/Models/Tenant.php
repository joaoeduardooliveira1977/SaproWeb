<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'nome', 'slug', 'email', 'telefone', 'cnpj', 'logo',
        'plano', 'trial_expira_em', 'ativo',
        'limite_processos', 'limite_usuarios',
        'ia_habilitada', 'datajud_habilitado', 'whatsapp_habilitado',
        'timezone', 'gemini_api_key', 'configuracoes',
    ];

    protected $casts = [
        'trial_expira_em'      => 'datetime',
        'ativo'                => 'boolean',
        'ia_habilitada'        => 'boolean',
        'datajud_habilitado'   => 'boolean',
        'whatsapp_habilitado'  => 'boolean',
        'configuracoes'        => 'array',
    ];

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
