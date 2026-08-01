<?php
/**
 * Diagram Application Service.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Service
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Service;

use WP_Query;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\BulkAssignTermsCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\CreateDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\DuplicateDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\RestoreDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\TrashDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\Command\UpdateDiagramCommand;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\BulkResultDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\DiagramDetailDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\DiagramPreviewDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\DiagramSummaryDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\EditConflictException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\GetDiagramQuery;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\GetDiagramUsageQuery;
use WebFalcon\MermaidDiagrams\Diagram\Application\Query\SearchDiagramsQuery;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramRepository;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\DiagramNotFoundException;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main application service handling diagram use cases.
 */
class DiagramApplicationService {

	/**
	 * Receipt verifier instance.
	 *
	 * @var ValidationReceiptVerifier
	 */
	private ValidationReceiptVerifier $receipt_verifier;

	/**
	 * Constructor.
	 *
	 * @param DiagramRepository             $repository       Diagram repository instance.
	 * @param ValidationReceiptVerifier|null $receipt_verifier Validation receipt verifier instance.
	 */
	public function __construct(
		private DiagramRepository $repository,
		?ValidationReceiptVerifier $receipt_verifier = null
	) {
		$this->receipt_verifier = $receipt_verifier ?? new ValidationReceiptVerifier();
	}

	/**
	 * Create a new diagram with optional idempotency check.
	 *
	 * @param CreateDiagramCommand $command Create command.
	 * @return array<string, mixed> Diagram detail payload.
	 */
	public function create_diagram( CreateDiagramCommand $command ): array {
		$idem_key = $command->idempotency_key();
		if ( ! empty( $idem_key ) ) {
			$transient_name = 'mdm_idem_' . md5( get_current_user_id() . ':' . $idem_key );
			$existing_id    = get_transient( $transient_name );

			if ( false !== $existing_id && (int) $existing_id > 0 ) {
				$existing_diagram = $this->repository->find( new DiagramId( (int) $existing_id ) );
				if ( $existing_diagram instanceof Diagram ) {
					return DiagramDetailDTO::from_aggregate( $existing_diagram, get_current_user_id() );
				}
			}
		}

		$this->receipt_verifier->verify(
			$command->validation_receipt(),
			$command->source(),
			$command->writer_profile()
		);

		$diagram = new Diagram(
			$command->title(),
			$command->source(),
			null,
			$command->description(),
			$command->status(),
			null,
			$command->author_id() > 0 ? $command->author_id() : get_current_user_id(),
			get_current_user_id(),
			$command->category_ids(),
			$command->tag_ids(),
			$command->render_config(),
			null,
			$command->validation_receipt()
		);

		$saved = $this->repository->save( $diagram );

		if ( ! empty( $idem_key ) && null !== $saved->id() ) {
			$transient_name = 'mdm_idem_' . md5( get_current_user_id() . ':' . $idem_key );
			set_transient( $transient_name, $saved->id()->value(), DAY_IN_SECONDS );
		}

		return DiagramDetailDTO::from_aggregate( $saved, get_current_user_id() );
	}

	/**
	 * Update an existing diagram with optimistic version token check.
	 *
	 * @param UpdateDiagramCommand $command Update command.
	 * @return array<string, mixed> Diagram detail payload.
	 * @throws DiagramNotFoundException If diagram not found.
	 * @throws EditConflictException If expected version does not match active version.
	 */
	public function update_diagram( UpdateDiagramCommand $command ): array {
		$existing = $this->repository->find( $command->id() );
		if ( ! $existing instanceof Diagram ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $command->id()->value() ) );
		}

		if ( null !== $command->expected_version() && '' !== $command->expected_version() ) {
			$current_token = $existing->version()?->value() ?? '';
			if ( $command->expected_version() !== $current_token ) {
				throw new EditConflictException( $command->expected_version(), $existing );
			}
		}

		if ( null !== $command->title() ) {
			$existing->update_title( $command->title() );
		}
		if ( null !== $command->source() ) {
			if ( ! $existing->source()->equals( $command->source() ) ) {
				$this->receipt_verifier->verify(
					$command->validation_receipt(),
					$command->source(),
					$command->writer_profile()
				);
			}
			$existing->update_source( $command->source() );
		}
		if ( null !== $command->status() ) {
			$existing->update_status( $command->status() );
		}
		if ( null !== $command->category_ids() ) {
			$existing->set_categories( $command->category_ids() );
		}
		if ( null !== $command->tag_ids() ) {
			$existing->set_tags( $command->tag_ids() );
		}
		if ( null !== $command->validation_receipt() ) {
			$existing->set_validation_receipt( $command->validation_receipt() );
		}

		$saved = $this->repository->save( $existing );

		return DiagramDetailDTO::from_aggregate( $saved, get_current_user_id() );
	}

	/**
	 * Get single diagram detail by ID.
	 *
	 * @param GetDiagramQuery $query Query DTO.
	 * @return array<string, mixed> Diagram detail payload.
	 * @throws DiagramNotFoundException If diagram not found.
	 */
	public function get_diagram( GetDiagramQuery $query ): array {
		$diagram = $this->repository->find( $query->id() );
		if ( ! $diagram instanceof Diagram ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $query->id()->value() ) );
		}

		return DiagramDetailDTO::from_aggregate( $diagram, get_current_user_id() );
	}

	/**
	 * Get authorized preview render payload for client-side rendering.
	 *
	 * @param GetDiagramQuery $query Query DTO.
	 * @return array<string, mixed> Preview payload.
	 * @throws DiagramNotFoundException If diagram not found.
	 */
	public function get_diagram_preview( GetDiagramQuery $query ): array {
		$diagram = $this->repository->find( $query->id() );
		if ( ! $diagram instanceof Diagram ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $query->id()->value() ) );
		}

		return DiagramPreviewDTO::from_aggregate( $diagram, get_current_user_id() );
	}

	/**
	 * Search and list diagrams with filters, pagination, and sorting.
	 *
	 * @param SearchDiagramsQuery $query Search query DTO.
	 * @return array<string, mixed> Result payload containing items, pagination, facets.
	 */
	public function search_diagrams( SearchDiagramsQuery $query ): array {
		$args = array(
			'post_type'      => DiagramPostType::CPT_SLUG,
			'paged'          => $query->page(),
			'posts_per_page' => $query->per_page(),
			'orderby'        => 'modified' === $query->orderby() ? 'post_modified' : 'post_title',
			'order'          => $query->order(),
		);

		if ( null !== $query->search() && '' !== trim( $query->search() ) ) {
			$args['s'] = trim( $query->search() );
		}

		$statuses = $query->statuses();
		if ( ! empty( $statuses ) ) {
			$args['post_status'] = $statuses;
		} else {
			$args['post_status'] = array( 'publish', 'draft', 'pending', 'private' );
		}

		if ( ! empty( $query->authors() ) ) {
			$args['author__in'] = $query->authors();
		}

		$tax_query = array();

		if ( ! empty( $query->category_ids() ) ) {
			$tax_query[] = array(
				'taxonomy' => DiagramTaxonomies::TAXONOMY_CATEGORY,
				'field'    => 'term_id',
				'terms'    => $query->category_ids(),
			);
		}

		if ( ! empty( $query->tag_ids() ) ) {
			$tax_query[] = array(
				'taxonomy' => DiagramTaxonomies::TAXONOMY_TAG,
				'field'    => 'term_id',
				'terms'    => $query->tag_ids(),
			);
		}

		if ( ! empty( $tax_query ) ) {
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax_query );
		}

		if ( ! empty( $query->types() ) ) {
			$args['meta_query'] = array(
				array(
					'key'     => DiagramMeta::META_DIAGRAM_TYPE,
					'value'   => $query->types(),
					'compare' => 'IN',
				),
			);
		}

		$wp_query = new WP_Query( $args );
		$items    = array();

		if ( $wp_query->have_posts() ) {
			foreach ( $wp_query->posts as $post ) {
				$diagram = $this->repository->find( new DiagramId( (int) $post->ID ) );
				if ( $diagram instanceof Diagram ) {
					$items[] = 'detail' === $query->view()
						? DiagramDetailDTO::from_aggregate( $diagram, get_current_user_id() )
						: DiagramSummaryDTO::from_aggregate( $diagram, get_current_user_id() );
				}
			}
		}

		return array(
			'items'      => $items,
			'pagination' => array(
				'page'       => $query->page(),
				'perPage'    => $query->per_page(),
				'totalItems' => (int) $wp_query->found_posts,
				'totalPages' => (int) $wp_query->max_num_pages,
			),
			'facets'     => array(
				'types'    => $this->collect_type_facets(),
				'statuses' => array( 'publish', 'draft', 'pending', 'private', 'trash' ),
			),
		);
	}

	/**
	 * Collect distinct diagram type facet values from stored meta.
	 *
	 * @return array<string>
	 */
	private function collect_type_facets(): array {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$rows = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT pm.meta_value
				FROM {$wpdb->postmeta} pm
				INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
				WHERE pm.meta_key = %s
				AND p.post_type = %s
				AND pm.meta_value <> ''
				ORDER BY pm.meta_value ASC
				LIMIT 50",
				DiagramMeta::META_DIAGRAM_TYPE,
				DiagramPostType::CPT_SLUG
			)
		);

		if ( ! is_array( $rows ) ) {
			return array();
		}

		return array_values( array_filter( array_map( 'strval', $rows ) ) );
	}

	/**
	 * Duplicate a diagram.
	 *
	 * @param DuplicateDiagramCommand $command Command DTO.
	 * @return array<string, mixed> Diagram detail payload of copy.
	 */
	public function duplicate_diagram( DuplicateDiagramCommand $command ): array {
		$saved = $this->repository->duplicate( $command->id(), $command->new_title() );
		return DiagramDetailDTO::from_aggregate( $saved, get_current_user_id() );
	}

	/**
	 * Trash or force-delete a diagram.
	 *
	 * @param TrashDiagramCommand $command Command DTO.
	 * @return bool True on success.
	 */
	public function trash_diagram( TrashDiagramCommand $command ): bool {
		if ( $command->force() ) {
			return $this->repository->delete( $command->id(), true );
		}
		return $this->repository->trash( $command->id() );
	}

	/**
	 * Restore a trashed diagram.
	 *
	 * @param RestoreDiagramCommand $command Command DTO.
	 * @return bool True on success.
	 */
	public function restore_diagram( RestoreDiagramCommand $command ): bool {
		return $this->repository->restore( $command->id() );
	}

	/**
	 * Perform bulk operations over an array of diagram IDs.
	 *
	 * @param BulkAssignTermsCommand $command Command DTO.
	 * @return array{results: array, summary: array{requested: int, succeeded: int, failed: int}}
	 */
	public function bulk_assign_terms( BulkAssignTermsCommand $command ): array {
		$results = array();
		$op      = $command->operation();
		$payload = $command->payload();

		foreach ( $command->ids() as $id_val ) {
			$diagram_id = new DiagramId( $id_val );
			try {
				if ( ! current_user_can( 'edit_post', $id_val ) ) {
					$results[] = array(
						'id'    => $id_val,
						'ok'    => false,
						'error' => array(
							'code'    => 'mdm_forbidden',
							'message' => sprintf( 'You do not have permission to edit diagram ID %d.', $id_val ),
						),
					);
					continue;
				}

				$diagram = $this->repository->find( $diagram_id );
				if ( ! $diagram instanceof Diagram ) {
					$results[] = array(
						'id'    => $id_val,
						'ok'    => false,
						'error' => array(
							'code'    => 'mdm_diagram_not_found',
							'message' => sprintf( 'Diagram ID %d not found.', $id_val ),
						),
					);
					continue;
				}

				switch ( $op ) {
					case BulkAssignTermsCommand::OP_ADD_CATEGORIES:
						$added = array_values( array_unique( array_merge( $diagram->category_ids(), (array) ( $payload['category_ids'] ?? array() ) ) ) );
						$diagram->set_categories( $added );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_REMOVE_CATEGORIES:
						$to_remove = (array) ( $payload['category_ids'] ?? array() );
						$remaining = array_values( array_diff( $diagram->category_ids(), $to_remove ) );
						$diagram->set_categories( $remaining );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_REPLACE_CATEGORIES:
						$new_cats = (array) ( $payload['category_ids'] ?? array() );
						$diagram->set_categories( $new_cats );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_ADD_TAGS:
						$added_tags = array_values( array_unique( array_merge( $diagram->tag_ids(), (array) ( $payload['tag_ids'] ?? array() ) ) ) );
						$diagram->set_tags( $added_tags );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_REMOVE_TAGS:
						$to_remove = (array) ( $payload['tag_ids'] ?? array() );
						$remaining = array_values( array_diff( $diagram->tag_ids(), $to_remove ) );
						$diagram->set_tags( $remaining );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_SET_STATUS:
						$status_str = (string) ( $payload['status'] ?? 'draft' );
						$diagram->update_status( new DiagramStatus( $status_str ) );
						$this->repository->save( $diagram );
						break;

					case BulkAssignTermsCommand::OP_TRASH:
						$this->repository->trash( $diagram_id );
						break;

					case BulkAssignTermsCommand::OP_RESTORE:
						$this->repository->restore( $diagram_id );
						break;
				}

				$results[] = array(
					'id' => $id_val,
					'ok' => true,
				);
			} catch ( \Throwable $ex ) {
				$results[] = array(
					'id'    => $id_val,
					'ok'    => false,
					'error' => array(
						'code'    => 'mdm_bulk_failed',
						'message' => $ex->getMessage(),
					),
				);
			}
		}

		return BulkResultDTO::format( $results );
	}

	/**
	 * Get usage information for a diagram.
	 *
	 * @param GetDiagramUsageQuery $query Query DTO.
	 * @return array<string, mixed> Usage details.
	 */
	public function get_diagram_usage( GetDiagramUsageQuery $query ): array {
		$diagram_id = $query->id()->value();
		$usage_count = (int) get_post_meta( $diagram_id, '_mdm_usage_count', true );

		return array(
			'diagramId'  => $diagram_id,
			'usageCount' => $usage_count,
			'references' => array(),
		);
	}
}
