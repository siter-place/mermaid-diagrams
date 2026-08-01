<?php
/**
 * Plugin deactivation handler.
 *
 * @package WebFalcon\MermaidDiagrams\Bootstrap
 */

namespace WebFalcon\MermaidDiagrams\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin deactivation routines.
 */
class Deactivation {

	/**
	 * Run deactivation logic.
	 */
	public static function deactivate(): void {
		// Deactivation routines for later phases.
	}
}
