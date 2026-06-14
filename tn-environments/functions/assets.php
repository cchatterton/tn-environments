<?php
/**
 * Asset loading for TN Environments.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_enqueue_scripts', 'tn_env_enqueue_admin_assets');

function tn_env_enqueue_admin_assets($hook_suffix): void {
    wp_enqueue_style(
        'tn-environments-admin',
        TN_ENV_PLUGIN_URL . 'style.css',
        array(),
        TN_ENV_VERSION
    );

    if ('settings_page_env-selector-settings' === $hook_suffix) {
        wp_enqueue_script(
            'tn-environments-settings',
            TN_ENV_PLUGIN_URL . 'scripts/tn-environments.js',
            array(),
            TN_ENV_VERSION,
            true
        );
    }
}
