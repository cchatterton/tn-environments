<?php
/**
 * Plugin Name: TN Environments
 * Description: Prompts admins to confirm the environment, adds admin environment styling, and displays a build ID.
 * Version: 1.10.1
 * Requires at least: 7.0
 * Requires PHP: 8.5
 * Update URI: https://github.com/cchatterton/tn-environments
 * Author: Techn
 * Author URI: https://techn.com.au
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Techn Controller API: 1
 * Text Domain: tn-environments
 */

if (!defined('ABSPATH')) {
    exit;
}

define('TN_ENV_VERSION', '1.10.1');
define('TN_ENV_PLUGIN_FILE', __FILE__);
define('TN_ENV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TN_ENV_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TN_ENV_PLUGIN_DIR . 'functions/environments.php';
require_once TN_ENV_PLUGIN_DIR . 'functions/styles.php';
require_once TN_ENV_PLUGIN_DIR . 'functions/login.php';
require_once TN_ENV_PLUGIN_DIR . 'functions/build_id.php';
require_once TN_ENV_PLUGIN_DIR . 'functions/assets.php';

register_activation_hook(TN_ENV_PLUGIN_FILE, 'tn_env_on_activation');

function tn_env_on_activation(): void {
    env_selector_activate();
    env_selector_set_default_values();
}

require_once __DIR__ . '/functions/controller-client.php';
tnuc_client_register(__FILE__, 'tn-environments');
