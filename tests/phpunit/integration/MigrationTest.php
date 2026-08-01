<?php
/**
 * Integration tests for UpgradeRunner and CreateUsageTables.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Upgrade\UpgradeRunner;

/**
 * Class MigrationTest
 */
class MigrationTest extends TestCase {

	/**
	 * Test migration creates custom usage tables and updates option idempotently.
	 */
	public function test_upgrade_runner_creates_tables(): void {
		global $wpdb;

		$runner = new UpgradeRunner();
		$runner->run();

		$this->assertSame( UpgradeRunner::TARGET_DB_VERSION, get_option( UpgradeRunner::OPTION_DB_VERSION ) );

		$table_usage = $wpdb->prefix . 'mdm_usage';
		$table_dirty = $wpdb->prefix . 'mdm_usage_dirty';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$this->assertSame( $table_usage, $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_usage ) ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching
		$this->assertSame( $table_dirty, $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_dirty ) ) );

		// Repeat execution check for idempotency.
		$runner->run();
		$this->assertSame( UpgradeRunner::TARGET_DB_VERSION, get_option( UpgradeRunner::OPTION_DB_VERSION ) );
	}
}
