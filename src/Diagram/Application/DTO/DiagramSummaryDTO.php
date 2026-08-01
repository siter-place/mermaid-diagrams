<?php
/**
 * Diagram Summary DTO for list/search REST projection.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\DTO
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\DTO;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data Transfer Object for diagram list summaries.
 */
class DiagramSummaryDTO {

	/**
	 * Convert a Diagram aggregate to summary array representation.
	 *
	 * @param Diagram $diagram Diagram aggregate instance.
	 * @param int     $current_user_id Current user ID for capability checks.
	 * @return array<string, mixed>
	 */
	public static function from_aggregate( Diagram $diagram, int $current_user_id = 0 ): array {
		$id_val = $diagram->id()?->value() ?? 0;

		$categories = array();
		foreach ( $diagram->category_ids() as $term_id ) {
			$term = get_term( $term_id, DiagramTaxonomies::TAXONOMY_CATEGORY );
			if ( $term && ! is_wp_error( $term ) ) {
				$categories[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}
		}

		$tags = array();
		foreach ( $diagram->tag_ids() as $term_id ) {
			$term = get_term( $term_id, DiagramTaxonomies::TAXONOMY_TAG );
			if ( $term && ! is_wp_error( $term ) ) {
				$tags[] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);
			}
		}

		$author_data = array(
			'id'   => $diagram->author_id(),
			'name' => get_the_author_meta( 'display_name', $diagram->author_id() ) ?: 'Unknown',
		);

		$modified_gmt = $diagram->modified_at()?->format( 'Y-m-d\TH:i:s\Z' ) ?? '';

		$can_edit    = current_user_can( 'edit_post', $id_val );
		$can_delete  = current_user_can( 'delete_post', $id_val );
		$can_publish = current_user_can( 'publish_posts' );

		$usage_count = (int) get_post_meta( $id_val, '_mdm_usage_count', true );

		return array(
			'id'           => $id_val,
			'title'        => $diagram->title()->value(),
			'description'  => $diagram->description()->value(),
			'type'         => $diagram->type()->value(),
			'status'       => $diagram->status()->value(),
			'categories'   => $categories,
			'tags'         => $tags,
			'author'       => $author_data,
			'modifiedGmt'  => $modified_gmt,
			'sourceHash'   => $diagram->source_hash()->value(),
			'can'          => array(
				'edit'    => $can_edit,
				'delete'  => $can_delete,
				'publish' => $can_publish,
			),
			'preview'      => array(
				'state' => 'available',
				'url'   => rest_url( sprintf( 'mdm/v1/diagrams/%d/preview', $id_val ) ),
			),
			'usageCount'   => $usage_count,
		);
	}
}
