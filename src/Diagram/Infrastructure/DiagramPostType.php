<?php
/**
 * Diagram CPT Registration.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Infrastructure;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages registration for mdm_diagram Custom Post Type.
 */
class DiagramPostType {

	public const CPT_SLUG = 'mdm_diagram';

	/**
	 * Register mdm_diagram post type.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( post_type_exists( self::CPT_SLUG ) ) {
			return;
		}

		$labels = array(
			'name'               => _x( 'Diagrams', 'post type general name', 'mermaid-diagrams' ),
			'singular_name'      => _x( 'Diagram', 'post type singular name', 'mermaid-diagrams' ),
			'menu_name'          => _x( 'Diagrams', 'admin menu', 'mermaid-diagrams' ),
			'name_admin_bar'     => _x( 'Diagram', 'add new on admin bar', 'mermaid-diagrams' ),
			'add_new'            => _x( 'Add New', 'diagram', 'mermaid-diagrams' ),
			'add_new_item'       => __( 'Add New Diagram', 'mermaid-diagrams' ),
			'new_item'           => __( 'New Diagram', 'mermaid-diagrams' ),
			'edit_item'          => __( 'Edit Diagram', 'mermaid-diagrams' ),
			'view_item'          => __( 'View Diagram', 'mermaid-diagrams' ),
			'all_items'          => __( 'All Diagrams', 'mermaid-diagrams' ),
			'search_items'       => __( 'Search Diagrams', 'mermaid-diagrams' ),
			'not_found'          => __( 'No diagrams found.', 'mermaid-diagrams' ),
			'not_found_in_trash' => __( 'No diagrams found in Trash.', 'mermaid-diagrams' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => false,
			'show_in_menu'       => false,
			'show_in_nav_menus'  => false,
			'show_in_admin_bar'  => false,
			'show_in_rest'       => true,
			'rest_base'          => 'mdm-diagrams',
			'supports'           => array( 'title', 'excerpt', 'revisions', 'author', 'editor' ),
			'capability_type'    => array( 'mdm_diagram', 'mdm_diagrams' ),
			'map_meta_cap'       => true,
			'hierarchical'       => false,
			'has_archive'        => false,
			'rewrite'            => false,
			'query_var'          => false,
			'can_export'         => true,
			'delete_with_user'   => false,
		);

		register_post_type( self::CPT_SLUG, $args );
	}
}
