<?php

if (!defined('ABSPATH')) {
    exit;
}

// Redirect users with manage_options permission to the Environment Selector Settings page upon login
function redirect_to_env_selector_settings($user_login, $user) {
    // Check if the user has the 'manage_options' capability
    if (user_can($user, 'manage_options')) {
        // Get the current URL path
        $current_url = isset($_SERVER['REQUEST_URI']) ? esc_url_raw(wp_unslash($_SERVER['REQUEST_URI'])) : '';

        // Only redirect if not already on the target page
        if (strpos($current_url, 'options-general.php?page=env-selector-settings') === false) {
            // Set a transient to handle redirection
            set_transient('redirected_to_env_selector_settings_' . $user->ID, true, 5 * MINUTE_IN_SECONDS);
            
            // Redirect to the Environment Selector Settings page
            wp_safe_redirect(admin_url('options-general.php?page=env-selector-settings'));
            exit;
        }
    }
}
add_action('wp_login', 'redirect_to_env_selector_settings', 10, 2);

// Reset the redirection transient on logout
function reset_redirection_transient($user_id) {
    delete_transient('redirected_to_env_selector_settings_' . $user_id);
}
add_action('wp_logout', 'reset_redirection_transient');

// Ensure the user is redirected only once per session
function check_redirect_status() {
    $user_id = get_current_user_id();
    
    if ($user_id) {
        $redirected = get_transient('redirected_to_env_selector_settings_' . $user_id);

        if ($redirected) {
            delete_transient('redirected_to_env_selector_settings_' . $user_id);
            return;
        }
    }
}
add_action('admin_init', 'check_redirect_status');
