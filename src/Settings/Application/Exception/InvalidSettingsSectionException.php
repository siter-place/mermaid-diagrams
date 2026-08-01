<?php
/**
 * Invalid Settings Section Exception.
 *
 * @package WebFalcon\MermaidDiagrams\Settings\Application\Exception
 */

namespace WebFalcon\MermaidDiagrams\Settings\Application\Exception;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Thrown when an invalid or unknown settings section is requested for update.
 */
class InvalidSettingsSectionException extends InvalidArgumentException {
}
