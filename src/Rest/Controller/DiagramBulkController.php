<?php
/**
 * REST Diagram Bulk Controller (/mdm/v1/diagrams/bulk).
 *
 * @package WebFalcon\MermaidDiagrams\Rest\Controller
 */

namespace WebFalcon\MermaidDiagrams\Rest\Controller;

use Throwable;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\BulkAssignTermsCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\BulkResultDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DiagramApplicationService;
use WebFalcon\MermaidDiagrams\Support\WordPressErrorMapper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller for bulk operations on diagram IDs.
 */
class DiagramBulkController extends WP_REST_Controller {

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
	protected $rest_base = 'diagrams/bulk';

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
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'bulk_operation' ),
					'permission_callback' => array( $this, 'bulk_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Permission check for bulk operations.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function bulk_permissions_check( $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Process bulk operation request.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function bulk_operation( $request ) {
		try {
			$raw_ids   = (array) $request->get_param( 'ids' );
			$ids       = array_values( array_filter( array_map( 'intval', $raw_ids ) ) );
			$operation = (string) ( $request->get_param( 'operation' ) ?? $request->get_param( 'action' ) ?? '' );

			if ( empty( $operation ) ) {
				$operation = 'add_tags';
			}

			$payload = (array) ( $request->get_param( 'payload' ) ?? array() );
			if ( $request->get_param( 'termIds' ) ) {
				$payload['termIds'] = (array) $request->get_param( 'termIds' );
			}
			if ( $request->get_param( 'categoryIds' ) ) {
				$payload['category_ids'] = (array) $request->get_param( 'categoryIds' );
			}
			if ( $request->get_param( 'tagIds' ) ) {
				$payload['tag_ids'] = (array) $request->get_param( 'tagIds' );
			}
			if ( $request->get_param( 'status' ) ) {
				$payload['status'] = (string) $request->get_param( 'status' );
			}

			if ( empty( $ids ) ) {
				return rest_ensure_response( BulkResultDTO::format( array() ) );
			}

			$command = new BulkAssignTermsCommand( $ids, $operation, $payload );
			$result  = $this->diagram_service->bulk_assign_terms( $command );

			return rest_ensure_response( $result );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}
}
