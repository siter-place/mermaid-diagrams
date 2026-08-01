<?php
/**
 * Settings Schema & Defaults Provider.
 *
 * @package WebFalcon\MermaidDiagrams\Settings\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Settings\Infrastructure;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defines authoritative settings schema, sections, default values, and sanitizers.
 */
class SettingsSchema {

	public const OPTION_NAME = 'mdm_settings';

	public const SECTION_RENDERING      = 'rendering';
	public const SECTION_DOWNLOADS      = 'downloads';
	public const SECTION_EDITOR         = 'editor';
	public const SECTION_VISUAL_EDITOR  = 'visual_editor';
	public const SECTION_PERMISSIONS    = 'permissions';
	public const SECTION_DATA_RETENTION = 'data_retention';

	/**
	 * List of valid settings sections.
	 *
	 * @var array<string>
	 */
	public const SECTIONS = array(
		self::SECTION_RENDERING,
		self::SECTION_DOWNLOADS,
		self::SECTION_EDITOR,
		self::SECTION_VISUAL_EDITOR,
		self::SECTION_PERMISSIONS,
		self::SECTION_DATA_RETENTION,
	);

	/**
	 * Get default settings values for all sections.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public static function defaults(): array {
		return array(
			self::SECTION_RENDERING      => array(
				'defaultTheme'      => 'default',
				'defaultToolbar'    => true,
				'maxSourceLength'   => 524288,
				'defaultWidth'      => '100%',
				'defaultHeight'     => 480,
				'publicSourceAccess' => true,
			),
			self::SECTION_DOWNLOADS      => array(
				'allowSource' => true,
				'allowSvg'    => true,
				'allowPng'    => false,
			),
			self::SECTION_EDITOR         => array(
				'liveValidationDebounceMs' => 300,
				'lineNumbers'              => true,
				'autocomplete'             => true,
			),
			self::SECTION_VISUAL_EDITOR  => array(
				'enabled'                  => true,
				'allowFlowchartAdapter'    => true,
				'experimentalBetaAdapters' => false,
			),
			self::SECTION_PERMISSIONS    => array(
				'requirePublishCap'  => true,
				'allowAuthorLibrary' => true,
			),
			self::SECTION_DATA_RETENTION => array(
				'uninstallPolicy' => 'preserve', // preserve, settings_only, complete_purge
			),
		);
	}

	/**
	 * Get settings UI schema structure.
	 *
	 * @return array<string, mixed>
	 */
	public static function schema_definition(): array {
		return array(
			'title'       => __( 'Mermaid Diagrams Settings', 'mermaid-diagrams' ),
			'description' => __( 'Configure rendering defaults, downloads, editor behavior, and permissions.', 'mermaid-diagrams' ),
			'sections'    => array(
				array(
					'id'    => self::SECTION_RENDERING,
					'title' => __( 'Rendering Defaults', 'mermaid-diagrams' ),
				),
				array(
					'id'    => self::SECTION_DOWNLOADS,
					'title' => __( 'Downloads & Exports', 'mermaid-diagrams' ),
				),
				array(
					'id'    => self::SECTION_EDITOR,
					'title' => __( 'Editor Configuration', 'mermaid-diagrams' ),
				),
				array(
					'id'    => self::SECTION_VISUAL_EDITOR,
					'title' => __( 'Visual Editor (Beta)', 'mermaid-diagrams' ),
				),
				array(
					'id'    => self::SECTION_PERMISSIONS,
					'title' => __( 'Permissions & Capabilities', 'mermaid-diagrams' ),
				),
				array(
					'id'    => self::SECTION_DATA_RETENTION,
					'title' => __( 'Data Retention & Uninstall', 'mermaid-diagrams' ),
				),
			),
		);
	}

	/**
	 * Sanitize and normalize a specific section payload.
	 *
	 * @param string               $section Section name.
	 * @param array<string, mixed> $input Raw input key-value array.
	 * @return array<string, mixed> Normalized section array.
	 */
	public static function sanitize_section( string $section, array $input ): array {
		$defaults = self::defaults();
		$base     = $defaults[ $section ] ?? array();
		$clean    = array();

		foreach ( $base as $key => $default_val ) {
			if ( ! array_key_exists( $key, $input ) ) {
				$clean[ $key ] = $default_val;
				continue;
			}

			$val = $input[ $key ];
			if ( is_bool( $default_val ) ) {
				$clean[ $key ] = (bool) $val;
			} elseif ( is_int( $default_val ) ) {
				$clean[ $key ] = (int) $val;
			} else {
				$clean[ $key ] = sanitize_text_field( (string) $val );
			}
		}

		return $clean;
	}
}
