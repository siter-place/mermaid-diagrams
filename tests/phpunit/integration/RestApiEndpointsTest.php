<?php
/**
 * REST API Endpoints Integration Test.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use WP_REST_Request;
use WP_REST_Server;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

/**
 * Integration tests for Phase 02 REST API routes and application services.
 */
class RestApiEndpointsTest extends TestCase {

	/**
	 * REST Server instance.
	 *
	 * @var WP_REST_Server
	 */
	private WP_REST_Server $server;

	/**
	 * Test setup.
	 */
	public function set_up(): void {
		parent::set_up();

		DiagramPostType::register();
		DiagramTaxonomies::register();
		DiagramCapabilities::assign_default_capabilities();

		global $wp_rest_server;
		$this->server = new WP_REST_Server();
		$wp_rest_server = $this->server;

		do_action( 'rest_api_init' );
	}

	/**
	 * Clean up.
	 */
	public function tear_down(): void {
		global $wp_rest_server;
		$wp_rest_server = null;
		parent::tear_down();
	}

	/**
	 * Test REST routes registration.
	 */
	public function test_rest_routes_are_registered(): void {
		$routes = $this->server->get_routes();

		$this->assertArrayHasKey( '/mdm/v1/diagrams', $routes );
		$this->assertArrayHasKey( '/mdm/v1/diagrams/(?P<id>[\d]+)', $routes );
		$this->assertArrayHasKey( '/mdm/v1/diagrams/bulk', $routes );
		$this->assertArrayHasKey( '/mdm/v1/diagrams/(?P<id>[\d]+)/usage', $routes );
		$this->assertArrayHasKey( '/mdm/v1/settings', $routes );
		$this->assertArrayHasKey( '/mdm/v1/settings/(?P<section>[\w\-]+)', $routes );
	}

	/**
	 * Test unauthenticated request is rejected.
	 */
	public function test_unauthenticated_request_is_rejected(): void {
		wp_set_current_user( 0 );

		$request  = new WP_REST_Request( 'GET', '/mdm/v1/diagrams' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 401, $response->get_status() );
	}

	private function make_receipt( string $source ): array {
		$hash = hash( 'sha256', str_replace( array( "\r\n", "\r" ), "\n", $source ) );
		return array(
			'sourceHash'     => 'sha256:' . $hash,
			'mermaidVersion' => '11.4.1',
			'diagramType'    => 'flowchart',
			'validatedAt'    => gmdate( 'Y-m-d\TH:i:s\Z' ),
			'profile'        => 'browser',
		);
	}

	/**
	 * Test create and get diagram via REST API.
	 */
	public function test_create_and_get_diagram(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'rest_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );

		$source  = "flowchart TD\n  A --> B";
		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$request->set_header( 'Content-Type', 'application/json' );
		$request->set_body_params(
			array(
				'title'          => 'REST Test Flow',
				'source'         => $source,
				'description'    => 'Integration test diagram',
				'status'         => 'draft',
				'idempotencyKey' => 'unit-test-idem-1',
				'validation'     => $this->make_receipt( $source ),
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 201, $response->get_status() );

		$data = $response->get_data();
		$this->assertArrayHasKey( 'id', $data );
		$this->assertEquals( 'REST Test Flow', $data['title'] );
		$this->assertNotEmpty( $data['versionToken'] );

		$diagram_id = $data['id'];

		// Test GET single item.
		$get_request  = new WP_REST_Request( 'GET', '/mdm/v1/diagrams/' . $diagram_id );
		$get_response = $this->server->dispatch( $get_request );

		$this->assertEquals( 200, $get_response->get_status() );
		$get_data = $get_response->get_data();
		$this->assertEquals( $diagram_id, $get_data['id'] );
		$this->assertEquals( "flowchart TD\n  A --> B", $get_data['source'] );

		// Test GET source download.
		$dl_request  = new WP_REST_Request( 'GET', '/mdm/v1/diagrams/' . $diagram_id . '/source' );
		$dl_response = $this->server->dispatch( $dl_request );
		$this->assertEquals( 200, $dl_response->get_status() );
		$this->assertEquals( "flowchart TD\n  A --> B", $dl_response->get_data() );
	}

	/**
	 * Test optimistic concurrency control 409 conflict.
	 */
	public function test_optimistic_concurrency_conflict(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'concurrency_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );

		// Create initial diagram.
		$source     = 'graph TD; X-->Y;';
		$create_req = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$create_req->set_body_params(
			array(
				'title'      => 'Concurrency Test',
				'source'     => $source,
				'validation' => $this->make_receipt( $source ),
			)
		);
		$created    = $this->server->dispatch( $create_req )->get_data();
		$diagram_id = $created['id'];
		$v1_token   = $created['versionToken'];

		// Perform successful update with valid version token.
		$update1_req = new WP_REST_Request( 'PATCH', '/mdm/v1/diagrams/' . $diagram_id );
		$update1_req->set_body_params(
			array(
				'title'           => 'Updated Title 1',
				'expectedVersion' => $v1_token,
			)
		);
		$updated1   = $this->server->dispatch( $update1_req )->get_data();
		$this->assertEquals( 'Updated Title 1', $updated1['title'] );
		$v2_token   = $updated1['versionToken'];
		$this->assertNotEquals( $v1_token, $v2_token );

		// Perform update with stale v1_token -> expect 409 Conflict!
		$update2_req = new WP_REST_Request( 'PATCH', '/mdm/v1/diagrams/' . $diagram_id );
		$update2_req->set_body_params(
			array(
				'title'           => 'Stale Title Attempt',
				'expectedVersion' => $v1_token,
			)
		);
		$response2  = $this->server->dispatch( $update2_req );
		$this->assertEquals( 409, $response2->get_status() );
		$error_data = $response2->get_data();
		$this->assertEquals( 'mdm_edit_conflict', $error_data['code'] );
	}

	/**
	 * Test settings GET and PATCH endpoints.
	 */
	public function test_settings_get_and_patch(): void {
		$user_id = wp_insert_user(
			array(
				'user_login' => 'settings_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		$this->assertIsInt( $user_id );
		wp_set_current_user( $user_id );

		// Test GET settings.
		$get_req  = new WP_REST_Request( 'GET', '/mdm/v1/settings' );
		$get_res  = $this->server->dispatch( $get_req );
		$this->assertEquals( 200, $get_res->get_status() );
		$settings = $get_res->get_data();
		$this->assertArrayHasKey( 'values', $settings );
		$this->assertArrayHasKey( 'rendering', $settings['values'] );

		// Test PATCH section.
		$patch_req = new WP_REST_Request( 'PATCH', '/mdm/v1/settings/rendering' );
		$patch_req->set_header( 'Content-Type', 'application/json' );
		$patch_req->set_body( wp_json_encode( array(
			'defaultTheme'  => 'dark',
			'defaultHeight' => 600,
		) ) );
		$patch_res = $this->server->dispatch( $patch_req );
		$this->assertEquals( 200, $patch_res->get_status() );
		$updated   = $patch_res->get_data();
		$this->assertEquals( 'dark', $updated['defaultTheme'] );
		$this->assertEquals( 600, $updated['defaultHeight'] );
	}
}
