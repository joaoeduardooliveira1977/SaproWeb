<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class AaspConfig extends Model
{
    use BelongsToTenant;

    protected $table = 'aasp_config';

    protected $fillable = [
        'tenant_id',
        'emails_destino',
        'horario_rotina',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function getEmailsArray(): array
    {
        if (empty($this->emails_destino)) {
            return [];
        }
        return array_filter(array_map('trim', preg_split('/[\n,;]+/', $this->emails_destino)));
    }
}
