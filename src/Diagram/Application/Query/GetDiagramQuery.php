<?php
/**
 * Get Diagram Query DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Query
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Query;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query for retrieving a single diagram detail.
 */
class GetDiagramQuery {

	/**
	 * Constructor.
	 *
	 * @param DiagramId $id Diagram ID.
	 */
	public function __construct(
		private DiagramId $id
	) {
	}

	/**
	 * Get Diagram ID.
	 *
	 * @return DiagramId
	 */
	public function id(): DiagramId {
		return $this->id;
	}
}
