<?php
/**
 * Settings Repository.
 *
 * @package WebFalcon\MermaidDiagrams\Settings\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Settings\Infrastructure;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persists and retrieves mdm_settings in WordPress options database.
 */
class SettingsRepository {

	/**
	 * Get all normalized settings sections.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function get_all(): array {
		$defaults = SettingsSchema::defaults();
		$raw      = get_option( SettingsSchema::OPTION_NAME, array() );

		if ( ! is_array( $raw ) ) {
			$raw = array();
		}

		$merged = array();
		foreach ( SettingsSchema::SECTIONS as $section ) {
			$section_raw = isset( $raw[ $section ] ) && is_array( $raw[ $section ] ) ? $raw[ $section ] : array();
			$merged[ $section ] = SettingsSchema::sanitize_section( $section, array_merge( $defaults[ $section ], $section_raw ) );
		}

		return $merged;
	}

	/**
	 * Get a single normalized section array.
	 *
	 * @param string $section Section name.
	 * @return array<string, mixed>
	 */
	public function get_section( string $section ): array {
		$all = $this->get_all();
		return $all[ $section ] ?? SettingsSchema::defaults()[ $section ] ?? array();
	}

	/**
	 * Save a single section and return all updated settings.
	 *
	 * @param string               $section Section name.
	 * @param array<string, mixed> $payload Key-value payload for section.
	 * @return array<string, mixed> Complete updated section payload.
	 */
	public function update_section( string $section, array $payload ): array {
		$all               = $this->get_all();
		$sanitized_section = SettingsSchema::sanitize_section( $section, array_merge( $all[ $section ] ?? array(), $payload ) );

		$all[ $section ] = $sanitized_section;
		update_option( SettingsSchema::OPTION_NAME, $all, false );

		return $sanitized_section;
	}
}
