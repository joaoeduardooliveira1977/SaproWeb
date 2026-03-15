<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TjspVerificacao extends Model
{
    protected $table = 'tjsp_verificacoes';

    protected $fillable = [
        'status',
        'total',
        'processado',
        'processo_atual',
        'novos_total',
        'novos_andamentos',
        'log_linhas',
        'prazos_criados',
        'iniciado_em',
        'concluido_em',
    ];

    protected $casts = [
        'novos_andamentos' => 'array',
        'log_linhas'       => 'array',
        'iniciado_em'      => 'datetime',
        'concluido_em'     => 'datetime',
    ];

    /**
     * Append a log line (keeps last 100 entries to avoid unbounded growth).
     */
    public function appendLog(string $tipo, string $numero, string $msg, ?string $tribunal = null, int $novos = 0): void
    {
        $linhas   = $this->log_linhas ?? [];
        $linhas[] = [
            'ts'       => now()->format('H:i:s'),
            'tipo'     => $tipo,   // consultando | ok | sem_novos | erro | ignorado
            'numero'   => $numero,
            'tribunal' => $tribunal,
            'novos'    => $novos,
            'msg'      => $msg,
        ];

        if (count($linhas) > 100) {
            $linhas = array_slice($linhas, -100);
        }

        $this->update(['log_linhas' => $linhas]);
    }

    public function porcentagem(): int
    {
        if ($this->total === 0) return 0;
        return (int) round(($this->processado / $this->total) * 100);
    }

    public function emAndamento(): bool
    {
        if (!in_array($this->status, ['pendente', 'rodando'])) {
            return false;
        }

        // Considera travado se estiver em andamento há mais de 10 minutos sem progresso
        if ($this->iniciado_em && $this->iniciado_em->diffInMinutes(now()) > 10) {
            return false;
        }

        return true;
    }
}
