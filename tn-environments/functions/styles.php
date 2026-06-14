<?php
// Ensure this file is not accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Set default values on plugin activation
function env_selector_set_default_values() {
    $default_css = array(
        'local'       => "/* BLUE */\n.env-local #wpwrap {\n    background-color: #06204a11;\n}\n.env-local li#wp-admin-bar-env_selector a {\n    background-color: #0c3f91;\n}\n.env-local div#wpadminbar, .env-local ul#adminmenu, .env-local #adminmenuwrap, .env-local #adminmenu .wp-submenu, .env-local #adminmenuback  {\n    background-color: #06204a;\n}\n.env-local #adminmenu li.menu-top:hover, .env-local #adminmenu li.opensub > a.menu-top, .env-local #adminmenu li > a.menu-top:focus {\n    background-color: #06204a;\n}",
        'development'       => "/* RED */\n.env-development #wpwrap {\n    background-color: #42032211;\n}\n.env-development li#wp-admin-bar-env_selector a {\n    background-color: #a20954;\n}\n.env-development div#wpadminbar, .env-development ul#adminmenu, .env-development #adminmenuwrap, .env-development #adminmenu .wp-submenu, .env-development #adminmenuback  {\n    background-color: #420322;\n}\n.env-development #adminmenu li.menu-top:hover, .env-development #adminmenu li.opensub > a.menu-top, .env-development #adminmenu li > a.menu-top:focus {\n    background-color: #420322;\n}",
        'staging'   => "/* PURPLE */\n.env-staging #wpwrap {\n    background-color: #40004211;\n}\n.env-staging li#wp-admin-bar-env_selector a {\n    background-color: #840486;\n}\n.env-staging div#wpadminbar, .env-staging ul#adminmenu, .env-staging #adminmenuwrap, .env-staging #adminmenu .wp-submenu, .env-staging #adminmenuback  {\n    background-color: #400042;\n}\n.env-staging #adminmenu li.menu-top:hover, .env-staging #adminmenu li.opensub > a.menu-top, .env-staging #adminmenu li > a.menu-top:focus {\n    background-color: #400042;\n}",
        'production' => "/* GREEN */\n.env-production #wpwrap {\n    background-color: #03423f11;\n}\n.env-production li#wp-admin-bar-env_selector {\n    background: #067f79;\n}\n.env-production div#wpadminbar, .env-production ul#adminmenu, .env-production #adminmenuwrap, .env-production #adminmenu .wp-submenu, .env-production #adminmenuback  {\n    background-color: #03423f;\n}\n.env-production #adminmenu li.menu-top:hover, .env-production #adminmenu li.opensub > a.menu-top, .env-production #adminmenu li > a.menu-top:focus {\n   background-color: #03423f;\n}",
    );
    update_site_option('wp_env_selector_css', $default_css);
}
// Render the Admin Style settings page
function env_selector_render_admin_style_page() {
    if (isset($_POST['admin_style_save'])) {
        check_admin_referer('admin_style_save', 'admin_style_nonce');
        
        $css = array(
            'local'       => isset($_POST['css_local']) ? sanitize_textarea_field(wp_unslash($_POST['css_local'])) : '',
            'development' => isset($_POST['css_development']) ? sanitize_textarea_field(wp_unslash($_POST['css_development'])) : '',
            'staging'     => isset($_POST['css_staging']) ? sanitize_textarea_field(wp_unslash($_POST['css_staging'])) : '',
            'production'  => isset($_POST['css_production']) ? sanitize_textarea_field(wp_unslash($_POST['css_production'])) : '',
        );
        update_site_option('wp_env_selector_css', $css);
        
        echo '<div id="message" class="updated"><p>Custom styles saved.</p></div>';
    }

    $css = get_site_option('wp_env_selector_css', array(
        'local'       => "/* BLUE */\n.env-local #wpwrap {\n    background-color: #06204a11;\n}\n.env-local li#wp-admin-bar-env_selector a {\n    background-color: #0c3f91;\n}\n.env-local div#wpadminbar, .env-local ul#adminmenu, .env-local #adminmenuwrap, .env-local #adminmenu .wp-submenu, .env-local #adminmenuback  {\n    background-color: #06204a;\n}\n.env-local #adminmenu li.menu-top:hover, .env-local #adminmenu li.opensub > a.menu-top, .env-local #adminmenu li > a.menu-top:focus {\n    background-color: #06204a;\n}",
        'development'       => "/* RED */\n.env-development #wpwrap {\n    background-color: #42032211;\n}\n.env-development li#wp-admin-bar-env_selector a {\n    background-color: #a20954;\n}\n.env-development div#wpadminbar, .env-development ul#adminmenu, .env-development #adminmenuwrap, .env-development #adminmenu .wp-submenu, .env-development #adminmenuback  {\n    background-color: #420322;\n}\n.env-development #adminmenu li.menu-top:hover, .env-development #adminmenu li.opensub > a.menu-top, .env-development #adminmenu li > a.menu-top:focus {\n    background-color: #420322;\n}",
        'staging'   => "/* PURPLE */\n.env-staging #wpwrap {\n    background-color: #40004211;\n}\n.env-staging li#wp-admin-bar-env_selector a {\n    background-color: #840486;\n}\n.env-staging div#wpadminbar, .env-staging ul#adminmenu, .env-staging #adminmenuwrap, .env-staging #adminmenu .wp-submenu, .env-staging #adminmenuback  {\n    background-color: #400042;\n}\n.env-staging #adminmenu li.menu-top:hover, .env-staging #adminmenu li.opensub > a.menu-top, .env-staging #adminmenu li > a.menu-top:focus {\n    background-color: #400042;\n}",
        'production' => "/* GREEN */\n.env-production #wpwrap {\n    background-color: #03423f11;\n}\n.env-production li#wp-admin-bar-env_selector {\n    background: #067f79;\n}\n.env-production div#wpadminbar, .env-production ul#adminmenu, .env-production #adminmenuwrap, .env-production #adminmenu .wp-submenu, .env-production #adminmenuback  {\n    background-color: #03423f;\n}\n.env-production #adminmenu li.menu-top:hover, .env-production #adminmenu li.opensub > a.menu-top, .env-production #adminmenu li > a.menu-top:focus {\n   background-color: #03423f;\n}",
    ));
    ?>
    <div class="wrap">
        <h1>Admin Style Settings</h1>
        <form method="post">
            <?php wp_nonce_field('admin_style_save', 'admin_style_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Local CSS</th>
                    <td>
                        <textarea name="css_local" rows="10" cols="50"><?php echo esc_textarea(isset($css['local']) ? $css['local'] : ''); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Development CSS</th>
                    <td>
                        <textarea name="css_development" rows="10" cols="50"><?php echo esc_textarea(isset($css['development']) ? $css['development'] : ''); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Staging CSS</th>
                    <td>
                        <textarea name="css_staging" rows="10" cols="50"><?php echo esc_textarea(isset($css['staging']) ? $css['staging'] : ''); ?></textarea>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Production CSS</th>
                    <td>
                        <textarea name="css_production" rows="10" cols="50"><?php echo esc_textarea(isset($css['production']) ? $css['production'] : ''); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php submit_button('Save Styles', 'primary', 'admin_style_save'); ?>
        </form>
    </div>
    <?php
}

// Add the settings pages
function env_selector_add_settings_pages() {
    add_options_page(
        'Admin Style',
        'Admin Style',
        'manage_options',
        'admin-style-settings',
        'env_selector_render_admin_style_page'
    );
}
add_action('admin_menu', 'env_selector_add_settings_pages');

// Apply custom CSS to admin pages based on the environment
function env_selector_apply_admin_css() {
    $current_env = get_site_option('wp_env_selector', 'development');
    $css_options = get_site_option('wp_env_selector_css', array());

    if (isset($css_options[$current_env])) {
        $custom_css = $css_options[$current_env];
        echo '<style>
        ' . esc_html($custom_css) . '
        </style>';
    }
}
add_action('admin_head', 'env_selector_apply_admin_css');
?>
