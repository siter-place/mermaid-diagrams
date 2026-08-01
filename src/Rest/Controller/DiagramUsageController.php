<?php
/**
 * REST Diagram Usage Controller (/mdm/v1/diagrams/{id}/usage).
 *
 * @package WebFalcon\MermaidDiagrams\Rest\Controller
 */

namespace WebFalcon\MermaidDiagrams\Rest\Controller;

use Throwable;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\GetDiagramUsageQuery;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DiagramApplicationService;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Support\WordPressErrorMapper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller for retrieving usage references of a diagram.
 */
class DiagramUsageController extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'mdm/v1';

	/**
	 * REST route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'diagrams/(?P<id>[\d]+)/usage';

	/**
	 * Constructor.
	 *
	 * @param DiagramApplicationService $diagram_service Application service.
	 */
	public function __construct(
		private DiagramApplicationService $diagram_service
	) {
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_usage' ),
					'permission_callback' => array( $this, 'get_usage_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Permission check for getting diagram usage.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function get_usage_permissions_check( $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'read_post', $id ) || current_user_can( 'edit_post', $id );
	}

	/**
	 * Get usage details.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_usage( $request ) {
		try {
			$id_val = (int) $request->get_param( 'id' );
			$query  = new GetDiagramUsageQuery( new DiagramId( $id_val ) );
			$usage  = $this->diagram_service->get_diagram_usage( $query );

			return rest_ensure_response( $usage );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}
}
