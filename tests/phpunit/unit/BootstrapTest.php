<?php
/**
 * Test plugin bootstrap and compatibility classes.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use WebFalcon\MermaidDiagrams\Bootstrap\Compatibility;
use WebFalcon\MermaidDiagrams\Bootstrap\Container;
use WebFalcon\MermaidDiagrams\Bootstrap\Plugin;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProviderRegistry;

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
		$this->assertSame( '1.3.1', Plugin::VERSION );
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

	/**
	 * Test container bind, instance, get, and has methods.
	 */
	public function test_container_bindings(): void {
		$container = new Container();

		$this->assertFalse( $container->has( 'service.test' ) );

		$container->bind(
			'service.test',
			function () {
				return new \stdClass();
			}
		);

		$this->assertTrue( $container->has( 'service.test' ) );

		$instance1 = $container->get( 'service.test' );
		$instance2 = $container->get( 'service.test' );

		$this->assertInstanceOf( \stdClass::class, $instance1 );
		$this->assertSame( $instance1, $instance2 );

		$directInstance = new \stdClass();
		$container->instance( 'service.direct', $directInstance );

		$this->assertTrue( $container->has( 'service.direct' ) );
		$this->assertSame( $directInstance, $container->get( 'service.direct' ) );
	}

	/**
	 * Test container throws exception for missing service.
	 */
	public function test_container_throws_for_unbound_service(): void {
		$container = new Container();
		$this->expectException( RuntimeException::class );
		$container->get( 'missing.service' );
	}

	/**
	 * Test service provider registry lifecycle.
	 */
	public function test_service_provider_registry(): void {
		$container = new Container();
		$registry  = new ServiceProviderRegistry( $container );

		$registered = false;
		$booted     = false;

		$provider = new class( $registered, $booted ) implements ServiceProvider {
			public function __construct( private bool &$registeredRef, private bool &$bootedRef ) {}

			public function register( Container $container ): void {
				$this->registeredRef = true;
				$container->instance( 'provider.test', 'value' );
			}

			public function boot(): void {
				$this->bootedRef = true;
			}
		};

		$registry->add_provider( $provider );
		$this->assertFalse( $registered );
		$this->assertFalse( $booted );

		$registry->register_all();
		$this->assertTrue( $registered );
		$this->assertTrue( $container->has( 'provider.test' ) );
		$this->assertFalse( $booted );

		$registry->boot_all();
		$this->assertTrue( $booted );
	}
}
