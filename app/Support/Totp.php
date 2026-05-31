<?php

namespace App\Support;

/**
 * Implementação TOTP (RFC 6238) sem dependências externas.
 * Compatível com Google Authenticator, Authy e Microsoft Authenticator.
 */
class Totp
{
    private const CHARS   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    private const PERIOD  = 30;
    private const DIGITS  = 6;
    private const WINDOW  = 1; // aceita ±1 período (tolerância de clock)

    // ── Gerar secret ─────────────────────────────────────────────

    public static function generateSecret(int $length = 32): string
    {
        $secret = '';
        $max    = strlen(self::CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::CHARS[random_int(0, $max)];
        }
        return $secret;
    }

    // ── Gerar URL para QR Code ────────────────────────────────────

    public static function getQrCodeUrl(string $issuer, string $account, string $secret): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($issuer),
            rawurlencode($account),
            $secret,
            rawurlencode($issuer),
            self::DIGITS,
            self::PERIOD
        );
    }

    // ── Verificar código ──────────────────────────────────────────

    public static function verify(string $secret, string $code, int $window = self::WINDOW): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $time = (int) floor(time() / self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::generate($secret, $time + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    // ── Gerar código atual (para testes) ──────────────────────────

    public static function getCurrentCode(string $secret): string
    {
        return self::generate($secret, (int) floor(time() / self::PERIOD));
    }

    // ── Interno: calcula o código TOTP para um time step ─────────

    private static function generate(string $secret, int $timeStep): string
    {
        $key     = self::base32Decode($secret);
        $msg     = pack('N*', 0) . pack('N*', $timeStep);
        $hash    = hash_hmac('sha1', $msg, $key, true);
        $offset  = ord($hash[19]) & 0x0F;
        $code    = (
            ((ord($hash[$offset])     & 0x7F) << 24) |
            ((ord($hash[$offset + 1]) & 0xFF) << 16) |
            ((ord($hash[$offset + 2]) & 0xFF) << 8)  |
            (ord($hash[$offset + 3])  & 0xFF)
        ) % 1_000_000;

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    // ── Decodifica Base32 ─────────────────────────────────────────

    private static function base32Decode(string $input): string
    {
        $input   = strtoupper(trim($input));
        $chars   = self::CHARS;
        $output  = '';
        $buffer  = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($input); $i++) {
            $pos = strpos($chars, $input[$i]);
            if ($pos === false) continue;
            $buffer    = ($buffer << 5) | $pos;
            $bitsLeft += 5;
            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output   .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }

    // ── Gerar QR Code SVG inline ──────────────────────────────────

    public static function generateQrSvg(string $url, int $size = 200): string
    {
        // Tenta usar BaconQrCode se disponível
        if (class_exists(\BaconQrCode\Writer::class)) {
            try {
                $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                    new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size),
                    new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
                );
                return (new \BaconQrCode\Writer($renderer))->writeString($url);
            } catch (\Exception) {}
        }

        // Fallback: link para API pública de QR code
        $encoded = rawurlencode($url);
        return sprintf(
            '<img src="https://api.qrserver.com/v1/create-qr-code/?size=%dx%d&data=%s" width="%d" height="%d" alt="QR Code 2FA">',
            $size, $size, $encoded, $size, $size
        );
    }
}
