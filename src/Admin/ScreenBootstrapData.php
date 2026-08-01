<?php
/**
 * Serializable bootstrap payload for admin React applications.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds screen-specific bootstrap data for JavaScript admin apps.
 */
class ScreenBootstrapData {

	/**
	 * Build bootstrap payload for the requested admin screen.
	 *
	 * @param string $screen Screen identifier: library|settings.
	 * @return array<string, mixed>
	 */
	public static function for_screen( string $screen ): array {
		return array(
			'screen'       => $screen,
			'restRoot'     => rest_url( 'mdm/v1' ),
			'nonce'        => wp_create_nonce( 'wp_rest' ),
			'locale'       => get_user_locale(),
			'capabilities' => array(
				'editDiagrams'   => current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ),
				'manageSettings' => current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ),
			),
			'routes'       => array(
				'library'   => AdminRoute::admin_url( AdminRoute::LIBRARY_SLUG ),
				'settings'  => AdminRoute::admin_url( AdminRoute::SETTINGS_SLUG ),
				'editorNew' => AdminRoute::admin_url( 'mdm-diagram-editor' ) . '&action=new',
			),
			'defaults'     => array(
				'perPage' => 20,
				'orderby' => 'modified',
				'order'   => 'DESC',
			),
			'i18n'         => self::i18n_strings(),
		);
	}

	/**
	 * Stable i18n keys consumed by admin React apps.
	 *
	 * @return array<string, string>
	 */
	private static function i18n_strings(): array {
		return array(
			'libraryTitle'        => __( 'Diagrams', 'mermaid-diagrams' ),
			'settingsTitle'       => __( 'Mermaid Diagrams Settings', 'mermaid-diagrams' ),
			'addDiagram'          => __( 'Add diagram', 'mermaid-diagrams' ),
			'loading'             => __( 'Loading diagrams…', 'mermaid-diagrams' ),
			'emptyTitle'          => __( 'No diagrams yet', 'mermaid-diagrams' ),
			'emptyDescription'    => __( 'Create your first diagram to see it listed here.', 'mermaid-diagrams' ),
			'errorTitle'          => __( 'Unable to load diagrams', 'mermaid-diagrams' ),
			'retry'               => __( 'Try again', 'mermaid-diagrams' ),
			'previousPage'        => __( 'Previous page', 'mermaid-diagrams' ),
			'nextPage'            => __( 'Next page', 'mermaid-diagrams' ),
			'pageOf'              => __( 'Page %1$s of %2$s', 'mermaid-diagrams' ),
			'saveSettings'        => __( 'Save settings', 'mermaid-diagrams' ),
			'settingsSaved'       => __( 'Settings saved.', 'mermaid-diagrams' ),
			'settingsSaveError'   => __( 'Unable to save settings.', 'mermaid-diagrams' ),
			'permissionDenied'    => __( 'You do not have permission to manage plugin settings.', 'mermaid-diagrams' ),
			'columnTitle'         => __( 'Title', 'mermaid-diagrams' ),
			'columnStatus'        => __( 'Status', 'mermaid-diagrams' ),
			'columnAuthor'        => __( 'Author', 'mermaid-diagrams' ),
			'columnModified'      => __( 'Modified', 'mermaid-diagrams' ),
			'columnUsage'         => __( 'Usage', 'mermaid-diagrams' ),
			'comingSoon'          => __( 'Coming in a later phase', 'mermaid-diagrams' ),
		);
	}
}
