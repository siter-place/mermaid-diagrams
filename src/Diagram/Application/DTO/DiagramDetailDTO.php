<?php
/**
 * Diagram Detail DTO for single diagram editor REST responses.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\DTO
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\DTO;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data Transfer Object for full diagram details including canonical source and version token.
 */
class DiagramDetailDTO {

	/**
	 * Convert a Diagram aggregate to full detail array representation.
	 *
	 * @param Diagram $diagram Diagram aggregate instance.
	 * @param int     $current_user_id Current user ID.
	 * @return array<string, mixed>
	 */
	public static function from_aggregate( Diagram $diagram, int $current_user_id = 0 ): array {
		$summary = DiagramSummaryDTO::from_aggregate( $diagram, $current_user_id );

		$created_gmt = $diagram->created_at()?->format( 'Y-m-d\TH:i:s\Z' ) ?? '';

		$last_editor_data = null;
		if ( null !== $diagram->last_editor_id() && $diagram->last_editor_id() > 0 ) {
			$last_editor_data = array(
				'id'   => $diagram->last_editor_id(),
				'name' => get_the_author_meta( 'display_name', $diagram->last_editor_id() ) ?: 'Unknown',
			);
		}

		return array_merge(
			$summary,
			array(
				'source'            => $diagram->source()->value(),
				'renderConfig'      => $diagram->render_config()->to_array(),
				'versionToken'      => $diagram->version()?->value() ?? '',
				'validationReceipt' => $diagram->validation_receipt()?->to_array(),
				'createdAtGmt'      => $created_gmt,
				'lastEditor'        => $last_editor_data,
			)
		);
	}
}
