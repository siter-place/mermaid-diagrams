<?php
/**
 * Validation Enforcement Integration Test.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use WP_REST_Request;
use WP_REST_Server;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;

/**
 * Integration test verifying validation receipt enforcement on REST endpoints.
 */
class ValidationEnforcementTest extends TestCase {

	private WP_REST_Server $server;

	public function set_up(): void {
		parent::set_up();

		DiagramPostType::register();
		DiagramCapabilities::assign_default_capabilities();

		global $wp_rest_server;
		$this->server   = new WP_REST_Server();
		$wp_rest_server = $this->server;

		do_action( 'rest_api_init' );

		$user_id = wp_insert_user(
			array(
				'user_login' => 'val_enforce_admin',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $user_id );
	}

	public function tear_down(): void {
		global $wp_rest_server;
		$wp_rest_server = null;
		parent::tear_down();
	}

	public function test_rejects_create_without_validation_receipt(): void {
		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body_params(
			array(
				'title'  => 'Unvalidated Diagram',
				'source' => "flowchart LR\n  A --> B",
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 422, $response->get_status() );

		$data = $response->get_data();
		$this->assertEquals( 'mdm_invalid_mermaid', $data['code'] );
	}

	public function test_rejects_create_with_mismatched_validation_receipt(): void {
		$source = "flowchart LR\n  A --> B";
		$hash   = hash( 'sha256', "flowchart LR\n  X --> Y" ); // Wrong source

		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body_params(
			array(
				'title'      => 'Mismatched Diagram',
				'source'     => $source,
				'validation' => array(
					'sourceHash'     => 'sha256:' . $hash,
					'mermaidVersion' => '11.4.1',
					'diagramType'    => 'flowchart',
					'validatedAt'    => gmdate( 'Y-m-d\TH:i:s\Z' ),
					'profile'        => 'browser',
				),
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 422, $response->get_status() );
		$this->assertEquals( 'mdm_invalid_mermaid', $response->get_data()['code'] );
	}

	public function test_rejects_autonomous_write_with_browser_receipt(): void {
		$source = "flowchart LR\n  A --> B";
		$hash   = hash( 'sha256', $source );

		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_header( 'X-MDM-Writer-Profile', 'autonomous' );
		$request->set_body_params(
			array(
				'title'      => 'Autonomous Diagram',
				'source'     => $source,
				'validation' => array(
					'sourceHash'     => 'sha256:' . $hash,
					'mermaidVersion' => '11.4.1',
					'diagramType'    => 'flowchart',
					'validatedAt'    => gmdate( 'Y-m-d\TH:i:s\Z' ),
					'profile'        => 'browser', // Browser receipt instead of worker
				),
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 422, $response->get_status() );
		$this->assertEquals( 'mdm_invalid_mermaid', $response->get_data()['code'] );
	}
}
