<?php
/**
 * Diagram Status Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramStatusException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents a diagram post status.
 */
readonly class DiagramStatus {

	public const DRAFT   = 'draft';
	public const PENDING = 'pending';
	public const PUBLISH = 'publish';
	public const PRIVATE = 'private';
	public const TRASH   = 'trash';

	public const ALLOWED_STATUSES = array(
		self::DRAFT,
		self::PENDING,
		self::PUBLISH,
		self::PRIVATE,
		self::TRASH,
	);

	/**
	 * Status string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $status Post status string.
	 * @throws InvalidDiagramStatusException If status is not allowed.
	 */
	public function __construct( string $status ) {
		$lowered = strtolower( trim( $status ) );

		if ( ! in_array( $lowered, self::ALLOWED_STATUSES, true ) ) {
			$message = sprintf(
				'Invalid diagram status "%s". Allowed statuses: %s.',
				$status,
				implode( ', ', self::ALLOWED_STATUSES )
			);
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidDiagramStatusException( $message );
		}

		$this->value = $lowered;
	}

	/**
	 * Create status from string.
	 *
	 * @param string $status Status string.
	 * @return self
	 */
	public static function from_string( string $status ): self {
		return new self( $status );
	}

	/**
	 * Create default draft status.
	 *
	 * @return self
	 */
	public static function draft(): self {
		return new self( self::DRAFT );
	}

	/**
	 * Get status string value.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Check if draft.
	 *
	 * @return bool
	 */
	public function is_draft(): bool {
		return self::DRAFT === $this->value;
	}

	/**
	 * Check if published.
	 *
	 * @return bool
	 */
	public function is_published(): bool {
		return self::PUBLISH === $this->value;
	}

	/**
	 * Check if trashed.
	 *
	 * @return bool
	 */
	public function is_trashed(): bool {
		return self::TRASH === $this->value;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other status.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
