<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ContratoMensal extends Model
{
    use BelongsToTenant, SoftDeletes;

    protected $table = 'contratos_mensais';

    protected $fillable = [
        'tenant_id', 'cliente_id', 'descricao', 'responsavel', 'valor',
        'dia_vencimento', 'periodicidade', 'data_inicio', 'data_fim',
        'status', 'observacoes',
        'forma_cobranca', 'email_financeiro', 'whatsapp_financeiro',
        'envio_automatico', 'created_by',
    ];

    protected $casts = [
        'data_inicio'      => 'date',
        'data_fim'         => 'date',
        'valor'            => 'decimal:2',
        'dia_vencimento'   => 'integer',
        'envio_automatico' => 'boolean',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'cliente_id');
    }

    public function mensalidades(): HasMany
    {
        return $this->hasMany(Mensalidade::class, 'contrato_mensal_id');
    }

    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    public static function periodicidadeLabels(): array
    {
        return [
            'mensal'     => 'Mensal',
            'bimestral'  => 'Bimestral',
            'trimestral' => 'Trimestral',
            'semestral'  => 'Semestral',
            'anual'      => 'Anual',
        ];
    }

    public static function statusLabels(): array
    {
        return ['ativo' => 'Ativo', 'suspenso' => 'Suspenso', 'encerrado' => 'Encerrado'];
    }

    public function getPeriodicidadeLabelAttribute(): string
    {
        return self::periodicidadeLabels()[$this->periodicidade] ?? $this->periodicidade;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    /**
     * Gera mensalidades (e lançamentos vinculados) para o período.
     * Usa updateOrCreate para ser idempotente.
     * Cria financeiro_lancamento apenas para mensalidades CRIADAS (não atualizadas).
     */
    public function gerarMensalidades(?Carbon $aPartirDe = null): int
    {
        $inicio = $aPartirDe
            ? $aPartirDe->copy()->startOfMonth()
            : Carbon::parse($this->data_inicio)->startOfMonth();

        $fim = $this->data_fim
            ? Carbon::parse($this->data_fim)->startOfMonth()
            : $inicio->copy()->addMonths(11);

        $geradas   = 0;
        $meses     = $this->mesesPorPeriodicidade();
        $cursor    = $inicio->copy();
        $indice    = 0;

        while ($cursor->lte($fim)) {
            $competencia = $cursor->format('Y-m');
            $dia         = min($this->dia_vencimento, $cursor->daysInMonth);
            $vencimento  = $cursor->copy()->setDay($dia)->toDateString();

            // updateOrCreate: se já existe, apenas atualiza valor; se é nova, cria tudo
            $mensalidade = Mensalidade::withoutGlobalScopes()->firstOrNew([
                'contrato_mensal_id' => $this->id,
                'competencia'        => $competencia,
            ]);

            $isNova = !$mensalidade->exists;

            if ($isNova) {
                $mensalidade->tenant_id   = $this->tenant_id;
                $mensalidade->cliente_id  = $this->cliente_id;
                $mensalidade->vencimento  = $vencimento;
                $mensalidade->valor       = $this->valor;
                $mensalidade->status      = 'pendente';
                $mensalidade->save();

                // Cria o lançamento financeiro vinculado
                $lancamento = FinanceiroLancamento::create([
                    'tenant_id'          => $this->tenant_id,
                    'cliente_id'         => $this->cliente_id,
                    'contrato_mensal_id' => $this->id,
                    'tipo'               => 'receita',
                    'origem_tipo'        => 'mensal',
                    'descricao'          => $this->descricao . ' — ' . $this->formatarCompetencia($competencia),
                    'valor'              => $this->valor,
                    'vencimento'         => $vencimento,
                    'status'             => 'previsto',
                    'forma_pagamento'    => $this->forma_cobranca ?? 'pix',
                    'numero_parcela'     => $indice + 1,
                ]);

                $mensalidade->update(['financeiro_lancamento_id' => $lancamento->id]);
                $geradas++;
            } elseif (in_array($mensalidade->status, ['pendente', 'vencido'])) {
                // Atualiza valor se contrato mudou
                $mensalidade->update(['valor' => $this->valor, 'vencimento' => $vencimento]);
            }

            $cursor->addMonths($meses);
            $indice++;
        }

        return $geradas;
    }

    public function totalEmAberto(): float
    {
        return (float) $this->mensalidades()
            ->whereIn('status', ['pendente', 'vencido', 'parcial'])
            ->sum('valor');
    }

    public function totalRecebidoAno(int $ano = null): float
    {
        $ano ??= now()->year;
        return (float) $this->mensalidades()
            ->where('status', 'recebido')
            ->whereYear('data_recebimento', $ano)
            ->sum('valor_recebido');
    }

    public function proximoVencimento(): ?Carbon
    {
        $prox = $this->mensalidades()
            ->whereIn('status', ['pendente', 'vencido'])
            ->where('vencimento', '>=', today()->toDateString())
            ->orderBy('vencimento')
            ->first();

        return $prox ? Carbon::parse($prox->vencimento) : null;
    }

    private function mesesPorPeriodicidade(): int
    {
        return match ($this->periodicidade) {
            'bimestral'  => 2,
            'trimestral' => 3,
            'semestral'  => 6,
            'anual'      => 12,
            default      => 1,
        };
    }

    private function formatarCompetencia(string $competencia): string
    {
        [$ano, $mes] = explode('-', $competencia);
        $meses = ['', 'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        return ($meses[(int) $mes] ?? $mes) . '/' . $ano;
    }
}
