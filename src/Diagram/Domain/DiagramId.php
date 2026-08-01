<?php
/**
 * Diagram Identity Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents a Diagram ID.
 */
readonly class DiagramId {

	/**
	 * Constructor.
	 *
	 * @param int $value Positive integer ID.
	 * @throws InvalidArgumentException If ID is not positive.
	 */
	public function __construct( private int $value ) {
		if ( $this->value <= 0 ) {
			throw new InvalidArgumentException( 'Diagram ID must be a positive integer.' );
		}
	}

	/**
	 * Create DiagramId from integer.
	 *
	 * @param int $value Integer ID.
	 * @return self
	 */
	public static function from_int( int $value ): self {
		return new self( $value );
	}

	/**
	 * Get raw integer value.
	 *
	 * @return int
	 */
	public function value(): int {
		return $this->value;
	}

	/**
	 * Compare equality with another DiagramId.
	 *
	 * @param self $other Diagram ID to compare.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
