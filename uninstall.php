<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('empire_hdr_frame');
delete_option('empire_hdr_hsts');
delete_option('empire_hdr_csp');
delete_option('empire_hdr_skip_admin');
