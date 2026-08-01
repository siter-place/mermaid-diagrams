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
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode(
			'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="3" width="6" height="6" rx="1"/><rect x="9" y="15" width="6" height="6" rx="1"/><path d="M6 9v3a1 1 0 0 0 1 1h5m0 0h5a1 1 0 0 0 1-1V9m-6 4v2"/></svg>'
		);

		add_menu_page(
			__( 'Mermaid Diagrams', 'mermaid-diagrams' ),
			__( 'Diagrams', 'mermaid-diagrams' ),
			DiagramCapabilities::CAP_EDIT_DIAGRAMS,
			AdminRoute::LIBRARY_SLUG,
			array( self::class, 'render_library_page' ),
			$icon_svg,
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
