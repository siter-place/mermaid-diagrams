<?php
/**
 * Bulk Assign Terms & Status Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\InvalidBulkOperationException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for performing bulk operations on diagram IDs.
 */
class BulkAssignTermsCommand {

	public const OP_ADD_CATEGORIES     = 'add_categories';
	public const OP_REMOVE_CATEGORIES  = 'remove_categories';
	public const OP_REPLACE_CATEGORIES = 'replace_categories';
	public const OP_ADD_TAGS           = 'add_tags';
	public const OP_REMOVE_TAGS        = 'remove_tags';
	public const OP_SET_STATUS         = 'set_status';
	public const OP_TRASH              = 'trash';
	public const OP_RESTORE            = 'restore';

	/**
	 * Allowed bulk operations.
	 *
	 * @var array<string>
	 */
	public const ALLOWED_OPERATIONS = array(
		self::OP_ADD_CATEGORIES,
		self::OP_REMOVE_CATEGORIES,
		self::OP_REPLACE_CATEGORIES,
		self::OP_ADD_TAGS,
		self::OP_REMOVE_TAGS,
		self::OP_SET_STATUS,
		self::OP_TRASH,
		self::OP_RESTORE,
	);

	/**
	 * Diagram IDs array.
	 *
	 * @var array<int>
	 */
	private array $ids;

	/**
	 * Payload parameters.
	 *
	 * @var array<string, mixed>
	 */
	private array $payload;

	/**
	 * Constructor.
	 *
	 * @param array<int>           $ids       Target diagram IDs.
	 * @param string               $operation Operation name.
	 * @param array<string, mixed> $payload   Operation parameters.
	 * @throws InvalidBulkOperationException If operation is invalid.
	 */
	public function __construct(
		array $ids,
		private string $operation,
		array $payload = array()
	) {
		if ( ! in_array( $operation, self::ALLOWED_OPERATIONS, true ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidBulkOperationException( sprintf( 'Invalid bulk operation "%s". Allowed: %s', $operation, implode( ', ', self::ALLOWED_OPERATIONS ) ) );
		}

		$this->ids     = array_values( array_unique( array_filter( array_map( 'intval', $ids ) ) ) );
		$this->payload = $payload;
	}

	/**
	 * Get Target Diagram IDs.
	 *
	 * @return array<int>
	 */
	public function ids(): array {
		return $this->ids;
	}

	/**
	 * Get Operation.
	 *
	 * @return string
	 */
	public function operation(): string {
		return $this->operation;
	}

	/**
	 * Get Payload.
	 *
	 * @return array<string, mixed>
	 */
	public function payload(): array {
		return $this->payload;
	}
}
