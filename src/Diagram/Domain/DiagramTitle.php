<?php
/**
 * Diagram Title Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramTitleException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents a library diagram title.
 */
readonly class DiagramTitle {

	/**
	 * Maximum title length in characters.
	 */
	public const MAX_LENGTH = 255;

	/**
	 * Title string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $title Raw title string.
	 * @throws InvalidDiagramTitleException If title is empty or exceeds max length.
	 */
	public function __construct( string $title ) {
		$trimmed = trim( $title );

		if ( '' === $trimmed ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidDiagramTitleException( 'Diagram title cannot be empty.' );
		}

		if ( mb_strlen( $trimmed ) > self::MAX_LENGTH ) {
			$message = sprintf( 'Diagram title exceeds maximum length of %d characters.', self::MAX_LENGTH );
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidDiagramTitleException( $message );
		}

		$this->value = $trimmed;
	}

	/**
	 * Create title from string.
	 *
	 * @param string $title Raw title.
	 * @return self
	 */
	public static function from_string( string $title ): self {
		return new self( $title );
	}

	/**
	 * Get title string value.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other title.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
