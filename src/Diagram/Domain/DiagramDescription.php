<?php
/**
 * Diagram Description Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents an optional diagram description excerpt.
 */
readonly class DiagramDescription {

	/**
	 * Description string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $description Raw description string.
	 */
	public function __construct( string $description = '' ) {
		$this->value = trim( $description );
	}

	/**
	 * Create from string.
	 *
	 * @param string $description Raw description.
	 * @return self
	 */
	public static function from_string( string $description ): self {
		return new self( $description );
	}

	/**
	 * Get description string.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Check if description is empty.
	 *
	 * @return bool
	 */
	public function is_empty(): bool {
		return '' === $this->value;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other description.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
