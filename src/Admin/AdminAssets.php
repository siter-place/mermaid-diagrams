<?php
/**
 * Admin script and style enqueue for React applications.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues screen-scoped admin assets with bootstrap data.
 */
class AdminAssets {

	/**
	 * Register admin asset hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue' ) );
	}

	/**
	 * Enqueue assets for plugin admin screens.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public static function enqueue( string $hook_suffix ): void {
		if ( AdminRoute::is_screen( $hook_suffix, AdminRoute::LIBRARY_SLUG ) ) {
			if ( ! current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) ) {
				return;
			}

			self::enqueue_app(
				'library',
				'mdm-diagram-library',
				'build/admin/library/index.js',
				'build/admin/library/index.css'
			);
			return;
		}

		if ( AdminRoute::is_screen( $hook_suffix, AdminRoute::SETTINGS_SLUG ) ) {
			if ( ! current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ) ) {
				return;
			}

			self::enqueue_app(
				'settings',
				'mdm-diagram-settings',
				'build/admin/settings/index.js',
				'build/admin/settings/index.css'
			);
		}
	}

	/**
	 * Enqueue a single admin React application bundle.
	 *
	 * @param string $screen      Bootstrap screen key.
	 * @param string $handle      Script handle.
	 * @param string $script_path Relative script path from plugin root.
	 * @param string $style_path  Relative style path from plugin root.
	 * @return void
	 */
	private static function enqueue_app(
		string $screen,
		string $handle,
		string $script_path,
		string $style_path
	): void {
		$plugin_dir = defined( 'MDM_PLUGIN_DIR' ) ? MDM_PLUGIN_DIR : dirname( __DIR__, 2 ) . '/';
		$plugin_url = defined( 'MDM_PLUGIN_URL' ) ? MDM_PLUGIN_URL : plugin_dir_url( $plugin_dir . 'mermaid-diagrams.php' );
		$version    = defined( 'MDM_VERSION' ) ? MDM_VERSION : '1.0.0';

		$script_file = $plugin_dir . $script_path;
		$style_file  = $plugin_dir . $style_path;

		if ( ! file_exists( $script_file ) ) {
			return;
		}

		wp_enqueue_script(
			$handle,
			$plugin_url . $script_path,
			array(
				'wp-element',
				'wp-components',
				'wp-api-fetch',
				'wp-i18n',
			),
			$version,
			true
		);

		wp_set_script_translations( $handle, 'mermaid-diagrams', $plugin_dir . 'languages' );

		if ( file_exists( $style_file ) ) {
			wp_enqueue_style(
				$handle,
				$plugin_url . $style_path,
				array( 'wp-components' ),
				$version
			);
		}

		$bootstrap = wp_json_encode( ScreenBootstrapData::for_screen( $screen ) );
		if ( false === $bootstrap ) {
			return;
		}

		wp_add_inline_script(
			$handle,
			'window.mdmAdminBootstrap = ' . $bootstrap . ';',
			'before'
		);
	}
}
