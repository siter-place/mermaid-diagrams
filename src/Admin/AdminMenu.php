<?php
/**
 * Top-Level Admin Menu Page Registration.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers top-level Diagrams admin menu page.
 */
class AdminMenu {

	public const MENU_SLUG = 'mdm-diagrams';

	/**
	 * Register top-level admin menu page.
	 *
	 * @return void
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'Mermaid Diagrams', 'mermaid-diagrams' ),
			__( 'Diagrams', 'mermaid-diagrams' ),
			DiagramCapabilities::CAP_EDIT_DIAGRAMS,
			self::MENU_SLUG,
			array( self::class, 'render_page' ),
			'dashicons-chart-flow',
			30
		);
	}

	/**
	 * Render placeholder template.
	 *
	 * @return void
	 */
	public static function render_page(): void {
		if ( ! current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'mermaid-diagrams' ) );
		}

		$plugin_dir    = defined( 'MDM_PLUGIN_DIR' ) ? MDM_PLUGIN_DIR : dirname( __DIR__, 2 ) . '/';
		$template_path = $plugin_dir . 'templates/admin-app-root.php';

		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}
}
