<?php
/**
 * Diagram Not Found Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain\Exception
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain\Exception;

use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when requested diagram record does not exist.
 */
class DiagramNotFoundException extends RuntimeException {}
