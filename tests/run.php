<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/class-headers.php';

use EmpireX\SecurityHeaders\Headers;

$failed = 0;
$passed = 0;
function expect(string $name, bool $ok, string $detail = ''): void
{
    global $failed, $passed;
    if ($ok) {
        $passed++;
        fwrite(STDOUT, "OK   {$name}\n");
        return;
    }
    $failed++;
    fwrite(STDOUT, "FAIL {$name}" . ($detail !== '' ? " — {$detail}" : '') . "\n");
}

$base = Headers::map([]);
expect('nosniff', ($base['X-Content-Type-Options'] ?? '') === 'nosniff');
expect('frame default', ($base['X-Frame-Options'] ?? '') === 'SAMEORIGIN');
expect('no hsts by default', !isset($base['Strict-Transport-Security']));
expect('no csp by default', !isset($base['Content-Security-Policy-Report-Only']));

$full = Headers::map(['hsts' => true, 'csp' => true, 'frame' => 'DENY', 'referrer' => 'same-origin']);
expect('hsts on', isset($full['Strict-Transport-Security']));
expect('csp report only', isset($full['Content-Security-Policy-Report-Only']));
expect('deny frame', $full['X-Frame-Options'] === 'DENY');
expect('referrer', $full['Referrer-Policy'] === 'same-origin');

$bad = Headers::map(['frame' => 'ALLOWALL', 'referrer' => 'totally-invalid']);
expect('invalid frame fallback', $bad['X-Frame-Options'] === 'SAMEORIGIN');
expect('invalid referrer fallback', $bad['Referrer-Policy'] === 'no-referrer');

fwrite(STDOUT, "\n{$passed} passed, {$failed} failed\n");
exit($failed === 0 ? 0 : 1);
