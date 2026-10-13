<?php
/**
 * Plugin Name: Empire Security Headers
 * Plugin URI: https://empirex.tech/shop/empire-security-headers-wp
 * Description: Sends nosniff, Referrer-Policy, X-Frame-Options, Permissions-Policy. Optional HSTS and CSP report-only. Free EMPIREX Tech module.
 * Version: 1.2.0
 * Requires at least: 5.8
 * Requires PHP: 8.0
 * Author: EMPIREX Tech
 * Author URI: https://empirex.tech
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: empire-security-headers
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/includes/class-headers.php';

final class Empire_Security_Headers_Plugin
{
    public static function init(): void
    {
        add_action('send_headers', [self::class, 'send']);
        add_action('admin_init', [self::class, 'register']);
        add_action('admin_menu', [self::class, 'menu']);
    }

    public static function register(): void
    {
        register_setting('empire_headers', 'empire_hdr_frame', [
            'type' => 'string',
            'sanitize_callback' => static fn ($v) => in_array($v, ['SAMEORIGIN', 'DENY'], true) ? $v : 'SAMEORIGIN',
            'default' => 'SAMEORIGIN',
        ]);
        register_setting('empire_headers', 'empire_hdr_hsts', [
            'type' => 'boolean',
            'sanitize_callback' => static fn ($v) => (bool) $v,
            'default' => false,
        ]);
        register_setting('empire_headers', 'empire_hdr_csp', [
            'type' => 'boolean',
            'sanitize_callback' => static fn ($v) => (bool) $v,
            'default' => false,
        ]);
        register_setting('empire_headers', 'empire_hdr_skip_admin', [
            'type' => 'boolean',
            'sanitize_callback' => static fn ($v) => (bool) $v,
            'default' => true,
        ]);
    }

    public static function menu(): void
    {
        add_options_page('Empire Security Headers', 'Empire Headers', 'manage_options', 'empire-security-headers', [self::class, 'page']);
    }

    public static function page(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        echo '<div class="wrap"><h1>Empire Security Headers</h1><form method="post" action="options.php">';
        settings_fields('empire_headers');
        $frame = (string) get_option('empire_hdr_frame', 'SAMEORIGIN');
        echo '<table class="form-table"><tr><th>X-Frame-Options</th><td><select name="empire_hdr_frame"><option value="SAMEORIGIN"' . selected($frame, 'SAMEORIGIN', false) . '>SAMEORIGIN</option><option value="DENY"' . selected($frame, 'DENY', false) . '>DENY</option></select></td></tr>';
        echo '<tr><th>HSTS</th><td><label><input type="checkbox" name="empire_hdr_hsts" value="1" ' . checked((bool) get_option('empire_hdr_hsts', false), true, false) . ' /> Send Strict-Transport-Security (HTTPS only)</label></td></tr>';
        echo '<tr><th>CSP report-only</th><td><label><input type="checkbox" name="empire_hdr_csp" value="1" ' . checked((bool) get_option('empire_hdr_csp', false), true, false) . ' /> Conservative CSP-Report-Only</label></td></tr>';
        echo '<tr><th>Skip wp-admin</th><td><label><input type="checkbox" name="empire_hdr_skip_admin" value="1" ' . checked((bool) get_option('empire_hdr_skip_admin', true), true, false) . ' /> Do not send extra headers in wp-admin</label></td></tr></table>';
        submit_button();
        echo '</form><p>SKU EMP-WP-HEADERS-FREE · <a href="https://empirex.tech/shop/empire-security-headers-wp">Empire Shop</a></p></div>';
    }

    public static function send(): void
    {
        if (headers_sent()) {
            return;
        }
        if (is_admin() && (bool) get_option('empire_hdr_skip_admin', true)) {
            return;
        }
        $map = \EmpireX\SecurityHeaders\Headers::map([
            'frame' => (string) get_option('empire_hdr_frame', 'SAMEORIGIN'),
            'hsts' => (bool) get_option('empire_hdr_hsts', false) && is_ssl(),
            'csp' => (bool) get_option('empire_hdr_csp', false),
        ]);
        foreach ($map as $name => $value) {
            header($name . ': ' . $value);
        }
    }
}

add_action('plugins_loaded', ['Empire_Security_Headers_Plugin', 'init']);
