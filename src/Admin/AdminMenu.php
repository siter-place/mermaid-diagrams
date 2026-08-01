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
 * Registers top-level Diagrams admin menu page and settings submenu.
 */
class AdminMenu {

	/**
	 * Register admin menu pages.
	 *
	 * @return void
	 */
	public static function register_menu(): void {
		add_menu_page(
			__( 'Mermaid Diagrams', 'mermaid-diagrams' ),
			__( 'Diagrams', 'mermaid-diagrams' ),
			DiagramCapabilities::CAP_EDIT_DIAGRAMS,
			AdminRoute::LIBRARY_SLUG,
			array( self::class, 'render_library_page' ),
			'dashicons-chart-flow',
			30
		);

		add_submenu_page(
			AdminRoute::LIBRARY_SLUG,
			__( 'Mermaid Diagrams Settings', 'mermaid-diagrams' ),
			__( 'Settings', 'mermaid-diagrams' ),
			DiagramCapabilities::CAP_MANAGE_SETTINGS,
			AdminRoute::SETTINGS_SLUG,
			array( self::class, 'render_settings_page' )
		);
	}

	/**
	 * Render diagram library shell template.
	 *
	 * @return void
	 */
	public static function render_library_page(): void {
		if ( ! current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'mermaid-diagrams' ) );
		}

		self::include_template( 'admin-app-root.php' );
	}

	/**
	 * Render settings shell template.
	 *
	 * @return void
	 */
	public static function render_settings_page(): void {
		if ( ! current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'mermaid-diagrams' ) );
		}

		self::include_template( 'admin-settings-root.php' );
	}

	/**
	 * Include an admin template if it exists.
	 *
	 * @param string $template_filename Template file name.
	 * @return void
	 */
	private static function include_template( string $template_filename ): void {
		$plugin_dir    = defined( 'MDM_PLUGIN_DIR' ) ? MDM_PLUGIN_DIR : dirname( __DIR__, 2 ) . '/';
		$template_path = $plugin_dir . 'templates/' . $template_filename;

		if ( file_exists( $template_path ) ) {
			include $template_path;
		}
	}
}
