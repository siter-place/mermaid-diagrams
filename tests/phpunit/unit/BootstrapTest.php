<?php
/**
 * Test plugin bootstrap and compatibility classes.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Bootstrap\Compatibility;
use WebFalcon\MermaidDiagrams\Bootstrap\Plugin;

/**
 * Class BootstrapTest
 */
class BootstrapTest extends TestCase {

	/**
	 * Test minimum PHP version constant.
	 */
	public function test_min_php_version_constant(): void {
		$this->assertSame( '8.3', Compatibility::MIN_PHP_VERSION );
	}

	/**
	 * Test minimum WP version constant.
	 */
	public function test_min_wp_version_constant(): void {
		$this->assertSame( '7.0', Compatibility::MIN_WP_VERSION );
	}

	/**
	 * Test plugin version constant.
	 */
	public function test_plugin_version(): void {
		$this->assertSame( '0.0.0-development', Plugin::VERSION );
	}

	/**
	 * Test PHP compatibility on current running PHP environment.
	 */
	public function test_is_php_compatible(): void {
		$this->assertTrue( Compatibility::is_php_compatible() );
	}

	/**
	 * Test plugin singleton instance.
	 */
	public function test_plugin_instance(): void {
		$plugin = Plugin::instance();
		$this->assertInstanceOf( Plugin::class, $plugin );
		$this->assertSame( $plugin, Plugin::instance() );
	}
}
