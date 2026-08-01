<?php
/**
 * Invalid Validation Receipt Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Exception;

use DomainException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Exception thrown when a validation receipt fails verification.
 */
class InvalidValidationReceiptException extends DomainException {

	/**
	 * Constructor.
	 *
	 * @param string $message Exception message.
	 */
	public function __construct( string $message = 'The provided Mermaid validation receipt is invalid or mismatched.' ) {
		parent::__construct( $message );
	}
}
