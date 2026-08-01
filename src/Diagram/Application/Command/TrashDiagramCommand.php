<?php
/**
 * Trash / Delete Diagram Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for trashing or force-deleting a diagram.
 */
class TrashDiagramCommand {

	/**
	 * Constructor.
	 *
	 * @param DiagramId $id    Diagram ID.
	 * @param bool      $force Force permanent deletion.
	 */
	public function __construct(
		private DiagramId $id,
		private bool $force = false
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

	/**
	 * Force permanent deletion flag.
	 *
	 * @return bool
	 */
	public function force(): bool {
		return $this->force;
	}
}
