<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmOportunidade extends Model
{
    protected $table = 'crm_oportunidades';

    protected $fillable = [
        'nome', 'telefone', 'email', 'cpf_cnpj', 'origem',
        'titulo', 'area_direito', 'valor_estimado', 'descricao',
        'etapa', 'responsavel_id', 'data_previsao', 'data_fechamento',
        'motivo_perda', 'convertido', 'pessoa_id', 'usuario_id',
    ];

    protected $casts = [
        'valor_estimado' => 'float',
        'data_previsao'  => 'date',
        'data_fechamento'=> 'date',
        'convertido'     => 'boolean',
    ];

    public static array $etapas = [
        'novo_contato' => 'Novo Contato',
        'qualificacao' => 'Qualificação',
        'reuniao'      => 'Reunião Marcada',
        'proposta'     => 'Proposta Enviada',
        'negociacao'   => 'Negociação',
        'ganho'        => 'Ganho',
        'perdido'      => 'Perdido',
    ];

    public static array $origens = [
        'indicacao'    => 'Indicação',
        'site'         => 'Site',
        'redes_sociais'=> 'Redes Sociais',
        'telefone'     => 'Telefone',
        'evento'       => 'Evento',
        'outro'        => 'Outro',
    ];

    public static array $areas = [
        'Civil', 'Trabalhista', 'Criminal', 'Tributário',
        'Família', 'Previdenciário', 'Empresarial', 'Imobiliário',
        'Consumidor', 'Administrativo', 'Outro',
    ];

    public function atividades(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CrmAtividade::class, 'oportunidade_id');
    }

    public function responsavel(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'responsavel_id');
    }

    public function pessoa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pessoa::class);
    }

    public function isAtiva(): bool
    {
        return ! in_array($this->etapa, ['ganho', 'perdido']);
    }

    public function isVencida(): bool
    {
        return $this->data_previsao && $this->data_previsao->isPast() && $this->isAtiva();
    }
}
