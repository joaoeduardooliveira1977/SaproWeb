<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterAdminLog extends Model
{
    public $timestamps  = false;
    const CREATED_AT    = 'created_at';

    protected $table    = 'master_admin_logs';

    protected $fillable = [
        'admin_id', 'admin_nome', 'acao',
        'tenant_id', 'tenant_nome', 'detalhes', 'ip',
    ];

    public static function registrar(
        string  $acao,
        ?int    $tenantId   = null,
        ?string $tenantNome = null,
        ?string $detalhes   = null
    ): void {
        $admin = auth('usuarios')->user();

        static::create([
            'admin_id'    => $admin?->id,
            'admin_nome'  => $admin?->nome ?? 'Sistema',
            'acao'        => $acao,
            'tenant_id'   => $tenantId,
            'tenant_nome' => $tenantNome,
            'detalhes'    => $detalhes,
            'ip'          => request()->ip(),
        ]);
    }
}
