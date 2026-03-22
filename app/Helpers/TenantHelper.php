<?php

if (!function_exists('tenant')) {
    function tenant(): ?\App\Models\Tenant
    {
        try {
            return app('tenant');
        } catch (\Exception) {
            return null;
        }
    }
}

if (!function_exists('tenant_id')) {
    function tenant_id(): ?int
    {
        return tenant()?->id;
    }
}

if (!function_exists('tenant_pode')) {
    function tenant_pode(string $recurso): bool
    {
        $t = tenant();
        if (!$t) return false;

        return match($recurso) {
            'ia'        => $t->ia_habilitada,
            'datajud'   => $t->datajud_habilitado,
            'whatsapp'  => $t->whatsapp_habilitado,
            default     => true,
        };
    }
}
