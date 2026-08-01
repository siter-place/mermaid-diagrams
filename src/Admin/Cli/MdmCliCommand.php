<?php
/**
 * WP-CLI Command for Mermaid Diagrams.
 *
 * @package WebFalcon\MermaidDiagrams\Admin\Cli
 */

namespace WebFalcon\MermaidDiagrams\Admin\Cli;

use WP_CLI;
use WebFalcon\MermaidDiagrams\Bootstrap\Plugin;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Infrastructure\Validation\NodeValidationWorker;
use WebFalcon\MermaidDiagrams\Upgrade\UpgradeRunner;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manage Mermaid Diagrams plugin status, capabilities, and migrations.
 */
class MdmCliCommand {

	/**
	 * Register CLI commands.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::add_command( 'mdm', self::class );
		}
	}

	/**
	 * Show plugin status and environment state.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mdm status
	 *
	 * @when after_wp_load
	 */
	public function status(): void {
		global $wpdb;

		$db_version = get_option( UpgradeRunner::OPTION_DB_VERSION, 'not installed' );
		$cpt_exists = post_type_exists( DiagramPostType::CPT_SLUG );

		$table_usage = $wpdb->prefix . 'mdm_usage';
		$table_dirty = $wpdb->prefix . 'mdm_usage_dirty';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$has_usage = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_usage ) ) === $table_usage;
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$has_dirty = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_dirty ) ) === $table_dirty;

		WP_CLI::line( '=== Mermaid Diagrams Status ===' );
		WP_CLI::line( 'Plugin Version: ' . Plugin::VERSION );
		WP_CLI::line( 'DB Version: ' . $db_version );
		WP_CLI::line( 'CPT Registered (mdm_diagram): ' . ( $cpt_exists ? 'Yes' : 'No' ) );
		WP_CLI::line( 'Usage Table (' . $table_usage . '): ' . ( $has_usage ? 'Present' : 'Missing' ) );
		WP_CLI::line( 'Dirty Table (' . $table_dirty . '): ' . ( $has_dirty ? 'Present' : 'Missing' ) );
	}

	/**
	 * Manage capabilities.
	 *
	 * ## SUBCOMMANDS
	 *
	 * [<action>]
	 * : Action to perform (repair). Default: repair.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mdm capabilities repair
	 *
	 * @when after_wp_load
	 * @param array<string> $args Command arguments.
	 */
	public function capabilities( array $args = array() ): void {
		$action = $args[0] ?? 'repair';

		if ( 'repair' === $action ) {
			DiagramCapabilities::assign_default_capabilities();
			WP_CLI::success( 'Mermaid Diagrams capabilities repaired successfully.' );
			return;
		}

		WP_CLI::error( sprintf( 'Unknown capabilities action "%s". Use "wp mdm capabilities repair".', $action ) );
	}

	/**
	 * Manage usage index (Stub for Phase 09).
	 *
	 * ## SUBCOMMANDS
	 *
	 * [<action>]
	 * : Action to perform (reindex).
	 *
	 * ## EXAMPLES
	 *
	 *     wp mdm usage reindex
	 *
	 * @when after_wp_load
	 * @param array<string> $args Command arguments.
	 */
	public function usage( array $args = array() ): void {
		unset( $args );
		WP_CLI::log( 'Usage indexing and scanning is scheduled for Phase 09.' );
	}

	/**
	 * Validate diagram source string or file using the Node validation worker.
	 *
	 * ## OPTIONS
	 *
	 * [<source>]
	 * : Mermaid diagram source string or path to .mmd file.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mdm validate "flowchart TD\n  A --> B"
	 *
	 * @when after_wp_load
	 * @param array<string> $args Command arguments.
	 */
	public function validate( array $args = array() ): void {
		$raw = $args[0] ?? "flowchart LR\n  A --> B";
		if ( file_exists( $raw ) ) {
			$raw = (string) file_get_contents( $raw );
		}

		$worker = new NodeValidationWorker();
		$result = $worker->validate( $raw );

		if ( ! empty( $result['valid'] ) ) {
			WP_CLI::success( sprintf( 'Valid %s diagram (hash: %s, version: %s)', $result['diagramType'] ?? 'Mermaid', $result['sourceHash'] ?? '', $result['mermaidVersion'] ?? '' ) );
		} else {
			WP_CLI::error( sprintf( 'Validation failed: %s', $result['error'] ?? 'Syntax or constraint error' ) );
		}
	}
}
