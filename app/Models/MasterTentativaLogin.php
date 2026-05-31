<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterTentativaLogin extends Model
{
    public $timestamps  = false;
    protected $table    = 'master_tentativas_login';

    protected $fillable = ['ip', 'tentativas', 'bloqueado_ate'];

    protected $casts = [
        'bloqueado_ate' => 'datetime',
        'updated_at'    => 'datetime',
    ];

    public static function registrarTentativa(string $ip): void
    {
        $registro = static::firstOrCreate(['ip' => $ip], ['tentativas' => 0]);
        $registro->increment('tentativas');
        $registro->updated_at = now();
        $registro->save();
    }

    public static function estaBloqueado(string $ip): bool
    {
        $registro = static::where('ip', $ip)->first();
        if (!$registro) return false;
        if ($registro->bloqueado_ate && $registro->bloqueado_ate->isFuture()) return true;
        if ($registro->bloqueado_ate && $registro->bloqueado_ate->isPast()) {
            $registro->update(['tentativas' => 0, 'bloqueado_ate' => null]);
            return false;
        }
        return false;
    }

    public static function bloquearSe5Tentativas(string $ip): bool
    {
        $registro = static::where('ip', $ip)->first();
        if (!$registro) return false;

        if ($registro->tentativas >= 5) {
            $registro->update(['bloqueado_ate' => now()->addMinutes(30)]);
            return true;
        }
        return false;
    }

    public static function resetar(string $ip): void
    {
        static::where('ip', $ip)->delete();
    }
}
