<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowRegra extends Model
{
    use BelongsToTenant;

    protected $table = 'workflow_regras';

    protected $fillable = [
        'tenant_id',
        'nome',
        'descricao',
        'gatilho',
        'gatilho_config',
        'condicoes',
        'ativo',
        'execucoes_total',
    ];

    protected $casts = [
        'gatilho_config'  => 'array',
        'condicoes'       => 'array',
        'ativo'           => 'boolean',
        'execucoes_total' => 'integer',
    ];

    // ── Constantes de gatilho ─────────────────────────────────
    const GATILHO_ANDAMENTO_CRIADO        = 'andamento.criado';
    const GATILHO_FASE_MUDOU              = 'processo.fase_mudou';
    const GATILHO_PRAZO_VENCENDO          = 'prazo.vencendo';
    const GATILHO_PRAZO_VENCIDO           = 'prazo.vencido';
    const GATILHO_SEM_ANDAMENTO_DIAS      = 'processo.sem_andamento_dias';

    // ── Constantes de tipo de ação ────────────────────────────
    const ACAO_CRIAR_PRAZO        = 'criar_prazo';
    const ACAO_CRIAR_NOTIFICACAO  = 'criar_notificacao';
    const ACAO_CRIAR_AGENDA       = 'criar_agenda';
    const ACAO_ENVIAR_WHATSAPP    = 'enviar_whatsapp';
    const ACAO_ATUALIZAR_SCORE    = 'atualizar_score';
    const ACAO_CHAMAR_IA          = 'chamar_ia';

    // ── Relações ──────────────────────────────────────────────
    public function acoes(): HasMany
    {
        return $this->hasMany(WorkflowAcao::class, 'regra_id')->orderBy('ordem');
    }

    public function execucoes(): HasMany
    {
        return $this->hasMany(WorkflowExecucao::class, 'regra_id');
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeAtivas($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorGatilho($query, string $gatilho)
    {
        return $query->where('gatilho', $gatilho);
    }

    // ── Helpers ───────────────────────────────────────────────
    public static function gatilhosDisponiveis(): array
    {
        return [
            self::GATILHO_ANDAMENTO_CRIADO   => 'Novo andamento registrado',
            self::GATILHO_FASE_MUDOU         => 'Fase do processo alterada',
            self::GATILHO_PRAZO_VENCENDO     => 'Prazo prestes a vencer',
            self::GATILHO_PRAZO_VENCIDO      => 'Prazo vencido',
            self::GATILHO_SEM_ANDAMENTO_DIAS => 'Processo sem andamento por X dias',
        ];
    }

    public static function acoesDisponiveis(): array
    {
        return [
            self::ACAO_CRIAR_PRAZO       => 'Criar Prazo',
            self::ACAO_CRIAR_NOTIFICACAO => 'Criar Notificação',
            self::ACAO_CRIAR_AGENDA      => 'Criar Compromisso na Agenda',
            self::ACAO_ENVIAR_WHATSAPP   => 'Enviar WhatsApp',
            self::ACAO_ATUALIZAR_SCORE   => 'Atualizar Score do Processo',
            self::ACAO_CHAMAR_IA         => 'Analisar com IA',
        ];
    }

    public function incrementarExecucoes(): void
    {
        $this->increment('execucoes_total');
    }
}
