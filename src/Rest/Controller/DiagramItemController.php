<?php
/**
 * REST Diagram Item Controller (/mdm/v1/diagrams/{id}).
 *
 * @package WebFalcon\MermaidDiagrams\Rest\Controller
 */

namespace WebFalcon\MermaidDiagrams\Rest\Controller;

use Throwable;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\TrashDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\UpdateDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\GetDiagramQuery;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DiagramApplicationService;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DownloadPolicyService;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramRepository;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\DiagramNotFoundException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\WordPressDiagramRepository;
use WebFalcon\MermaidDiagrams\Support\WordPressErrorMapper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller for single diagram REST endpoints (/mdm/v1/diagrams/{id}).
 */
class DiagramItemController extends WP_REST_Controller {

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
	 * Repository instance.
	 *
	 * @var DiagramRepository
	 */
	private DiagramRepository $repository;

	/**
	 * Download policy service instance.
	 *
	 * @var DownloadPolicyService
	 */
	private DownloadPolicyService $download_service;

	/**
	 * Constructor.
	 *
	 * @param DiagramApplicationService $diagram_service Application service instance.
	 * @param DiagramRepository|null    $repository      Diagram repository instance.
	 * @param DownloadPolicyService|null $download_service Download policy service instance.
	 */
	public function __construct(
		private DiagramApplicationService $diagram_service,
		?DiagramRepository $repository = null,
		?DownloadPolicyService $download_service = null
	) {
		$this->repository       = $repository ?? new WordPressDiagramRepository();
		$this->download_service = $download_service ?? new DownloadPolicyService();
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item' ),
					'permission_callback' => array( $this, 'get_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
				array(
					'methods'             => WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'delete_item_permissions_check' ),
					'args'                => array(
						'force' => array(
							'type'    => 'boolean',
							'default' => false,
						),
					),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/categories',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_categories' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/tags',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_tags' ),
					'permission_callback' => array( $this, 'update_item_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/source',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item_source_download' ),
					'permission_callback' => array( $this, 'get_item_download_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<id>[\d]+)/svg',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_item_svg_download' ),
					'permission_callback' => array( $this, 'get_item_download_permissions_check' ),
				),
			)
		);
	}

	/**
	 * Permission check for reading a single diagram.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function get_item_permissions_check( $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'read_post', $id ) || current_user_can( 'edit_post', $id );
	}

	/**
	 * Permission check for updating a diagram.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function update_item_permissions_check( $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'edit_post', $id );
	}

	/**
	 * Permission check for deleting a diagram.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function delete_item_permissions_check( $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'delete_post', $id );
	}

	/**
	 * Get single diagram item.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_item( $request ) {
		try {
			$id_val = (int) $request->get_param( 'id' );
			$query  = new GetDiagramQuery( new DiagramId( $id_val ) );
			$detail = $this->diagram_service->get_diagram( $query );

			return rest_ensure_response( $detail );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Update diagram item with optimistic concurrency token check.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function update_item( $request ) {
		try {
			$id_val           = (int) $request->get_param( 'id' );
			$title_str        = $request->get_param( 'title' );
			$source_str       = $request->get_param( 'source' );
			$desc_str         = $request->get_param( 'description' );
			$status_str       = $request->get_param( 'status' );
			$category_ids     = $request->get_param( 'categoryIds' );
			$tag_ids          = $request->get_param( 'tagIds' );
			$render_config    = $request->get_param( 'renderConfig' );
			$expected_version = $request->get_param( 'expectedVersion' );
			$validation_data  = $request->get_param( 'validation' );
			$receipt          = is_array( $validation_data ) ? ValidationReceipt::from_array( $validation_data ) : null;
			$writer_profile   = (string) ( $request->get_header( 'X-MDM-Writer-Profile' ) ?? 'browser' );
			if ( empty( $writer_profile ) ) {
				$writer_profile = 'browser';
			}

			$command = new UpdateDiagramCommand(
				new DiagramId( $id_val ),
				null !== $title_str ? new DiagramTitle( (string) $title_str ) : null,
				null !== $source_str ? new DiagramSource( (string) $source_str ) : null,
				null !== $desc_str ? new DiagramDescription( (string) $desc_str ) : null,
				null !== $status_str ? new DiagramStatus( (string) $status_str ) : null,
				is_array( $category_ids ) ? $category_ids : null,
				is_array( $tag_ids ) ? $tag_ids : null,
				is_array( $render_config ) ? new RenderConfig( $render_config ) : null,
				$receipt,
				null !== $expected_version ? (string) $expected_version : null,
				get_current_user_id(),
				$writer_profile
			);

			$detail = $this->diagram_service->update_diagram( $command );

			return rest_ensure_response( $detail );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Update diagram categories.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function update_categories( $request ) {
		try {
			$id_val           = (int) $request->get_param( 'id' );
			$term_ids         = (array) ( $request->get_param( 'termIds' ) ?? $request->get_param( 'categoryIds' ) ?? array() );
			$expected_version = $request->get_param( 'expectedVersion' );

			$command = new UpdateDiagramCommand(
				new DiagramId( $id_val ),
				null,
				null,
				null,
				null,
				$term_ids,
				null,
				null,
				null,
				null !== $expected_version ? (string) $expected_version : null,
				get_current_user_id()
			);

			$detail = $this->diagram_service->update_diagram( $command );

			return rest_ensure_response( $detail );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Update diagram tags.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function update_tags( $request ) {
		try {
			$id_val           = (int) $request->get_param( 'id' );
			$term_ids         = (array) ( $request->get_param( 'termIds' ) ?? $request->get_param( 'tagIds' ) ?? array() );
			$expected_version = $request->get_param( 'expectedVersion' );

			$command = new UpdateDiagramCommand(
				new DiagramId( $id_val ),
				null,
				null,
				null,
				null,
				null,
				$term_ids,
				null,
				null,
				null !== $expected_version ? (string) $expected_version : null,
				get_current_user_id()
			);

			$detail = $this->diagram_service->update_diagram( $command );

			return rest_ensure_response( $detail );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Trash or force-delete diagram item.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function delete_item( $request ) {
		try {
			$id_val  = (int) $request->get_param( 'id' );
			$force   = (bool) $request->get_param( 'force' );
			$command = new TrashDiagramCommand( new DiagramId( $id_val ), $force );

			$this->diagram_service->trash_diagram( $command );

			return rest_ensure_response(
				array(
					'deleted' => true,
					'id'      => $id_val,
					'force'   => $force,
				)
			);
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Permission check for download endpoints.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function get_item_download_permissions_check( $request ): bool {
		$id = (int) $request->get_param( 'id' );
		return current_user_can( 'read_post', $id ) || current_user_can( 'edit_post', $id ) || 'publish' === get_post_status( $id );
	}

	/**
	 * Download raw diagram source (.mmd).
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_item_source_download( $request ) {
		try {
			$id_val  = (int) $request->get_param( 'id' );
			$diagram = $this->repository->find( new DiagramId( $id_val ) );
			if ( ! $diagram instanceof Diagram ) {
				throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id_val ) );
			}

			$user_id = get_current_user_id();
			if ( ! $this->download_service->can_download_source( $diagram, $user_id ) ) {
				return new WP_Error( 'mdm_download_forbidden', 'Source download is disabled or forbidden for this diagram.', array( 'status' => 403 ) );
			}

			$filename = DownloadPolicyService::format_filename( $diagram->title()->value(), $id_val, 'mmd' );
			$source   = $diagram->source()->value();

			$response = new WP_REST_Response( $source, 200 );
			$response->header( 'Content-Type', 'text/plain; charset=utf-8' );
			$response->header( 'Content-Disposition', sprintf( 'attachment; filename="%s"', $filename ) );
			$response->header( 'X-Content-Type-Options', 'nosniff' );

			return $response;
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Download diagram SVG (.svg).
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_item_svg_download( $request ) {
		try {
			$id_val  = (int) $request->get_param( 'id' );
			$diagram = $this->repository->find( new DiagramId( $id_val ) );
			if ( ! $diagram instanceof Diagram ) {
				throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id_val ) );
			}

			$user_id = get_current_user_id();
			if ( ! $this->download_service->can_download_svg( $diagram, $user_id ) ) {
				return new WP_Error( 'mdm_download_forbidden', 'SVG download is disabled or forbidden for this diagram.', array( 'status' => 403 ) );
			}

			$filename = DownloadPolicyService::format_filename( $diagram->title()->value(), $id_val, 'svg' );
			$svg      = sprintf( '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 600" role="img"><title>%s</title></svg>', esc_html( $diagram->title()->value() ) );

			$response = new WP_REST_Response( $svg, 200 );
			$response->header( 'Content-Type', 'image/svg+xml; charset=utf-8' );
			$response->header( 'Content-Disposition', sprintf( 'attachment; filename="%s"', $filename ) );
			$response->header( 'X-Content-Type-Options', 'nosniff' );

			return $response;
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}
}
