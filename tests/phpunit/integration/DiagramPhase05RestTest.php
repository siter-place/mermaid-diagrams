<?php
/**
 * Phase 05 REST integration tests: preview, duplicate, filters, bulk.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use WP_REST_Request;
use WP_REST_Server;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

/**
 * Integration tests for Phase 05 library workflow REST endpoints.
 */
class DiagramPhase05RestTest extends TestCase {

	/**
	 * REST Server instance.
	 *
	 * @var WP_REST_Server
	 */
	private WP_REST_Server $server;

	/**
	 * Admin user ID.
	 *
	 * @var int
	 */
	private int $admin_id;

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

		$this->admin_id = (int) wp_insert_user(
			array(
				'user_login' => 'phase05_admin_' . wp_generate_password( 6, false ),
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);
		wp_set_current_user( $this->admin_id );
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
	 * Build validation receipt for test source.
	 *
	 * @param string $source Mermaid source.
	 * @return array<string, mixed>
	 */
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
	 * Create a diagram via REST and return ID.
	 *
	 * @param string $title  Title.
	 * @param string $source Source.
	 * @return int
	 */
	private function create_diagram( string $title, string $source ): int {
		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams' );
		$request->set_body_params(
			array(
				'title'      => $title,
				'source'     => $source,
				'status'     => 'draft',
				'validation' => $this->make_receipt( $source ),
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 201, $response->get_status() );

		return (int) $response->get_data()['id'];
	}

	/**
	 * Preview and duplicate routes are registered.
	 */
	public function test_preview_and_duplicate_routes_registered(): void {
		$routes = $this->server->get_routes();

		$this->assertArrayHasKey( '/mdm/v1/diagrams/(?P<id>[\d]+)/preview', $routes );
		$this->assertArrayHasKey( '/mdm/v1/diagrams/(?P<id>[\d]+)/duplicate', $routes );
	}

	/**
	 * Authorized preview returns source render payload.
	 */
	public function test_preview_returns_authorized_payload(): void {
		$source = "flowchart TD\n  P --> Q";
		$id     = $this->create_diagram( 'Preview Test', $source );

		$request  = new WP_REST_Request( 'GET', '/mdm/v1/diagrams/' . $id . '/preview' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertEquals( $id, $data['id'] );
		$this->assertEquals( $source, $data['source'] );
		$this->assertArrayHasKey( 'renderConfig', $data );
		$this->assertArrayHasKey( 'thumbnail', $data );
	}

	/**
	 * Private diagram preview is forbidden for other users.
	 */
	public function test_preview_forbidden_for_other_user_private_diagram(): void {
		$source = "flowchart TD\n  X --> Y";
		$id     = $this->create_diagram( 'Private Preview', $source );

		wp_update_post(
			array(
				'ID'          => $id,
				'post_status' => 'private',
			)
		);

		$other_id = (int) wp_insert_user(
			array(
				'user_login' => 'phase05_subscriber_' . wp_generate_password( 6, false ),
				'user_pass'  => 'password',
				'role'       => 'subscriber',
			)
		);
		wp_set_current_user( $other_id );

		$request  = new WP_REST_Request( 'GET', '/mdm/v1/diagrams/' . $id . '/preview' );
		$response = $this->server->dispatch( $request );

		$this->assertEquals( 403, $response->get_status() );
	}

	/**
	 * Duplicate route creates a new draft copy.
	 */
	public function test_duplicate_route_creates_copy(): void {
		$source = "flowchart LR\n  A --> B";
		$id     = $this->create_diagram( 'Original Diagram', $source );

		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams/' . $id . '/duplicate' );
		$request->set_body_params(
			array(
				'title' => 'Copied Diagram',
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertNotEquals( $id, $data['id'] );
		$this->assertEquals( 'Copied Diagram', $data['title'] );
		$this->assertEquals( 'draft', $data['status'] );
	}

	/**
	 * Type filter and search combine in list endpoint.
	 */
	public function test_search_and_type_filter(): void {
		$flow_source = "flowchart TD\n  A --> B";
		$seq_source  = "sequenceDiagram\n  Alice->>Bob: Hi";

		$flow_id = $this->create_diagram( 'Flow Filter Test', $flow_source );
		$seq_id  = $this->create_diagram( 'Sequence Filter Test', $seq_source );

		update_post_meta( $flow_id, DiagramMeta::META_DIAGRAM_TYPE, 'flowchart' );
		update_post_meta( $seq_id, DiagramMeta::META_DIAGRAM_TYPE, 'sequenceDiagram' );

		$request = new WP_REST_Request( 'GET', '/mdm/v1/diagrams' );
		$request->set_query_params(
			array(
				'search' => 'Flow Filter',
				'type'   => array( 'flowchart' ),
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );

		$data  = $response->get_data();
		$ids   = wp_list_pluck( $data['items'], 'id' );
		$this->assertContains( $flow_id, $ids );
		$this->assertNotContains( $seq_id, $ids );
		$this->assertNotEmpty( $data['facets']['types'] );
	}

	/**
	 * Bulk operations report partial failures.
	 */
	public function test_bulk_partial_failure(): void {
		$source = "flowchart TD\n  A --> B";
		$id     = $this->create_diagram( 'Bulk Partial', $source );

		$request = new WP_REST_Request( 'POST', '/mdm/v1/diagrams/bulk' );
		$request->set_body_params(
			array(
				'ids'       => array( $id, 999999 ),
				'operation' => 'trash',
			)
		);

		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );

		$data = $response->get_data();
		$this->assertEquals( 2, $data['summary']['requested'] );
		$this->assertEquals( 1, $data['summary']['succeeded'] );
		$this->assertEquals( 1, $data['summary']['failed'] );
	}
}
