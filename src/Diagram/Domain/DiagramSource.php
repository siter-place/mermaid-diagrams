<?php
/**
 * Diagram Source Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramSourceException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encapsulates canonical Mermaid source code.
 */
readonly class DiagramSource {

	/**
	 * Maximum allowed source size in bytes (500 KB).
	 */
	public const MAX_BYTES = 500000;

	/**
	 * Normalized Mermaid source string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $source Raw Mermaid source code.
	 * @throws InvalidDiagramSourceException If source contains null bytes or exceeds max size.
	 */
	public function __construct( string $source ) {
		if ( str_contains( $source, "\0" ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidDiagramSourceException( 'Mermaid source code contains forbidden null bytes.' );
		}

		// Normalize line endings to LF (\n).
		$normalized = str_replace( array( "\r\n", "\r" ), "\n", $source );

		if ( strlen( $normalized ) > self::MAX_BYTES ) {
			$message = sprintf( 'Mermaid source exceeds maximum allowed size of %d bytes.', self::MAX_BYTES );
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidDiagramSourceException( $message );
		}

		$this->value = $normalized;
	}

	/**
	 * Create from raw string.
	 *
	 * @param string $source Raw string.
	 * @return self
	 */
	public static function from_string( string $source ): self {
		return new self( $source );
	}

	/**
	 * Get normalized source value.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Compute SHA-256 source hash.
	 *
	 * @return SourceHash
	 */
	public function hash(): SourceHash {
		return SourceHash::from_source( $this->value );
	}

	/**
	 * Attempt lightweight type detection from initial keywords.
	 *
	 * @return DiagramType
	 */
	public function detect_type(): DiagramType {
		return DiagramType::detect_from_source( $this->value );
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other source object.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
