<?php
/**
 * Serializable bootstrap payload for admin React applications.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramType;
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
				'editDiagrams'    => current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ),
				'manageSettings'  => current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ),
				'manageTerms'     => current_user_can( DiagramCapabilities::CAP_MANAGE_TERMS ),
				'createDiagrams'  => current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ),
				'publishDiagrams' => current_user_can( DiagramCapabilities::CAP_PUBLISH_DIAGRAMS ),
			),
			'diagramTypes' => self::diagram_type_options(),
			'routes'       => array(
				'library'   => AdminRoute::admin_url( AdminRoute::LIBRARY_SLUG ),
				'settings'  => AdminRoute::admin_url( AdminRoute::SETTINGS_SLUG ),
				'editor'    => AdminRoute::admin_url( 'mdm-diagram-editor' ),
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
			'searchPlaceholder'   => __( 'Search diagrams', 'mermaid-diagrams' ),
			'filterCategory'      => __( 'Category', 'mermaid-diagrams' ),
			'filterTag'           => __( 'Tag', 'mermaid-diagrams' ),
			'filterType'          => __( 'Type', 'mermaid-diagrams' ),
			'filterStatus'        => __( 'Status', 'mermaid-diagrams' ),
			'filterAuthor'        => __( 'Author', 'mermaid-diagrams' ),
			'filterSort'          => __( 'Sort by', 'mermaid-diagrams' ),
			'resetFilters'        => __( 'Reset filters', 'mermaid-diagrams' ),
			'noMatchTitle'        => __( 'No diagrams match your filters', 'mermaid-diagrams' ),
			'noMatchDescription'  => __( 'Try adjusting your search or filters.', 'mermaid-diagrams' ),
			'columnCategories'    => __( 'Categories', 'mermaid-diagrams' ),
			'columnTags'          => __( 'Tags', 'mermaid-diagrams' ),
			'selectAll'           => __( 'Select all on page', 'mermaid-diagrams' ),
			'bulkActions'         => __( 'Bulk actions', 'mermaid-diagrams' ),
			'bulkAddCategories'   => __( 'Add categories', 'mermaid-diagrams' ),
			'bulkRemoveCategories'=> __( 'Remove categories', 'mermaid-diagrams' ),
			'bulkReplaceCategories'=> __( 'Replace categories', 'mermaid-diagrams' ),
			'bulkAddTags'         => __( 'Add tags', 'mermaid-diagrams' ),
			'bulkRemoveTags'      => __( 'Remove tags', 'mermaid-diagrams' ),
			'bulkSetStatus'       => __( 'Change status', 'mermaid-diagrams' ),
			'bulkTrash'           => __( 'Move to trash', 'mermaid-diagrams' ),
			'bulkRestore'         => __( 'Restore', 'mermaid-diagrams' ),
			'bulkApply'           => __( 'Apply', 'mermaid-diagrams' ),
			'bulkSummary'         => __( '%1$s succeeded, %2$s failed.', 'mermaid-diagrams' ),
			'preview'             => __( 'Preview', 'mermaid-diagrams' ),
			'previewClose'        => __( 'Close', 'mermaid-diagrams' ),
			'editDiagram'         => __( 'Edit', 'mermaid-diagrams' ),
			'duplicateDiagram'    => __( 'Duplicate', 'mermaid-diagrams' ),
			'trashDiagram'        => __( 'Trash', 'mermaid-diagrams' ),
			'restoreDiagram'      => __( 'Restore', 'mermaid-diagrams' ),
			'confirmTrash'        => __( 'Confirm', 'mermaid-diagrams' ),
			'trashedNotice'       => __( 'Diagram moved to trash.', 'mermaid-diagrams' ),
			'duplicatedNotice'    => __( 'Diagram duplicated.', 'mermaid-diagrams' ),
			'savedNotice'         => __( 'Diagram saved.', 'mermaid-diagrams' ),
			'quickCreateTitle'    => __( 'Create diagram', 'mermaid-diagrams' ),
			'fieldTitle'          => __( 'Title', 'mermaid-diagrams' ),
			'fieldTitleHelp'      => __( 'Provide a descriptive title for this diagram.', 'mermaid-diagrams' ),
			'fieldSource'         => __( 'Mermaid source', 'mermaid-diagrams' ),
			'fieldSourceHelp'     => __( 'Enter valid Mermaid syntax (e.g. flowchart, sequenceDiagram).', 'mermaid-diagrams' ),
			'columnActions'       => __( 'Actions', 'mermaid-diagrams' ),
			'editHelp'            => __( 'Open in diagram editor', 'mermaid-diagrams' ),
			'save'                => __( 'Save', 'mermaid-diagrams' ),
			'cancel'              => __( 'Cancel', 'mermaid-diagrams' ),
			'invalidSource'       => __( 'Mermaid source is invalid.', 'mermaid-diagrams' ),
			'previewLoading'      => __( 'Loading preview…', 'mermaid-diagrams' ),
			'thumbnailMissing'    => __( 'No thumbnail available', 'mermaid-diagrams' ),
			'usageSummary'        => __( 'Used in %1$s places', 'mermaid-diagrams' ),
		);
	}

	/**
	 * Known diagram type filter options.
	 *
	 * @return array<int, array{value: string, label: string}>
	 */
	private static function diagram_type_options(): array {
		$types = array(
			'flowchart',
			'sequenceDiagram',
			'classDiagram',
			'stateDiagram',
			'erDiagram',
			'gantt',
			'pie',
			'journey',
			'gitGraph',
			'mindmap',
			'timeline',
			'quadrantChart',
			DiagramType::UNKNOWN,
		);

		return array_map(
			static function ( string $type ): array {
				return array(
					'value' => $type,
					'label' => $type,
				);
			},
			$types
		);
	}
}
