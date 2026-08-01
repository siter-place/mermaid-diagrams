<?php
/**
 * Migration: Create Usage Index Tables.
 *
 * @package WebFalcon\MermaidDiagrams\Upgrade\Migration
 */

namespace WebFalcon\MermaidDiagrams\Upgrade\Migration;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Migration for creating mdm_usage and mdm_usage_dirty custom tables.
 */
class CreateUsageTables {

	/**
	 * Execute dbDelta table creation.
	 *
	 * @return void
	 */
	public static function run(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();

		$table_usage = $wpdb->prefix . 'mdm_usage';
		$sql_usage   = "CREATE TABLE {$table_usage} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			diagram_id bigint(20) unsigned NOT NULL,
			consumer_id bigint(20) unsigned NOT NULL,
			consumer_type varchar(50) NOT NULL DEFAULT 'post',
			block_key varchar(100) NOT NULL DEFAULT '',
			consumer_status varchar(20) NOT NULL DEFAULT 'publish',
			source_revision bigint(20) unsigned NOT NULL DEFAULT 0,
			first_seen datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			last_seen datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (id),
			KEY diagram_id (diagram_id),
			KEY consumer_id (consumer_id),
			KEY consumer_type (consumer_type)
		) {$charset_collate};";

		$table_dirty = $wpdb->prefix . 'mdm_usage_dirty';
		$sql_dirty   = "CREATE TABLE {$table_dirty} (
			consumer_id bigint(20) unsigned NOT NULL,
			enqueued_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			PRIMARY KEY  (consumer_id)
		) {$charset_collate};";

		dbDelta( $sql_usage );
		dbDelta( $sql_dirty );
	}
}
