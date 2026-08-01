<?php
/**
 * Invalid Bulk Operation Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Exception;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when an unsupported or malformed bulk operation is requested.
 */
class InvalidBulkOperationException extends InvalidArgumentException {
}
