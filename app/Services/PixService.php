<?php

namespace App\Services;

class PixService
{
    /**
     * Gera o payload PIX no formato EMV/BR Code (padrão BACEN).
     */
    public static function gerar(
        string $chave,
        string $nome,
        string $cidade,
        float  $valor,
        string $descricao = 'Honorarios',
        string $txid      = '***'
    ): string {
        $nome   = mb_substr(self::toAscii($nome), 0, 25);
        $cidade = mb_substr(self::toAscii($cidade), 0, 15);
        $descricao = mb_substr(self::toAscii($descricao), 0, 25);

        // Merchant Account Information (tag 26)
        $mai  = self::tlv('00', 'BR.GOV.BCB.PIX');
        $mai .= self::tlv('01', $chave);
        if ($descricao) {
            $mai .= self::tlv('02', $descricao);
        }

        // Additional Data Field (tag 62) — Reference Label
        $adl = self::tlv('05', $txid ?: '***');

        $payload  = self::tlv('00', '01');                    // Payload Format Indicator
        $payload .= self::tlv('01', '12');                    // Point of Initiation (12 = dynamic)
        $payload .= self::tlv('26', $mai);                    // Merchant Account Info
        $payload .= self::tlv('52', '0000');                  // Merchant Category Code
        $payload .= self::tlv('53', '986');                   // Currency (BRL = 986)
        if ($valor > 0) {
            $payload .= self::tlv('54', number_format($valor, 2, '.', ''));
        }
        $payload .= self::tlv('58', 'BR');                    // Country Code
        $payload .= self::tlv('59', $nome);                   // Merchant Name
        $payload .= self::tlv('60', $cidade);                 // Merchant City
        $payload .= self::tlv('62', $adl);                    // Additional Data
        $payload .= '6304';                                   // CRC16 placeholder

        $crc = self::crc16($payload);

        return $payload . str_pad(strtoupper(dechex($crc)), 4, '0', STR_PAD_LEFT);
    }

    /**
     * URL para gerar imagem do QR Code via API externa.
     */
    public static function qrCodeUrl(string $payload, int $size = 220): string
    {
        return 'https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size
            . '&data=' . rawurlencode($payload)
            . '&ecc=M';
    }

    /**
     * Verifica se o PIX está configurado (chave preenchida).
     */
    public static function configurado(): bool
    {
        return (bool) config('services.pix.chave');
    }

    // ── Helpers internos ──────────────────────────────────────

    private static function tlv(string $tag, string $value): string
    {
        return $tag . str_pad(strlen($value), 2, '0', STR_PAD_LEFT) . $value;
    }

    private static function toAscii(string $str): string
    {
        $str = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str) ?: $str;
        return preg_replace('/[^A-Za-z0-9 ]/', '', $str);
    }

    private static function crc16(string $data): int
    {
        $poly = 0x1021;
        $crc  = 0xFFFF;
        for ($i = 0, $len = strlen($data); $i < $len; $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000)
                    ? (($crc << 1) ^ $poly) & 0xFFFF
                    : ($crc << 1) & 0xFFFF;
            }
        }
        return $crc;
    }
}
