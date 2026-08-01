<?php
/**
 * WordPress CPT Diagram Repository Implementation.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Infrastructure;

use DateTimeImmutable;
use DateTimeZone;
use WP_Post;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramRepository;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramType;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramVersion;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\DiagramNotFoundException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceHash;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WordPress database repository adapter for mdm_diagram CPT.
 */
class WordPressDiagramRepository implements DiagramRepository {

	/**
	 * Find a diagram by ID.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return Diagram|null Returns null if not found or wrong post type.
	 */
	public function find( DiagramId $id ): ?Diagram {
		$post = get_post( $id->value() );

		if ( ! $post instanceof WP_Post || DiagramPostType::CPT_SLUG !== $post->post_type ) {
			return null;
		}

		$title       = new DiagramTitle( '' !== $post->post_title ? $post->post_title : 'Untitled Diagram' );
		$source      = new DiagramSource( $post->post_content );
		$description = new DiagramDescription( $post->post_excerpt );
		$status      = new DiagramStatus( $post->post_status );

		$type_str = (string) get_post_meta( $post->ID, DiagramMeta::META_DIAGRAM_TYPE, true );
		$type     = '' !== $type_str ? DiagramType::from_string( $type_str ) : $source->detect_type();

		$raw_config    = get_post_meta( $post->ID, DiagramMeta::META_RENDER_CONFIG, true );
		$render_config = new RenderConfig( is_array( $raw_config ) ? $raw_config : array() );

		$last_editor_id = (int) get_post_meta( $post->ID, DiagramMeta::META_LAST_EDITOR_ID, true );

		$category_ids = wp_get_object_terms( $post->ID, DiagramTaxonomies::TAXONOMY_CATEGORY, array( 'fields' => 'ids' ) );
		$tag_ids      = wp_get_object_terms( $post->ID, DiagramTaxonomies::TAXONOMY_TAG, array( 'fields' => 'ids' ) );

		$receipt_data = get_post_meta( $post->ID, DiagramMeta::META_VALIDATION_SUMMARY, true );
		$receipt      = is_array( $receipt_data ) && ! empty( $receipt_data['sourceHash'] ) ? ValidationReceipt::from_array( $receipt_data ) : null;

		$revisions   = wp_get_post_revisions( $post->ID, array( 'numberposts' => 1 ) );
		$revision_id = ! empty( $revisions ) ? (int) current( $revisions )->ID : $post->ID;

		$version = DiagramVersion::generate(
			$post->ID,
			$post->post_modified_gmt,
			$source->hash(),
			$revision_id
		);

		$utc_tz      = new DateTimeZone( 'UTC' );
		$created_at  = '' !== $post->post_date_gmt ? new DateTimeImmutable( $post->post_date_gmt, $utc_tz ) : null;
		$modified_at = '' !== $post->post_modified_gmt ? new DateTimeImmutable( $post->post_modified_gmt, $utc_tz ) : null;

		return new Diagram(
			$title,
			$source,
			$id,
			$description,
			$status,
			$type,
			(int) $post->post_author,
			$last_editor_id > 0 ? $last_editor_id : null,
			is_array( $category_ids ) ? $category_ids : array(),
			is_array( $tag_ids ) ? $tag_ids : array(),
			$render_config,
			$version,
			$receipt,
			$created_at,
			$modified_at
		);
	}

	/**
	 * Save a diagram (create or update).
	 *
	 * @param Diagram $diagram Diagram aggregate instance.
	 * @return Diagram Saved aggregate instance.
	 */
	public function save( Diagram $diagram ): Diagram {
		$post_arr = array(
			'post_type'    => DiagramPostType::CPT_SLUG,
			'post_title'   => $diagram->title()->value(),
			'post_excerpt' => $diagram->description()->value(),
			'post_content' => $diagram->source()->value(),
			'post_status'  => $diagram->status()->value(),
			'post_author'  => $diagram->author_id() > 0 ? $diagram->author_id() : get_current_user_id(),
		);

		// Prevent KSES from converting Mermaid syntax (e.g. > or <) in post_content.
		$kses_priority = has_filter( 'content_save_pre', 'wp_filter_post_kses' );
		if ( false !== $kses_priority ) {
			remove_filter( 'content_save_pre', 'wp_filter_post_kses', $kses_priority );
		}

		if ( null !== $diagram->id() ) {
			$post_arr['ID'] = $diagram->id()->value();
			$post_id        = wp_update_post( $post_arr, true );
		} else {
			$post_id = wp_insert_post( $post_arr, true );
		}

		if ( false !== $kses_priority ) {
			add_filter( 'content_save_pre', 'wp_filter_post_kses', $kses_priority );
		}

		if ( is_wp_error( $post_id ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new \RuntimeException( 'Failed to save diagram record: ' . $post_id->get_error_message() );
		}

		$post_id_int = (int) $post_id;

		wp_set_object_terms( $post_id_int, $diagram->category_ids(), DiagramTaxonomies::TAXONOMY_CATEGORY );
		wp_set_object_terms( $post_id_int, $diagram->tag_ids(), DiagramTaxonomies::TAXONOMY_TAG );

		update_post_meta( $post_id_int, DiagramMeta::META_DIAGRAM_TYPE, $diagram->type()->value() );
		update_post_meta( $post_id_int, DiagramMeta::META_RENDER_CONFIG, $diagram->render_config()->to_array() );
		update_post_meta( $post_id_int, DiagramMeta::META_SOURCE_HASH, $diagram->source_hash()->value() );
		update_post_meta( $post_id_int, DiagramMeta::META_LAST_EDITOR_ID, $diagram->last_editor_id() ?? get_current_user_id() );

		if ( null !== $diagram->validation_receipt() ) {
			update_post_meta( $post_id_int, DiagramMeta::META_VALIDATION_STATE, 'valid' );
			update_post_meta( $post_id_int, DiagramMeta::META_RENDERER_VERSION, $diagram->validation_receipt()->mermaid_version() );
			update_post_meta( $post_id_int, DiagramMeta::META_VALIDATION_SUMMARY, $diagram->validation_receipt()->to_array() );
		}

		$saved = $this->find( new DiagramId( $post_id_int ) );
		if ( ! $saved instanceof Diagram ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new \RuntimeException( 'Saved diagram record could not be re-fetched.' );
		}

		return $saved;
	}

	/**
	 * Permanently delete or trash a diagram.
	 *
	 * @param DiagramId $id    Diagram ID.
	 * @param bool      $force Whether to bypass trash.
	 * @return bool
	 * @throws DiagramNotFoundException If diagram not found.
	 */
	public function delete( DiagramId $id, bool $force = false ): bool {
		$post = get_post( $id->value() );
		if ( ! $post instanceof WP_Post || DiagramPostType::CPT_SLUG !== $post->post_type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id->value() ) );
		}

		$result = wp_delete_post( $id->value(), $force );
		return false !== $result && null !== $result;
	}

	/**
	 * Move a diagram to trash.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return bool
	 * @throws DiagramNotFoundException If diagram not found.
	 */
	public function trash( DiagramId $id ): bool {
		$post = get_post( $id->value() );
		if ( ! $post instanceof WP_Post || DiagramPostType::CPT_SLUG !== $post->post_type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id->value() ) );
		}

		$result = wp_trash_post( $id->value() );
		return false !== $result && null !== $result;
	}

	/**
	 * Restore a diagram from trash.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return bool
	 * @throws DiagramNotFoundException If diagram not found.
	 */
	public function restore( DiagramId $id ): bool {
		$post = get_post( $id->value() );
		if ( ! $post instanceof WP_Post || DiagramPostType::CPT_SLUG !== $post->post_type ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id->value() ) );
		}

		$result = wp_untrash_post( $id->value() );
		return false !== $result && null !== $result;
	}

	/**
	 * Duplicate a diagram into a new draft copy.
	 *
	 * @param DiagramId   $id        Original diagram ID.
	 * @param string|null $new_title Custom title for duplicate.
	 * @return Diagram Newly created draft.
	 * @throws DiagramNotFoundException If original diagram not found.
	 */
	public function duplicate( DiagramId $id, ?string $new_title = null ): Diagram {
		$original = $this->find( $id );
		if ( ! $original instanceof Diagram ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new DiagramNotFoundException( sprintf( 'Diagram with ID %d not found.', $id->value() ) );
		}

		$title_text = $new_title ?? sprintf( '%s (Copy)', $original->title()->value() );
		$copy_title = new DiagramTitle( $title_text );

		$duplicate = new Diagram(
			$copy_title,
			$original->source(),
			null,
			$original->description(),
			DiagramStatus::draft(),
			$original->type(),
			get_current_user_id(),
			get_current_user_id(),
			$original->category_ids(),
			$original->tag_ids(),
			$original->render_config(),
			null,
			$original->validation_receipt()
		);

		return $this->save( $duplicate );
	}
}
