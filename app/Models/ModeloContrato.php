<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModeloContrato extends Model
{
    protected $table = 'modelo_contratos';

    protected $fillable = ['tenant_id', 'nome', 'tipo', 'texto', 'ativo'];

    protected $casts = ['ativo' => 'boolean'];

    public static function tiposLabels(): array
    {
        return [
            'honorario_processo' => 'Honorário de Processo',
            'consultivo'         => 'Consultivo / Retainer',
            'avulso'             => 'Serviço Avulso',
        ];
    }

    /**
     * Substitui as variáveis do modelo com dados reais.
     * Variáveis suportadas: {{cliente}}, {{cpf_cnpj}}, {{advogado}}, {{oab}},
     * {{valor}}, {{parcelas}}, {{data_inicio}}, {{processo}}, {{tipo_acao}}, {{vara}},
     * {{escritorio}}, {{data_hoje}}
     */
    public function mesclar(array $vars): string
    {
        $texto = $this->texto;
        foreach ($vars as $chave => $valor) {
            $texto = str_replace("{{{$chave}}}", $valor ?? '', $texto);
        }
        return $texto;
    }

    public static function templatesPadrao(): array
    {
        return [
            [
                'nome' => 'Contrato de Honorários — Processo',
                'tipo' => 'honorario_processo',
                'texto' => "CONTRATO DE PRESTAÇÃO DE SERVIÇOS ADVOCATÍCIOS\n\nPelo presente instrumento particular, de um lado, o(a) CONTRATANTE:\n\nNome: {{cliente}}\nCPF/CNPJ: {{cpf_cnpj}}\n\nE de outro lado, o(a) CONTRATADO(A):\n\n{{escritorio}}\nAdvogado(a) responsável: {{advogado}}\nOAB: {{oab}}\n\nCLÁUSULA 1ª — OBJETO\nO presente contrato tem por objeto a prestação de serviços advocatícios referentes ao processo n.º {{processo}}, de natureza {{tipo_acao}}, em trâmite perante {{vara}}.\n\nCLÁUSULA 2ª — HONORÁRIOS\nPelos serviços prestados, o(a) CONTRATANTE pagará ao(à) CONTRATADO(A) o valor de R$ {{valor}}, em {{parcelas}} parcela(s), com início em {{data_inicio}}.\n\nCLÁUSULA 3ª — OBRIGAÇÕES DO CONTRATANTE\nO(A) CONTRATANTE se obriga a fornecer todos os documentos necessários à condução do processo e a efetuar os pagamentos nas datas acordadas.\n\nCLÁUSULA 4ª — DISPOSIÇÕES GERAIS\nEste contrato é regido pelas normas do Estatuto da Advocacia (Lei 8.906/94) e pelo Código de Ética da OAB.\n\nAssinado em {{data_hoje}}.\n\n\n_______________________________\nCONTRATANTE\n{{cliente}}\n\n\n_______________________________\nCONTRATADO(A)\n{{advogado}} — OAB {{oab}}",
            ],
            [
                'nome' => 'Contrato Consultivo / Retainer',
                'tipo' => 'consultivo',
                'texto' => "CONTRATO DE ASSESSORIA JURÍDICA\n\nPelo presente instrumento, as partes:\n\nCONTRATANTE: {{cliente}} — CPF/CNPJ: {{cpf_cnpj}}\n\nCONTRATADO(A): {{escritorio}} — Advogado(a): {{advogado}} — OAB: {{oab}}\n\nCLÁUSULA 1ª — OBJETO\nPrestação de assessoria jurídica continuada nas áreas de atuação do escritório.\n\nCLÁUSULA 2ª — VIGÊNCIA\nInício: {{data_inicio}}.\n\nCLÁUSULA 3ª — HONORÁRIOS\nHonorários mensais de R$ {{valor}}, vencendo todo dia {{parcelas}} de cada mês.\n\nCLÁUSULA 4ª — RESCISÃO\nQualquer das partes poderá rescindir este contrato mediante aviso prévio de 30 dias.\n\nAssinado em {{data_hoje}}.\n\n\n_______________________________\nCONTRATANTE\n{{cliente}}\n\n\n_______________________________\nCONTRATADO(A)\n{{advogado}} — OAB {{oab}}",
            ],
            [
                'nome' => 'Contrato de Serviço Avulso',
                'tipo' => 'avulso',
                'texto' => "CONTRATO DE SERVIÇO ADVOCATÍCIO AVULSO\n\nCONTRATANTE: {{cliente}} — CPF/CNPJ: {{cpf_cnpj}}\n\nCONTRATADO(A): {{escritorio}} — {{advogado}} — OAB: {{oab}}\n\nOBJETO: {{processo}}\n\nHONORÁRIOS: R$ {{valor}}, a ser pago em {{parcelas}} parcela(s) a partir de {{data_inicio}}.\n\nAssinado em {{data_hoje}}.\n\n\n_______________________________\n{{cliente}}\n\n\n_______________________________\n{{advogado}} — OAB {{oab}}",
            ],
        ];
    }
}
