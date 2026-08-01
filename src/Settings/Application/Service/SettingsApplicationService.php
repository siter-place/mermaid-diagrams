<?php
/**
 * Settings Application Service.
 *
 * @package WebFalcon\MermaidDiagrams\Settings\Application\Service
 */

namespace WebFalcon\MermaidDiagrams\Settings\Application\Service;

use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Settings\Application\Command\UpdateSettingsSectionCommand;
use WebFalcon\MermaidDiagrams\Settings\Application\Query\GetSettingsQuery;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsRepository;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Application service for retrieving and updating settings.
 */
class SettingsApplicationService {

	/**
	 * Constructor.
	 *
	 * @param SettingsRepository $repository Repository instance.
	 */
	public function __construct(
		private SettingsRepository $repository
	) {
	}

	/**
	 * Get full settings response payload.
	 *
	 * @param GetSettingsQuery $query Query DTO.
	 * @return array<string, mixed> Settings payload.
	 */
	public function get_settings( GetSettingsQuery $query ): array {
		return array(
			'schema'       => SettingsSchema::schema_definition(),
			'values'       => $this->repository->get_all(),
			'capabilities' => array(
				'manageSettings' => current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ),
			),
			'runtime'      => array(
				'pluginVersion'  => MDM_VERSION,
				'mermaidVersion' => MDM_MERMAID_VERSION,
				'phpVersion'     => PHP_VERSION,
				'wpVersion'      => get_bloginfo( 'version' ),
			),
		);
	}

	/**
	 * Update a single settings section and return complete updated section values.
	 *
	 * @param UpdateSettingsSectionCommand $command Command DTO.
	 * @return array<string, mixed> Updated section key-value array.
	 */
	public function update_section( UpdateSettingsSectionCommand $command ): array {
		return $this->repository->update_section( $command->section(), $command->payload() );
	}
}
