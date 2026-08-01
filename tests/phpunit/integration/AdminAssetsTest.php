<?php
/**
 * Admin assets integration tests.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use WP_Scripts;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Admin\AdminAssets;
use WebFalcon\MermaidDiagrams\Admin\AdminRoute;
use WebFalcon\MermaidDiagrams\Admin\ScreenBootstrapData;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;

/**
 * Tests admin asset enqueue and bootstrap registration.
 */
class AdminAssetsTest extends TestCase {

	/**
	 * Set up test environment.
	 */
	public function set_up(): void {
		parent::set_up();

		DiagramCapabilities::assign_default_capabilities();
		set_current_screen( 'toplevel_page_mdm-diagrams' );
	}

	/**
	 * Ensure library assets enqueue only on the library screen.
	 */
	public function test_library_assets_enqueue_on_library_screen(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'library_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		AdminAssets::enqueue( AdminRoute::LIBRARY_HOOK );

		$this->assertTrue( wp_script_is( 'mdm-diagram-library', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'mdm-diagram-library', 'enqueued' ) );
	}

	/**
	 * Ensure settings assets enqueue only on the settings screen.
	 */
	public function test_settings_assets_enqueue_on_settings_screen(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'settings_assets_admin',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		AdminAssets::enqueue( AdminRoute::SETTINGS_HOOK );

		$this->assertTrue( wp_script_is( 'mdm-diagram-settings', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'mdm-diagram-settings', 'enqueued' ) );
	}

	/**
	 * Ensure unrelated admin screens do not enqueue plugin assets.
	 */
	public function test_assets_not_enqueued_on_unrelated_screen(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'dashboard_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		AdminAssets::enqueue( 'index.php' );

		$this->assertFalse( wp_script_is( 'mdm-diagram-library', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'mdm-diagram-settings', 'enqueued' ) );
	}

	/**
	 * Ensure bootstrap payload contains expected keys without diagram source.
	 */
	public function test_bootstrap_payload_shape(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'bootstrap_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		$bootstrap = ScreenBootstrapData::for_screen( 'library' );

		$this->assertSame( 'library', $bootstrap['screen'] );
		$this->assertArrayHasKey( 'restRoot', $bootstrap );
		$this->assertArrayHasKey( 'nonce', $bootstrap );
		$this->assertArrayHasKey( 'capabilities', $bootstrap );
		$this->assertTrue( $bootstrap['capabilities']['editDiagrams'] );
		$this->assertTrue( $bootstrap['capabilities']['manageSettings'] );
		$this->assertArrayHasKey( 'routes', $bootstrap );
		$this->assertArrayHasKey( 'defaults', $bootstrap );
		$this->assertArrayHasKey( 'i18n', $bootstrap );
		$this->assertStringNotContainsString( 'flowchart', wp_json_encode( $bootstrap ) );
	}

	/**
	 * Ensure script translations are registered when assets enqueue.
	 */
	public function test_script_translations_registered(): void {
		if ( ! function_exists( 'wp_set_script_translations' ) ) {
			$this->markTestSkipped( 'Script translations unavailable in this environment.' );
		}

		$user_id = wp_insert_user(
			array(
				'user_login' => 'translations_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );

		AdminAssets::enqueue( AdminRoute::LIBRARY_HOOK );

		global $wp_scripts;
		$this->assertInstanceOf( WP_Scripts::class, $wp_scripts );
		$this->assertTrue( isset( $wp_scripts->registered['mdm-diagram-library'] ) );
	}

	/**
	 * Ensure editor role cannot enqueue settings assets.
	 */
	public function test_editor_cannot_enqueue_settings_assets(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'editor_assets_user',
				'user_pass'  => 'password',
				'role'       => 'editor',
			)
		);
		wp_set_current_user( $user_id );

		AdminAssets::enqueue( AdminRoute::SETTINGS_HOOK );

		$this->assertFalse( wp_script_is( 'mdm-diagram-settings', 'enqueued' ) );
	}
}
