<?php
/**
 * Plugin activation handler.
 *
 * @package WebFalcon\MermaidDiagrams\Bootstrap
 */

namespace WebFalcon\MermaidDiagrams\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles plugin activation routines.
 */
class Activation {

	/**
	 * Run activation logic.
	 */
	public static function activate(): void {
		if ( ! Compatibility::check() ) {
			wp_die(
				esc_html__( 'Mermaid Diagrams cannot be activated because environment requirements are not met.', 'mermaid-diagrams' ),
				esc_html__( 'Plugin Activation Error', 'mermaid-diagrams' ),
				array( 'back_link' => true )
			);
		}

		// Flush rewrite rules or setup defaults when needed in later phases.
	}
}
