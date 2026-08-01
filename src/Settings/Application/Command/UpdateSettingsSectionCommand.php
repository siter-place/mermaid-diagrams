<?php
/**
 * Update Settings Section Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Settings\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Settings\Application\Command;

use WebFalcon\MermaidDiagrams\Settings\Application\Exception\InvalidSettingsSectionException;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for updating a section of settings.
 */
class UpdateSettingsSectionCommand {

	/**
	 * Constructor.
	 *
	 * @param string               $section Section ID.
	 * @param array<string, mixed> $payload Key-value payload for section.
	 * @throws InvalidSettingsSectionException If section is unknown.
	 */
	public function __construct(
		private string $section,
		private array $payload
	) {
		if ( ! in_array( $section, SettingsSchema::SECTIONS, true ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidSettingsSectionException( sprintf( 'Invalid settings section "%s". Allowed: %s', $section, implode( ', ', SettingsSchema::SECTIONS ) ) );
		}
	}

	/**
	 * Get Section ID.
	 *
	 * @return string
	 */
	public function section(): string {
		return $this->section;
	}

	/**
	 * Get Section Payload.
	 *
	 * @return array<string, mixed>
	 */
	public function payload(): array {
		return $this->payload;
	}
}
