# Empire Security Headers for WordPress

Free HTTP security headers: **nosniff**, **Referrer-Policy**, **X-Frame-Options**, **Permissions-Policy**. Optional HSTS and CSP report-only.

Built by **SC EMPIRE GLOBAL SOLUTIONS SRL** (EMPIREX Tech).

Shop: [empirex.tech/shop/empire-security-headers-wp](https://empirex.tech/shop/empire-security-headers-wp)  
SKU `EMP-WP-HEADERS-FREE` · WordPress 5.8+ / 6.x · PHP 8.0+ · GPL-2.0-or-later

Original EMPIREX module. Not a client dump.

## What it does

- Sends headers on `send_headers`
- Skips `wp-admin` by default so the dashboard does not break
- HSTS only when the option is on **and** the request is SSL
- CSP is report-only, never enforcing, until you know the site graph

## Install

1. Download the ZIP from Empire Shop (free) or clone this repo
2. Plugins → Upload → Activate
3. Settings → Empire Headers

## Tests

```bash
php tests/run.php
```

## Docs

[empirex.tech/docs/modules/empire-security-headers-wp](https://empirex.tech/docs/modules/empire-security-headers-wp)
