CREATE OR REPLACE VIEW vw_custas_reembolsaveis AS
SELECT
    p.id,
    p.processo_id,
    p.descricao,
    p.valor,
    p.valor_pago,
    p.data_pagamento,
    p.reembolso_gerado,
    p.recebimento_reembolso_id,

    CASE
        WHEN p.reembolso_gerado = true THEN 'cobrado'
        WHEN p.pago = false            THEN 'aguardando_pagamento'
        ELSE                                'pendente_cobranca'
    END AS situacao_reembolso,

    pr.numero AS numero_processo,

    pe.id   AS cliente_id,
    pe.nome AS cliente_nome

FROM pagamentos p
JOIN processos pr ON pr.id = p.processo_id
JOIN pessoas   pe ON pe.id = pr.cliente_id
WHERE p.reembolsavel = true
  AND p.processo_id IS NOT NULL;
