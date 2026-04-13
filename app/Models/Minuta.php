<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Minuta extends Model
{
    protected $fillable = ['titulo', 'categoria', 'corpo', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public static function categorias(): array
    {
        return [
            'peticao'    => 'Petição',
            'contrato'   => 'Contrato',
            'procuracao' => 'Procuração',
            'notificacao'=> 'Notificação',
            'declaracao' => 'Declaração',
            'recurso'    => 'Recurso',
            'outros'     => 'Outros',
        ];
    }

    /** Substitui todos os {{placeholders}} com dados reais do processo */
    public function preencher(Processo $processo): string
    {
        $cliente  = $processo->cliente;
        $advogado = $processo->advogado;

        $endCliente = implode(', ', array_filter([
            $cliente?->logradouro,
            $cliente?->cidade,
            $cliente?->estado,
            $cliente?->cep ? 'CEP ' . $cliente->cep : null,
        ]));

        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro',
        ];
        $hoje = now();
        $dataExtenso = $hoje->day . ' de ' . $meses[$hoje->month] . ' de ' . $hoje->year;

        $vars = [
            '{{processo_numero}}'            => $processo->numero ?? '',
            '{{processo_vara}}'              => $processo->vara ?? '',
            '{{processo_data_distribuicao}}' => $processo->data_distribuicao?->format('d/m/Y') ?? '',
            '{{processo_tipo_acao}}'         => $processo->tipoAcao?->descricao ?? '',
            '{{processo_fase}}'              => $processo->fase?->descricao ?? '',
            '{{processo_valor_causa}}'       => 'R$ ' . number_format($processo->valor_causa ?? 0, 2, ',', '.'),
            '{{parte_contraria}}'            => $processo->parte_contraria ?? '',
            '{{cliente_nome}}'               => $cliente?->nome ?? '',
            '{{cliente_cpf_cnpj}}'           => $cliente?->cpf_cnpj ?? '',
            '{{cliente_rg}}'                 => $cliente?->rg ?? '',
            '{{cliente_email}}'              => $cliente?->email ?? '',
            '{{cliente_telefone}}'           => $cliente?->telefone ?? '',
            '{{cliente_celular}}'            => $cliente?->celular ?? '',
            '{{cliente_endereco}}'           => $endCliente,
            '{{cliente_cidade}}'             => $cliente?->cidade ?? '',
            '{{cliente_estado}}'             => $cliente?->estado ?? '',
            '{{cliente_cep}}'                => $cliente?->cep ?? '',
            '{{advogado_nome}}'              => $advogado?->nome ?? '',
            '{{advogado_oab}}'               => $advogado?->oab ?? '',
            '{{data_atual}}'                 => $dataExtenso,
            '{{data_atual_curta}}'           => $hoje->format('d/m/Y'),
        ];

        return str_replace(array_keys($vars), array_values($vars), $this->corpo);
    }
}
