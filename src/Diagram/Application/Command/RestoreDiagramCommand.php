<?php
/**
 * Restore Diagram Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for restoring a trashed diagram.
 */
class RestoreDiagramCommand {

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
