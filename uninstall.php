<?php
/**
 * Plugin Uninstall Handler.
 *
 * @package WebFalcon\MermaidDiagrams
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Perform uninstall cleanup based on configured data retention policy.
 */
function mdm_uninstall_plugin(): void {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$settings = get_option( 'mdm_settings', array() );
	$policy   = $settings['data_retention']['uninstall_action'] ?? 'preserve';

	if ( 'preserve' === $policy ) {
		// Preserving content and settings by default.
		return;
	}

	global $wpdb;

	if ( 'delete_all' === $policy ) {
		// Delete all diagram posts and terms.
		$diagram_ids = get_posts(
			array(
				'post_type'      => 'mdm_diagram',
				'post_status'    => 'any',
				'numberposts'    => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		foreach ( $diagram_ids as $diagram_id ) {
			wp_delete_post( $diagram_id, true );
		}

		// Drop custom usage tables.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mdm_usage, {$wpdb->prefix}mdm_usage_dirty" );
	}

	if ( in_array( $policy, array( 'delete_settings', 'delete_all' ), true ) ) {
		delete_option( 'mdm_settings' );
		delete_option( 'mdm_db_version' );
	}
}

if ( is_multisite() ) {
	$sites = get_sites( array( 'fields' => 'ids' ) );
	foreach ( $sites as $site_id ) {
		switch_to_blog( $site_id );
		mdm_uninstall_plugin();
		restore_current_blog();
	}
} else {
	mdm_uninstall_plugin();
}
