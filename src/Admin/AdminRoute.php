<?php
/**
 * Admin route slugs and screen identifiers.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Centralizes admin page slugs and hook suffix helpers.
 */
class AdminRoute {

	public const LIBRARY_SLUG  = 'mdm-diagrams';
	public const SETTINGS_SLUG = 'mdm-settings';

	/**
	 * Expected hook suffix for the library top-level menu page.
	 */
	public const LIBRARY_HOOK = 'toplevel_page_mdm-diagrams';

	/**
	 * Expected hook suffix for the settings submenu page.
	 */
	public const SETTINGS_HOOK = 'diagrams_page_mdm-settings';

	/**
	 * Build admin URL for a plugin screen.
	 *
	 * @param string $slug Page slug.
	 * @return string
	 */
	public static function admin_url( string $slug ): string {
		return admin_url( 'admin.php?page=' . rawurlencode( $slug ) );
	}

	/**
	 * Determine whether the current admin screen matches a plugin page.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 * @param string $slug        Expected page slug.
	 * @return bool
	 */
	public static function is_screen( string $hook_suffix, string $slug ): bool {
		if ( self::LIBRARY_SLUG === $slug ) {
			return self::LIBRARY_HOOK === $hook_suffix;
		}

		if ( self::SETTINGS_SLUG === $slug ) {
			return self::SETTINGS_HOOK === $hook_suffix;
		}

		return false;
	}
}
