<?php
/**
 * Protected Post Meta Registration.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Infrastructure;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Manages protected post meta registrations for mdm_diagram CPT.
 */
class DiagramMeta {

	public const META_DIAGRAM_TYPE       = '_mdm_diagram_type';
	public const META_RENDER_CONFIG      = '_mdm_render_config';
	public const META_VISUAL_MODEL       = '_mdm_visual_model';
	public const META_VISUAL_ADAPTER     = '_mdm_visual_adapter';
	public const META_SOURCE_HASH        = '_mdm_source_hash';
	public const META_RENDERER_VERSION   = '_mdm_renderer_version';
	public const META_VALIDATION_STATE   = '_mdm_validation_state';
	public const META_VALIDATION_SUMMARY = '_mdm_validation_summary';
	public const META_LAST_EDITOR_ID     = '_mdm_last_editor_id';

	/**
	 * Register all protected post meta.
	 *
	 * @return void
	 */
	public static function register(): void {
		$post_type = DiagramPostType::CPT_SLUG;

		$auth_callback = function ( $allowed, $meta_key, $post_id ) {
			return current_user_can( 'edit_post', $post_id );
		};

		register_post_meta(
			$post_type,
			self::META_DIAGRAM_TYPE,
			array(
				'type'              => 'string',
				'description'       => __( 'Detected Mermaid diagram type', 'mermaid-diagrams' ),
				'single'            => true,
				'default'           => 'unknown',
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			$post_type,
			self::META_RENDER_CONFIG,
			array(
				'type'          => 'object',
				'description'   => __( 'Presentation and render configuration', 'mermaid-diagrams' ),
				'single'        => true,
				'default'       => array(
					'theme'               => 'default',
					'showToolbar'         => true,
					'allowSourceDownload' => true,
					'allowSvgDownload'    => true,
				),
				'show_in_rest'  => array(
					'schema' => array(
						'type'                 => 'object',
						'additionalProperties' => true,
					),
				),
				'auth_callback' => $auth_callback,
			)
		);

		register_post_meta(
			$post_type,
			self::META_VISUAL_MODEL,
			array(
				'type'          => 'object',
				'description'   => __( 'Optional derived visual-editor model', 'mermaid-diagrams' ),
				'single'        => true,
				'show_in_rest'  => array(
					'schema' => array(
						'type'                 => 'object',
						'additionalProperties' => true,
					),
				),
				'auth_callback' => $auth_callback,
			)
		);

		register_post_meta(
			$post_type,
			self::META_VISUAL_ADAPTER,
			array(
				'type'              => 'string',
				'description'       => __( 'Visual editor adapter identifier and version', 'mermaid-diagrams' ),
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			$post_type,
			self::META_SOURCE_HASH,
			array(
				'type'              => 'string',
				'description'       => __( 'SHA-256 source hash', 'mermaid-diagrams' ),
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			$post_type,
			self::META_RENDERER_VERSION,
			array(
				'type'              => 'string',
				'description'       => __( 'Mermaid/runtime version used for last validation', 'mermaid-diagrams' ),
				'single'            => true,
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			$post_type,
			self::META_VALIDATION_STATE,
			array(
				'type'              => 'string',
				'description'       => __( 'Validation status (valid, invalid, unknown)', 'mermaid-diagrams' ),
				'single'            => true,
				'default'           => 'unknown',
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		register_post_meta(
			$post_type,
			self::META_VALIDATION_SUMMARY,
			array(
				'type'          => 'object',
				'description'   => __( 'Safe diagnostic summary of validation proof', 'mermaid-diagrams' ),
				'single'        => true,
				'show_in_rest'  => array(
					'schema' => array(
						'type'                 => 'object',
						'additionalProperties' => true,
					),
				),
				'auth_callback' => $auth_callback,
			)
		);

		register_post_meta(
			$post_type,
			self::META_LAST_EDITOR_ID,
			array(
				'type'              => 'integer',
				'description'       => __( 'User ID of last user who edited diagram', 'mermaid-diagrams' ),
				'single'            => true,
				'default'           => 0,
				'show_in_rest'      => true,
				'auth_callback'     => $auth_callback,
				'sanitize_callback' => 'absint',
			)
		);
	}
}
