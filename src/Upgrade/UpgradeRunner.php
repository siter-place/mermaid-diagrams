<?php
/**
 * Database Upgrade Runner.
 *
 * @package WebFalcon\MermaidDiagrams\Upgrade
 */

namespace WebFalcon\MermaidDiagrams\Upgrade;

use WebFalcon\MermaidDiagrams\Upgrade\Migration\CreateUsageTables;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages database migrations and version tracking.
 */
class UpgradeRunner {

	public const OPTION_DB_VERSION = 'mdm_db_version';
	public const TARGET_DB_VERSION = '1.3.1';

	/**
	 * Run required database migrations.
	 *
	 * @return void
	 */
	public function run(): void {
		$current_version = get_option( self::OPTION_DB_VERSION, '0.0.0' );

		if ( version_compare( (string) $current_version, self::TARGET_DB_VERSION, '<' ) ) {
			CreateUsageTables::run();
			update_option( self::OPTION_DB_VERSION, self::TARGET_DB_VERSION );
		}
	}
}
