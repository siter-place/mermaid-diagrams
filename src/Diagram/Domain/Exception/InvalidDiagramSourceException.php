<?php
/**
 * Invalid Diagram Source Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain\Exception;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when diagram source violates structural or security constraints.
 */
class InvalidDiagramSourceException extends InvalidArgumentException {}
