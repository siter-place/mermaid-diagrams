<?php
/**
 * Duplicate Diagram Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for duplicating a diagram.
 */
class DuplicateDiagramCommand {

	/**
	 * Constructor.
	 *
	 * @param DiagramId   $id        Source diagram ID.
	 * @param string|null $new_title Optional custom title.
	 * @param bool        $keep_terms Whether to copy category and tag terms.
	 */
	public function __construct(
		private DiagramId $id,
		private ?string $new_title = null,
		private bool $keep_terms = true
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
	 * Get New Title.
	 *
	 * @return string|null
	 */
	public function new_title(): ?string {
		return $this->new_title;
	}

	/**
	 * Check if terms should be kept.
	 *
	 * @return bool
	 */
	public function keep_terms(): bool {
		return $this->keep_terms;
	}
}
