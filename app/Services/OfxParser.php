<?php

namespace App\Services;

class OfxParser
{
    /**
     * Parseia conteúdo de arquivo OFX/QFX (1.x SGML ou 2.x XML).
     * Retorna array com: banco, agencia, conta, data_ini, data_fim, lancamentos[]
     */
    public function parse(string $content): array
    {
        $content = $this->normalizar($content);

        if ($this->isXml($content)) {
            return $this->parseXml($content);
        }

        return $this->parseSgml($content);
    }

    // ── Detecção de formato ────────────────────────────────────

    private function isXml(string $content): bool
    {
        return (bool) preg_match('/<\?xml/i', $content)
            || (bool) preg_match('/^<OFX>/im', $content);
    }

    private function normalizar(string $content): string
    {
        return str_replace(["\r\n", "\r"], "\n", $content);
    }

    // ── OFX 2.x XML ───────────────────────────────────────────

    private function parseXml(string $content): array
    {
        // Remove cabeçalho OFXHEADER se vier junto do XML
        $xml = preg_replace('/^.*?(?=<OFX>)/si', '', $content);

        // Tenta corrigir & soltos
        $xml = preg_replace('/&(?!amp;|lt;|gt;|apos;|quot;)/', '&amp;', $xml);

        try {
            $dom = new \SimpleXMLElement($xml);
        } catch (\Exception $e) {
            throw new \RuntimeException('OFX XML inválido: ' . $e->getMessage());
        }

        // Tenta banco ou cartão
        $stmtrs = $dom->BANKMSGSRSV1->STMTTRNRS->STMTRS
            ?? $dom->CREDITCARDMSGSRSV1->CCSTMTTRNRS->CCSTMTRS
            ?? null;

        if (! $stmtrs) {
            throw new \RuntimeException('Estrutura OFX não reconhecida.');
        }

        $acctFrom = $stmtrs->BANKACCTFROM ?? $stmtrs->CCACCTFROM ?? null;
        $tranList = $stmtrs->BANKTRANLIST ?? null;

        $result = [
            'banco'    => trim((string) ($acctFrom->BANKID  ?? '')),
            'agencia'  => trim((string) ($acctFrom->BRANCHID ?? '')),
            'conta'    => trim((string) ($acctFrom->ACCTID  ?? '')),
            'data_ini' => $this->parseData((string) ($tranList->DTSTART ?? '')),
            'data_fim' => $this->parseData((string) ($tranList->DTEND   ?? '')),
            'lancamentos' => [],
        ];

        foreach ($tranList->STMTTRN ?? [] as $trn) {
            $lance = [
                'tipo'     => trim((string) ($trn->TRNTYPE ?? '')),
                'data'     => $this->parseData((string) ($trn->DTPOSTED ?? '')),
                'valor'    => (float) str_replace(',', '.', trim((string) ($trn->TRNAMT ?? 0))),
                'fitid'    => trim((string) ($trn->FITID   ?? '')),
                'descricao'=> trim((string) ($trn->MEMO    ?? $trn->NAME ?? '')),
            ];

            if ($lance['data'] && $lance['valor'] != 0) {
                $result['lancamentos'][] = $lance;
            }
        }

        return $result;
    }

    // ── OFX 1.x SGML ──────────────────────────────────────────

    private function parseSgml(string $content): array
    {
        // Isola o bloco <OFX>...</OFX> (ou do <OFX> até o fim)
        if (preg_match('/<OFX>(.*)/si', $content, $m)) {
            $content = '<OFX>' . $m[1];
        }

        $result = [
            'banco'    => $this->sgmlTag($content, 'BANKID'),
            'agencia'  => $this->sgmlTag($content, 'BRANCHID'),
            'conta'    => $this->sgmlTag($content, 'ACCTID'),
            'data_ini' => $this->parseData($this->sgmlTag($content, 'DTSTART')),
            'data_fim' => $this->parseData($this->sgmlTag($content, 'DTEND')),
            'lancamentos' => [],
        ];

        // Divide em blocos <STMTTRN> (alguns bancos fecham, outros não)
        preg_match_all(
            '/<STMTTRN>(.*?)(?:<\/STMTTRN>|(?=<STMTTRN>)|(?=<\/BANKTRANLIST>)|$)/si',
            $content,
            $blocos
        );

        foreach ($blocos[1] as $bloco) {
            $valor = (float) str_replace(',', '.', $this->sgmlTag($bloco, 'TRNAMT'));
            $data  = $this->parseData($this->sgmlTag($bloco, 'DTPOSTED'));

            if (! $data || $valor == 0) continue;

            $result['lancamentos'][] = [
                'tipo'      => $this->sgmlTag($bloco, 'TRNTYPE'),
                'data'      => $data,
                'valor'     => $valor,
                'fitid'     => $this->sgmlTag($bloco, 'FITID'),
                'descricao' => $this->sgmlTag($bloco, 'MEMO') ?: $this->sgmlTag($bloco, 'NAME'),
            ];
        }

        return $result;
    }

    /**
     * Extrai valor de uma tag SGML: <TAG>valor (até quebra de linha ou próxima tag)
     */
    private function sgmlTag(string $content, string $tag): string
    {
        if (preg_match('/<' . $tag . '>\s*([^\n<]+)/i', $content, $m)) {
            return trim($m[1]);
        }
        return '';
    }

    /**
     * Converte data OFX para Y-m-d.
     * Formatos: 20240115 | 20240115120000 | 20240115120000[-3:BRT]
     */
    private function parseData(string $raw): ?string
    {
        $limpo = preg_replace('/[^0-9].*$/', '', trim($raw));

        if (strlen($limpo) >= 8) {
            return substr($limpo, 0, 4) . '-'
                 . substr($limpo, 4, 2) . '-'
                 . substr($limpo, 6, 2);
        }

        return null;
    }
}
