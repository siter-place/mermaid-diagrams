<?php
/**
 * Edit Conflict Exception for Optimistic Concurrency Control.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Exception;

use DomainException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when update expectedVersion token does not match active server version token.
 */
class EditConflictException extends DomainException {

	/**
	 * Constructor.
	 *
	 * @param string       $expected_version Token sent by client.
	 * @param Diagram      $current_diagram Active server diagram state.
	 * @param string       $message Error message.
	 * @param int          $code Error code.
	 * @param \Throwable|null $previous Previous exception.
	 */
	public function __construct(
		private string $expected_version,
		private Diagram $current_diagram,
		string $message = 'The diagram was modified by another user or session.',
		int $code = 409,
		?\Throwable $previous = null
	) {
		parent::__construct( $message, $code, $previous );
	}

	/**
	 * Get expected version token.
	 *
	 * @return string
	 */
	public function expected_version(): string {
		return $this->expected_version;
	}

	/**
	 * Get active server diagram.
	 *
	 * @return Diagram
	 */
	public function current_diagram(): Diagram {
		return $this->current_diagram;
	}
}
