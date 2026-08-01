<?php
/**
 * Diagram Preview DTO for on-demand library preview rendering.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\DTO
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\DTO;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Authorized render payload for client-side preview; not a permanent public URL.
 */
class DiagramPreviewDTO {

	/**
	 * Convert a Diagram aggregate to preview render payload.
	 *
	 * @param Diagram $diagram Diagram aggregate instance.
	 * @param int     $current_user_id Current user ID for capability checks.
	 * @return array<string, mixed>
	 */
	public static function from_aggregate( Diagram $diagram, int $current_user_id = 0 ): array {
		$id_val       = $diagram->id()?->value() ?? 0;
		$can_edit     = current_user_can( 'edit_post', $id_val );
		$can_delete   = current_user_can( 'delete_post', $id_val );
		$can_publish  = current_user_can( 'publish_posts' );
		$can_read     = current_user_can( 'read_post', $id_val ) || $can_edit;

		$validation_state = (string) get_post_meta( $id_val, DiagramMeta::META_VALIDATION_STATE, true );
		if ( '' === $validation_state ) {
			$validation_state = 'unknown';
		}

		$receipt = $diagram->validation_receipt()?->to_array();

		$thumbnail_id = (int) get_post_thumbnail_id( $id_val );

		$payload = array(
			'id'           => $id_val,
			'title'        => $diagram->title()->value(),
			'description'  => $diagram->description()->value(),
			'type'         => $diagram->type()->value(),
			'status'       => $diagram->status()->value(),
			'renderConfig' => $diagram->render_config()->to_array(),
			'validation'   => array(
				'state'   => $validation_state,
				'receipt' => $receipt,
			),
			'thumbnail'    => array(
				'state'        => $thumbnail_id > 0 ? 'available' : 'missing',
				'attachmentId' => $thumbnail_id > 0 ? $thumbnail_id : null,
			),
			'can'          => array(
				'edit'    => $can_edit,
				'delete'  => $can_delete,
				'publish' => $can_publish,
			),
		);

		if ( $can_read ) {
			$payload['source'] = $diagram->source()->value();
		}

		return $payload;
	}
}
