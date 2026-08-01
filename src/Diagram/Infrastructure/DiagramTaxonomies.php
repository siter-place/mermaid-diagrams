<?php
/**
 * Diagram Taxonomies Registration.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Infrastructure;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages category and tag taxonomy registrations for mdm_diagram.
 */
class DiagramTaxonomies {

	public const TAXONOMY_CATEGORY = 'mdm_diagram_category';
	public const TAXONOMY_TAG      = 'mdm_diagram_tag';

	/**
	 * Register taxonomies.
	 *
	 * @return void
	 */
	public static function register(): void {
		self::register_categories();
		self::register_tags();
	}

	/**
	 * Register hierarchical category taxonomy.
	 *
	 * @return void
	 */
	private static function register_categories(): void {
		if ( taxonomy_exists( self::TAXONOMY_CATEGORY ) ) {
			return;
		}

		$labels = array(
			'name'              => _x( 'Diagram Categories', 'taxonomy general name', 'mermaid-diagrams' ),
			'singular_name'     => _x( 'Diagram Category', 'taxonomy singular name', 'mermaid-diagrams' ),
			'search_items'      => __( 'Search Categories', 'mermaid-diagrams' ),
			'all_items'         => __( 'All Categories', 'mermaid-diagrams' ),
			'parent_item'       => __( 'Parent Category', 'mermaid-diagrams' ),
			'parent_item_colon' => __( 'Parent Category:', 'mermaid-diagrams' ),
			'edit_item'         => __( 'Edit Category', 'mermaid-diagrams' ),
			'update_item'       => __( 'Update Category', 'mermaid-diagrams' ),
			'add_new_item'      => __( 'Add New Category', 'mermaid-diagrams' ),
			'new_item_name'     => __( 'New Category Name', 'mermaid-diagrams' ),
			'menu_name'         => __( 'Categories', 'mermaid-diagrams' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => false,
			'show_ui'           => false,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rest_base'         => 'mdm-diagram-categories',
			'rewrite'           => false,
			'capabilities'      => array(
				'manage_terms' => 'manage_mdm_diagram_terms',
				'edit_terms'   => 'manage_mdm_diagram_terms',
				'delete_terms' => 'manage_mdm_diagram_terms',
				'assign_terms' => 'edit_mdm_diagrams',
			),
		);

		register_taxonomy( self::TAXONOMY_CATEGORY, array( DiagramPostType::CPT_SLUG ), $args );
	}

	/**
	 * Register flat tag taxonomy.
	 *
	 * @return void
	 */
	private static function register_tags(): void {
		if ( taxonomy_exists( self::TAXONOMY_TAG ) ) {
			return;
		}

		$labels = array(
			'name'          => _x( 'Diagram Tags', 'taxonomy general name', 'mermaid-diagrams' ),
			'singular_name' => _x( 'Diagram Tag', 'taxonomy singular name', 'mermaid-diagrams' ),
			'search_items'  => __( 'Search Tags', 'mermaid-diagrams' ),
			'all_items'     => __( 'All Tags', 'mermaid-diagrams' ),
			'edit_item'     => __( 'Edit Tag', 'mermaid-diagrams' ),
			'update_item'   => __( 'Update Tag', 'mermaid-diagrams' ),
			'add_new_item'  => __( 'Add New Tag', 'mermaid-diagrams' ),
			'new_item_name' => __( 'New Tag Name', 'mermaid-diagrams' ),
			'menu_name'     => __( 'Tags', 'mermaid-diagrams' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => false,
			'show_ui'           => false,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rest_base'         => 'mdm-diagram-tags',
			'rewrite'           => false,
			'capabilities'      => array(
				'manage_terms' => 'manage_mdm_diagram_terms',
				'edit_terms'   => 'manage_mdm_diagram_terms',
				'delete_terms' => 'manage_mdm_diagram_terms',
				'assign_terms' => 'edit_mdm_diagrams',
			),
		);

		register_taxonomy( self::TAXONOMY_TAG, array( DiagramPostType::CPT_SLUG ), $args );
	}
}
