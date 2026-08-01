<?php
/**
 * Invalid Diagram Title Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain\Exception;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when diagram title violates library domain constraints.
 */
class InvalidDiagramTitleException extends InvalidArgumentException {}
