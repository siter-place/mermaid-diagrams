<?php
/**
 * Missing Validation Receipt Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Exception;

use DomainException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exception thrown when a required validation receipt is missing.
 */
class MissingValidationReceiptException extends DomainException {

	/**
	 * Constructor.
	 *
	 * @param string $message Exception message.
	 */
	public function __construct( string $message = 'A valid Mermaid validation receipt is required for diagram persistence.' ) {
		parent::__construct( $message );
	}
}
