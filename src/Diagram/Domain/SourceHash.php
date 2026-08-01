<?php
/**
 * Source Hash Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents a SHA-256 hash of normalized Mermaid source code.
 */
readonly class SourceHash {

	/**
	 * Formatted hash string (sha256:<hex>).
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $hash Formatted hash string.
	 * @throws InvalidArgumentException If hash format is invalid.
	 */
	public function __construct( string $hash ) {
		if ( ! preg_match( '/^sha256:[a-f0-9]{64}$/i', $hash ) ) {
			throw new InvalidArgumentException( 'Invalid SourceHash format. Expected "sha256:<64-hex>".' );
		}
		$this->value = strtolower( $hash );
	}

	/**
	 * Create hash from source string.
	 *
	 * @param string $source Source string.
	 * @return self
	 */
	public static function from_source( string $source ): self {
		return new self( 'sha256:' . hash( 'sha256', $source ) );
	}

	/**
	 * Create from existing formatted string.
	 *
	 * @param string $hash Formatted hash string.
	 * @return self
	 */
	public static function from_string( string $hash ): self {
		return new self( $hash );
	}

	/**
	 * Get formatted hash string.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other hash.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
