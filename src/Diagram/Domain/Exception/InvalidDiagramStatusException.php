<?php
/**
 * Invalid Diagram Status Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain\Exception;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when diagram status is not allowed.
 */
class InvalidDiagramStatusException extends InvalidArgumentException {}
