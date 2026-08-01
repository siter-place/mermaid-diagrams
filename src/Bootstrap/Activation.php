<?php
/**
 * Plugin activation handler.
 *
 * @package WebFalcon\MermaidDiagrams\Bootstrap
 */

namespace WebFalcon\MermaidDiagrams\Bootstrap;

use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

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

		DiagramPostType::register();
		DiagramTaxonomies::register();
		DiagramMeta::register();
		DiagramCapabilities::assign_default_capabilities();

		flush_rewrite_rules();
	}
}
