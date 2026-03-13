<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AaspConfig extends Model
{
    protected $table = 'aasp_config';

    protected $fillable = [
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
