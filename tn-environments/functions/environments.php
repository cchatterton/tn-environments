<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Redirect to the environment selection page on plugin activation
function env_selector_activate() {
    update_option('env_selector_activation_redirect', true);
}

// Handle the redirect to the settings page after plugin activation
function env_selector_redirect_to_settings() {
    if (get_option('env_selector_activation_redirect', false)) {
        delete_option('env_selector_activation_redirect');
        wp_safe_redirect(admin_url('options-general.php?page=env-selector-settings'));
        exit;
    }
}
add_action('admin_init', 'env_selector_redirect_to_settings');

// Add the settings page
function env_selector_add_settings_page() {
    add_options_page(
        'Environments',
        'Environments',
        'manage_options',
        'env-selector-settings',
        'env_selector_render_settings_page'
    );
}
add_action('admin_menu', 'env_selector_add_settings_page');

// Get last login for a user
function get_current_user_last_login($user_id) {
global $wpdb;

$user_id = absint($user_id);
$last_login = $wpdb->get_var($wpdb->prepare("
    SELECT s.created
    FROM {$wpdb->prefix}stream s
    JOIN {$wpdb->prefix}stream_context c ON s.context_id = c.context_id
    WHERE s.author = %d
      AND c.connector = 'users'
      AND c.action = 'login'
    ORDER BY s.created DESC
    LIMIT 1
", $user_id));


    return $last_login ?: null;
}

// Fetch user activities within a date range
function get_user_activities($start_date, $end_date, $user_id = null) {
    global $wpdb;

    $stream_table = $wpdb->prefix . 'stream';
    $query = "
        SELECT *
        FROM {$stream_table}
        WHERE created BETWEEN %s AND %s
    ";
    $params = [$start_date, $end_date];

    if ($user_id) {
        $query .= " AND user_id = %d";
        $params[] = $user_id;
    }

    $query .= " ORDER BY created ASC";

    return $wpdb->get_results($wpdb->prepare($query, ...$params));
}

// Summarize activity logs
function summarize_activity($activity_logs) {
    $summary = [];

    foreach ($activity_logs as $log) {
        $connector = $log->connector;
        $action = $log->action;

        $key = "{$connector}_{$action}";

        if (!isset($summary[$key])) {
            $summary[$key] = [
                'connector' => $connector,
                'action' => $action,
                'count' => 0,
                'examples' => []
            ];
        }

        $summary[$key]['count']++;
        $example = $log->summary;
        if (!isset($summary[$key]['examples'][$example])) {
            $summary[$key]['examples'][$example] = 0;
        }
        $summary[$key]['examples'][$example]++;
    }

    return $summary;
}

// Format user summary
function format_user_summary($activity_logs, $user_name) {
    $summary = summarize_activity($activity_logs);
    $current_user_name = wp_get_current_user()->display_name;
    $date_filter = isset($_GET['date']) ? sanitize_key(wp_unslash($_GET['date'])) : '';
    $output = "<div class='user-summary'>";
    $heading = ('week' === $date_filter) ? 'What you have done in the last week' : 'What you did last time you logged in';
    $output .= ($user_name === $current_user_name) ? '<h4>' . esc_html($heading) . '</h4>' : '<h4>What others have been up to in that time</h4>';
    $output .= "<div class='user-summary-details'><h3>" . esc_html($user_name) . "</h3>";

    $exclude = ('week' === $date_filter) ? ['Acf - Deleted', 'Acf - Updated'] : ['Acf - Deleted', 'Acf - Updated', 'Users - Login', 'Users - Logout'];

    foreach ($summary as $key => $item) {
        $connector = ucfirst($item['connector']);
        $action = ucfirst($item['action']);
        $count = $item['count'];
        $examples = $item['examples'];

        if (!in_array($connector . ' - ' . $action, $exclude)) {
            $output .= "<div class='summary-item'>";
            $output .= '<h4>' . esc_html($connector . ' - ' . $action . ' (' . $count . ' actions)') . '</h4>';
            if (!empty($examples)) {
                $output .= '<ul>';
                foreach ($examples as $example => $example_count) {
                    if ($example_count > 1) {
                        $output .= '<li>' . esc_html($example . ' (' . $example_count . ' times)') . '</li>';
                    } else {
                        $output .= '<li>' . esc_html($example) . '</li>';
                    }
                }
                $output .= '</ul>';
            }
            $output .= '</div>';
        }
    }

    $output .= '</div></div>';
    return $output;
}

// Main script to display activities
function display_user_activities() {
    $current_user_id = get_current_user_id();
    $current_user_last_login = get_current_user_last_login($current_user_id);
    $current_time = current_time('mysql');
    $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_time)));
    $date_filter = isset($_GET['date']) ? sanitize_key(wp_unslash($_GET['date'])) : '';
    $start_date = ('week' === $date_filter) ? $seven_days_ago : $current_user_last_login;

    if (!$current_user_last_login) {
        echo "<p>No login activity found for the current user.</p>";
        return;
    }

    // Fetch current user's activities
    $current_user_activities = get_user_activities($start_date, $current_time, $current_user_id);
    $current_user_summary = format_user_summary($current_user_activities, wp_get_current_user()->display_name);

    // Fetch all activities in the same period
    $all_activities = get_user_activities($start_date, $current_time);

    // Group activities by user
    $grouped_activities = [];
    foreach ($all_activities as $activity) {
        $user_id = $activity->user_id;
        if (!isset($grouped_activities[$user_id])) {
            $grouped_activities[$user_id] = [];
        }
        $grouped_activities[$user_id][] = $activity;
    }

    // Exclude the current user
    unset($grouped_activities[$current_user_id]);

    // Generate the HTML output
    $html_output = '<div class="activity-summary" style="display: flex; flex-wrap: nowrap; overflow-x: auto;">';

    // Add current user summary
    $html_output .= '<div class="user-column" style="flex: 0 0 35rem; max-width: 35rem; overflow-y: auto; padding: 0; box-sizing: border-box;">';
    $html_output .= $current_user_summary;
    $html_output .= '</div>';

    // Add other users' summaries
    foreach ($grouped_activities as $user_id => $activities) {
        $user_info = get_userdata($user_id);
        $user_name = $user_info ? $user_info->display_name : "User {$user_id}";

        $html_output .= '<div class="user-column" style="flex: 0 0 35rem; max-width: 35rem; overflow-y: auto; padding: 0; box-sizing: border-box;">';
        $html_output .= format_user_summary($activities, $user_name);
        $html_output .= '</div>';
    }

    $html_output .= '</div>';

    return $html_output;
}

// Hook to render activities
//add_action('admin_notices', 'display_user_activities');

function env_selector_render_settings_page() {

    if (isset($_POST['env_selector_save'])) {
        check_admin_referer('env_selector_save', 'env_selector_nonce');

        $selected_env = isset($_POST['wp_env']) ? sanitize_key(wp_unslash($_POST['wp_env'])) : 'development';
        update_site_option('wp_env_selector', $selected_env);

        if (isset($_POST['env_urls']) && is_array($_POST['env_urls'])) {
            $env_urls = array_map('esc_url_raw', (array) wp_unslash($_POST['env_urls']));
            update_site_option('wp_env_urls', $env_urls);
        }

        set_transient('env_selector_notice', 'Environment and URLs saved successfully.', 5);
        //wp_safe_redirect(admin_url('index.php'));
        
        $save_target = isset($_POST['env_selector_save']) ? sanitize_key(wp_unslash($_POST['env_selector_save'])) : '';

        switch ($save_target) {
    		case 'dashboard':
    			wp_safe_redirect(admin_url());
    			break;
    		
    		case 'stream':
    		    
                global $wpdb;
    
                $user_id = get_current_user_id();
                $last_login = $wpdb->get_var($wpdb->prepare("
                    SELECT created
                    FROM {$wpdb->prefix}stream
                    WHERE user_id = %d
                      AND connector = 'users'
                      AND action IN ('logged_in','login','wp_login')
                    ORDER BY created DESC
                    LIMIT 1
                ", $user_id));
                
    			wp_safe_redirect(admin_url('admin.php?page=wp_stream&date_predefined=custom&date_from=' . rawurlencode((string) $last_login)));
    			break;
    		
    		case 'analytics':
    		    wp_safe_redirect(admin_url('admin.php?page=independent-analytics'));
    		    break;
    		    
    		case 'posts':
    		    wp_safe_redirect(admin_url('edit.php'));
    		    break;
    		    
    		case 'migrate':
    		    wp_safe_redirect(admin_url('tools.php?page=wp-migrate-db-pro'));
    		    break;
    		
    		default:
    			// code...
    			break;
    	}

        exit;
    }

    $stored_env = get_site_option('wp_env_selector', 'development');
    $stored_urls = get_site_option('wp_env_urls', []);
    $environments = ['local', 'development', 'staging', 'production'];

    $current_user = wp_get_current_user();
    $site_name = get_bloginfo('name');
    $discourage_search_engines = get_option('blog_public') ? 'No' : 'Yes';
    $wordpress_version = get_bloginfo('version');

    $current_user_id = get_current_user_id();
    $current_user_last_login = get_current_user_last_login($current_user_id);
    $current_time = current_time('mysql');
    $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days', strtotime($current_time)));
    $date_filter = isset($_GET['date']) ? sanitize_key(wp_unslash($_GET['date'])) : '';
    $start_date = ('week' === $date_filter) ? $seven_days_ago : $current_user_last_login;
    $current_user_activities = get_user_activities($start_date, $current_time, $current_user_id);
    
    // Fetch current user's activity
    $current_user_summary = format_user_summary($current_user_activities, $current_user->display_name);
    
    // Fetch all activities in the same period
    $all_activities = get_user_activities($start_date, $current_time);
    
    // Group activities by user
    $grouped_activities = [];
    foreach ($all_activities as $activity) {
        $user_id = $activity->user_id;
        if (!isset($grouped_activities[$user_id])) {
            $grouped_activities[$user_id] = [];
        }
        $grouped_activities[$user_id][] = $activity;
    }
    
    // Exclude current user
    unset($grouped_activities[$current_user_id]);
    
    // Build horizontal scroll view
    $activity_summary_html = '<div class="activity-row">';
    $activity_summary_html .= '<div class="user-card">' . $current_user_summary . '</div>';
    
    foreach ($grouped_activities as $user_id => $logs) {
        $user_info = get_userdata($user_id);
        if (!$user_info) continue;
        $activity_summary_html .= '<div class="user-card">' .
            format_user_summary($logs, $user_info->display_name) .
        '</div>';
    }
    $activity_summary_html .= '</div>';

    
    
    ?>

    <div class="wrap tn-env-tabs">
        <h1>Welcome back, <?php echo esc_html($current_user->display_name); ?></h1>

        <?php if ($notice = get_transient('env_selector_notice')): ?>
            <div class="notice notice-success is-dismissible"><p><?php echo esc_html($notice); ?></p></div>
            <?php delete_transient('env_selector_notice'); ?>
        <?php endif; ?>

<form method="post" id="env-selector-form" action="">
            <?php wp_nonce_field('env_selector_save', 'env_selector_nonce'); ?>

            <!--<ul class="tabs">-->
            <!--    <li class="tab-link current" data-tab="tab-1">Environments</li>-->
            <!--    <li class="tab-link" data-tab="tab-2">Activity</li>-->
            <!--</ul>-->
            <div id="tab-1" class="tab-content current">
                <h2 class="bottom"><span>1</span>Before entering, please review the following items</h2>
                <table class="form-table summary">
                    <tr><th>Site Name:</th><td><?php echo esc_html($site_name); ?></td><td><a href="/wp-admin/options-general.php">Change Site Name</a></td></tr>
                    <tr><th>Discouraging Search Engines:</th><td><?php echo esc_html($discourage_search_engines); ?></td><td><a href="/wp-admin/options-reading.php">Manage Search Engines</a></td></tr>
                    <tr><th>WordPress Version:</th><td><?php echo esc_html($wordpress_version); ?></td><td><a href="/wp-admin/about.php">More Details</a></td></tr>
                    <tr><th>Current User:</th><td><?php echo esc_html($current_user->user_login); ?> (<?php echo esc_html($current_user->user_email); ?>)</td><td><a href="/wp-login.php?action=logout">Log Out</a></td></tr>
                    <tr><th>User Roles:</th><td><?php echo esc_html(implode(', ', $current_user->roles)); ?></td><td><a href="/wp-admin/profile.php">Edit Profile</a></td></tr>
                </table>
                <table class="form-table environments">
                    <h2><span>2</span>Are the environment details below complete and correct?</h2>
                    <p>If not, please update them before continuing.</p>
                    <p>If you are in the wrong environment, you can switch with the buttons below.</p>
                    <h2 class="top"><span>3</span>Please select a label for this environment.</h2>
                    <tr>
                        
                        <?php foreach ($environments as $env): ?>
                            <td>
                                <span class="wrapper">
                                    <label><input type="radio" name="wp_env" value="<?php echo esc_attr($env); ?>" <?php checked($stored_env, $env); ?>> <?php echo ucfirst($env); ?></label>
                                    <input type="url" name="env_urls[<?php echo esc_attr($env); ?>]" value="<?php echo esc_url($stored_urls[$env] ?? ''); ?>" placeholder="https://example.com">
                                </span>
                                <?php
                                 if($env !== "local" && !empty($stored_urls[$env])) {
                                     echo '<a href="' . esc_url(trailingslashit($stored_urls[$env]) . 'wp-admin') . '" target="_blank" rel="noopener noreferrer">Switch to ' . esc_html($env) . '</a>';
                                 } ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                </table>
                <h2><span>4</span>Save Settings - Where do you want to go next?</strong></h2>
                <button type="submit" class="button" id="save-env-continue" name="env_selector_save" value="dashboard">Dashboard</button>
                <?php if(function_exists( 'IAWP_FS' )) { ?><button type="submit" class="button" id="save-env-continue" name="env_selector_save" value="analytics">Analytics</button><?php } ?>
                <?php if(function_exists('wp_stream_get_instance')) { ?><button type="submit" class="button" id="save-env-continue" name="env_selector_save" value="stream">Stream</button><?php } ?>
                <button type="submit" class="button" id="save-env-continue" name="env_selector_save" value="posts">Posts</button>
                <?php if(function_exists('wpmdb_pro_remove_mu_plugin')) { ?><button type="submit" class="button" id="save-env-continue" name="env_selector_save" value="migrate">WP Migrate</button><?php } ?>
            </div>

            <!--<div id="tab-2" class="tab-content">-->
            <!--    <h3>Activity Summary</h3>-->
            <!--    <?php echo $activity_summary_html; ?>-->
            <!--</div>-->
        </form>
    </div>

<?php
} // end function


// Add the environment as a body class in the admin area
function env_selector_add_body_class($classes) {
    if ($env = get_site_option('wp_env_selector')) {
        $classes .= ' env-' . esc_attr($env);
    }
    return $classes;
}
add_filter('admin_body_class', 'env_selector_add_body_class');

// Add the environment label to the admin bar with a link to the options page
function env_selector_admin_bar_menu($wp_admin_bar) {
    $env = get_site_option('wp_env_selector', 'development');
    $wp_admin_bar->add_node(array(
        'id'    => 'env_selector',
        'title' => 'Environment: ' . ucfirst($env),
        'href'  => admin_url('options-general.php?page=env-selector-settings')
    ));
}
add_action('admin_bar_menu', 'env_selector_admin_bar_menu', 999);

// Display the admin notice
function env_selector_admin_notice() {
    if ($notice = get_transient('env_selector_notice')) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html($notice) . '</p></div>';
        delete_transient('env_selector_notice');
    }
}
add_action('admin_notices', 'env_selector_admin_notice');

?>
