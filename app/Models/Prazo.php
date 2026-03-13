<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prazo extends Model
{
    protected $table = 'prazos';

    protected $fillable = [
        'processo_id', 'responsavel_id', 'criado_por',
        'titulo', 'descricao', 'tipo',
        'data_inicio', 'tipo_contagem', 'dias', 'data_prazo',
        'prazo_fatal', 'status', 'data_cumprimento', 'observacoes',
    ];

    protected $casts = [
        'data_inicio'      => 'date',
        'data_prazo'       => 'date',
        'data_cumprimento' => 'date',
        'prazo_fatal'      => 'boolean',
    ];

    // ── Relacionamentos ──────────────────────────────────────────

    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    // ── Helpers ──────────────────────────────────────────────────

    /**
     * Calcula data_prazo a partir de data_inicio + dias (úteis ou corridos).
     */
    public static function calcularData(string $dataInicio, int $dias, string $tipo): Carbon
    {
        $data = Carbon::parse($dataInicio);

        if ($tipo === 'uteis') {
            $count = 0;
            while ($count < $dias) {
                $data->addDay();
                if (!$data->isWeekend()) {
                    $count++;
                }
            }
        } else {
            $data->addDays($dias);
        }

        return $data;
    }

    /**
     * Retorna quantos dias faltam (negativo = vencido).
     */
    public function diasRestantes(): int
    {
        if ($this->status !== 'aberto') {
            return 0;
        }
        return (int) now()->startOfDay()->diffInDays($this->data_prazo, false);
    }

    /**
     * Retorna a cor/urgência do prazo: normal | alerta | atencao | urgente | vencido | cumprido | perdido
     */
    public function urgencia(): string
    {
        return match (true) {
            $this->status === 'cumprido' => 'cumprido',
            $this->status === 'perdido'  => 'perdido',
            $this->diasRestantes() < 0   => 'vencido',
            $this->diasRestantes() <= 1  => 'urgente',
            $this->diasRestantes() <= 5  => 'atencao',
            $this->diasRestantes() <= 15 => 'alerta',
            default                      => 'normal',
        };
    }
}
