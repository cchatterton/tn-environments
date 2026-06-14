<?php
/**
 * Build ID generation and admin footer display.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * ============================================================
 * BUILD ID HELPERS
 * ============================================================
 */

/**
 * Parse Version: X.Y(.Z) from function file
 */
function tn_env_parse_function_version( $file ): array {
	$contents = @file_get_contents( $file );
	if ( ! $contents ) {
		return [ 0, 0 ];
	}

	if ( preg_match( '/Version:\s*([0-9]+)\.([0-9]+)/i', $contents, $m ) ) {
		return [
			(int) $m[1], // major
			(int) $m[2], // minor
		];
	}

	return [ 0, 0 ];
}

/**
 * Sum MAJOR and MINOR versions across ACTIVE function files
 */
function tn_env_get_function_version_sums(): array {
	$fn_dir = trailingslashit( get_stylesheet_directory() ) . 'functions';
	$files  = glob( $fn_dir . '/*.php' ) ?: [];

	$disabled = get_option( 'theme_disabled_functions', [] );
	if ( ! is_array( $disabled ) ) {
		$disabled = [];
	}

	$major_sum = 0;
	$minor_sum = 0;

	foreach ( $files as $file ) {
		$base = basename( $file );

		// Skip disabled functions
		if ( in_array( $base, $disabled, true ) ) {
			continue;
		}

		[ $major, $minor ] = tn_env_parse_function_version( $file );
		$major_sum += $major;
		$minor_sum += $minor;
	}

	return [
		'major' => $major_sum,
		'minor' => $minor_sum,
	];
}

/**
 * Plugin counts
 */
function tn_env_get_plugin_counts(): array {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$all_plugins = get_plugins();
	$total       = count( $all_plugins );

	$active = (array) get_option( 'active_plugins', [] );

	// Multisite support
	if ( is_multisite() ) {
		$network = (array) get_site_option( 'active_sitewide_plugins', [] );
		$active  = array_merge( $active, array_keys( $network ) );
	}

	$active = array_unique( $active );

	return [
		'active'   => count( $active ),
		'inactive' => max( 0, $total - count( $active ) ),
	];
}

/**
 * Compute full Build ID
 */
function tn_env_compute_build_id(): string {
	$wp_version = get_bloginfo( 'version' );

	$fn  = tn_env_get_function_version_sums();
	$pl  = tn_env_get_plugin_counts();

	return sprintf(
		'%s-%d.%d-%d.%d',
		$wp_version,
		$fn['major'],
		$fn['minor'],
		$pl['active'],
		$pl['inactive']
	);
}

/**
 * ============================================================
 * PERSIST BUILD ID
 * ============================================================
 */
function tn_env_update_build_id() {
	update_option( 'tn_build_id', tn_env_compute_build_id(), false );
}

/**
 * Generate on admin login (admins only)
 */
function tn_env_update_build_id_on_admin_login( $user_login, $user ) {
	if ( user_can( $user, 'manage_options' ) ) {
		tn_env_update_build_id();
	}
}
add_action( 'wp_login', 'tn_env_update_build_id_on_admin_login', 20, 2 );

/**
 * Safety net: recompute once per admin load if missing
 */
function tn_env_maybe_update_missing_build_id() {
	if ( ! get_option( 'tn_build_id' ) ) {
		tn_env_update_build_id();
	}
}
add_action( 'admin_init', 'tn_env_maybe_update_missing_build_id' );

/**
 * ============================================================
 * ADMIN FOOTER OVERRIDE
 * ============================================================
 */
function tn_env_update_footer_text() {
	$build = get_option( 'tn_build_id' );
	return $build ? 'Build ' . esc_html( $build ) : '';
}
add_filter( 'update_footer', 'tn_env_update_footer_text', 999 );

/**
 * Optional: hide default WP version entirely
 */
add_filter( 'core_version_check_enabled', '__return_false' );
