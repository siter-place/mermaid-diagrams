<?php
/**
 * REST Diagram Collection Controller (/mdm/v1/diagrams).
 *
 * @package WebFalcon\MermaidDiagrams\Rest\Controller
 */

namespace WebFalcon\MermaidDiagrams\Rest\Controller;

use Throwable;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\CreateDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\SearchDiagramsQuery;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DiagramApplicationService;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;
use WebFalcon\MermaidDiagrams\Support\WordPressErrorMapper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller for diagram collection endpoint (/mdm/v1/diagrams).
 */
class DiagramCollectionController extends WP_REST_Controller {

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
	protected $rest_base = 'diagrams';

	/**
	 * Constructor.
	 *
	 * @param DiagramApplicationService $diagram_service Application service instance.
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
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'get_items_permissions_check' ),
					'args'                => $this->get_collection_params(),
				),
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'create_item_permissions_check' ),
					'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
				),
			)
		);
	}

	/**
	 * Permission check for reading collection.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function get_items_permissions_check( $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Permission check for creating a diagram.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function create_item_permissions_check( $request ): bool {
		return current_user_can( 'edit_posts' );
	}

	/**
	 * Get diagram collection items.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_items( $request ) {
		try {
			$search       = $request->get_param( 'search' );
			$category_ids = (array) $request->get_param( 'category' );
			$tag_ids      = (array) $request->get_param( 'tag' );
			$types        = (array) $request->get_param( 'type' );
			$statuses     = (array) $request->get_param( 'status' );
			$authors      = (array) $request->get_param( 'author' );
			$page         = (int) ( $request->get_param( 'page' ) ?: 1 );
			$per_page     = (int) ( $request->get_param( 'per_page' ) ?: 20 );
			$orderby      = (string) ( $request->get_param( 'orderby' ) ?: 'modified' );
			$order        = (string) ( $request->get_param( 'order' ) ?: 'DESC' );
			$view         = (string) ( $request->get_param( 'view' ) ?: 'summary' );

			$query = new SearchDiagramsQuery(
				$search,
				$category_ids,
				$tag_ids,
				$types,
				$statuses,
				$authors,
				$page,
				$per_page,
				$orderby,
				$order,
				$view
			);

			$result   = $this->diagram_service->search_diagrams( $query );
			$response = rest_ensure_response( $result );

			$response->header( 'X-WP-Total', (string) $result['pagination']['totalItems'] );
			$response->header( 'X-WP-TotalPages', (string) $result['pagination']['totalPages'] );

			return $response;
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Create a new diagram item.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function create_item( $request ) {
		try {
			$title_str       = (string) $request->get_param( 'title' );
			$source_str      = (string) $request->get_param( 'source' );
			$desc_str        = (string) ( $request->get_param( 'description' ) ?? '' );
			$status_str      = (string) ( $request->get_param( 'status' ) ?? 'draft' );
			$category_ids    = (array) ( $request->get_param( 'categoryIds' ) ?? array() );
			$tag_ids         = (array) ( $request->get_param( 'tagIds' ) ?? array() );
			$render_config   = (array) ( $request->get_param( 'renderConfig' ) ?? array() );
			$idempotency_key = $request->get_param( 'idempotencyKey' );
			$validation_data = $request->get_param( 'validation' );
			$receipt         = is_array( $validation_data ) ? ValidationReceipt::from_array( $validation_data ) : null;
			$writer_profile  = (string) ( $request->get_header( 'X-MDM-Writer-Profile' ) ?? 'browser' );
			if ( empty( $writer_profile ) ) {
				$writer_profile = 'browser';
			}

			$command = new CreateDiagramCommand(
				new DiagramTitle( $title_str ),
				new DiagramSource( $source_str ),
				new DiagramDescription( $desc_str ),
				new DiagramStatus( $status_str ),
				$category_ids,
				$tag_ids,
				new RenderConfig( $render_config ),
				$receipt,
				null !== $idempotency_key ? (string) $idempotency_key : null,
				get_current_user_id(),
				$writer_profile
			);

			$detail   = $this->diagram_service->create_diagram( $command );
			$response = rest_ensure_response( $detail );
			$response->set_status( 201 );

			return $response;
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Get query parameters schema for collection.
	 *
	 * @return array<string, mixed>
	 */
	public function get_collection_params(): array {
		return array(
			'search'   => array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
			'category' => array(
				'type'  => 'array',
				'items' => array( 'type' => 'integer' ),
			),
			'tag'      => array(
				'type'  => 'array',
				'items' => array( 'type' => 'integer' ),
			),
			'type'     => array(
				'type'  => 'array',
				'items' => array( 'type' => 'string' ),
			),
			'status'   => array(
				'type'  => 'array',
				'items' => array( 'type' => 'string' ),
			),
			'author'   => array(
				'type'  => 'array',
				'items' => array( 'type' => 'integer' ),
			),
			'page'     => array(
				'type'    => 'integer',
				'default' => 1,
				'minimum' => 1,
			),
			'per_page' => array(
				'type'    => 'integer',
				'default' => 20,
				'minimum' => 1,
				'maximum' => 100,
			),
			'orderby'  => array(
				'type'    => 'string',
				'default' => 'modified',
				'enum'    => array( 'modified', 'title' ),
			),
			'order'    => array(
				'type'    => 'string',
				'default' => 'DESC',
				'enum'    => array( 'ASC', 'DESC', 'asc', 'desc' ),
			),
			'view'     => array(
				'type'    => 'string',
				'default' => 'summary',
				'enum'    => array( 'summary', 'detail', 'selector' ),
			),
		);
	}
}
