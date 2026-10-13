<?php

declare(strict_types=1);

namespace EmpireX\SecurityHeaders;

/**
 * Header map. No WordPress symbols.
 */
final class Headers
{
    /**
     * @param array<string,mixed> $opts
     * @return array<string,string>
     */
    public static function map(array $opts): array
    {
        $out = [
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => self::pick(
                (string) ($opts['referrer'] ?? 'strict-origin-when-cross-origin'),
                ['no-referrer', 'strict-origin-when-cross-origin', 'same-origin', 'origin-when-cross-origin']
            ),
            'X-Frame-Options' => self::pick(
                (string) ($opts['frame'] ?? 'SAMEORIGIN'),
                ['SAMEORIGIN', 'DENY']
            ),
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
            'X-DNS-Prefetch-Control' => 'off',
        ];
        if (!empty($opts['hsts'])) {
            $out['Strict-Transport-Security'] = 'max-age=15552000; includeSubDomains';
        }
        if (!empty($opts['csp'])) {
            $out['Content-Security-Policy-Report-Only'] = "default-src 'self'; img-src 'self' data: https:; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'";
        }

        return $out;
    }

    /**
     * @param list<string> $allowed
     */
    private static function pick(string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : $allowed[0];
    }
}
