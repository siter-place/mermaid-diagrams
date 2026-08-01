<?php
/**
 * Download Policy Service.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Service
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Service;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsRepository;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsSchema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Evaluates authorization and policy settings for source/SVG downloads.
 */
class DownloadPolicyService {

	/**
	 * Settings repository instance.
	 *
	 * @var SettingsRepository
	 */
	private SettingsRepository $settings_repository;

	/**
	 * Constructor.
	 *
	 * @param SettingsRepository|null $settings_repository Settings repository instance.
	 */
	public function __construct(
		?SettingsRepository $settings_repository = null
	) {
		$this->settings_repository = $settings_repository ?? new SettingsRepository();
	}

	/**
	 * Can current user or public user download source for a given diagram.
	 *
	 * @param Diagram $diagram Diagram domain aggregate.
	 * @param int     $user_id User ID checking download access.
	 * @return bool
	 */
	public function can_download_source( Diagram $diagram, int $user_id = 0 ): bool {
		$download_settings = $this->settings_repository->get_section( SettingsSchema::SECTION_DOWNLOADS );
		if ( empty( $download_settings['allowSource'] ) ) {
			return false;
		}

		$rendering_settings = $this->settings_repository->get_section( SettingsSchema::SECTION_RENDERING );
		$public_access      = ! empty( $rendering_settings['publicSourceAccess'] );

		// Check diagram per-diagram render config.
		$config_allow = $diagram->render_config()?->get( 'allowSourceDownload', true ) ?? true;
		if ( ! $config_allow ) {
			return false;
		}

		if ( 0 === $user_id && ! $public_access ) {
			return false;
		}

		$diagram_id = $diagram->id()?->value() ?? 0;
		if ( $user_id > 0 && ! current_user_can( 'read_post', $diagram_id ) && ! current_user_can( 'edit_post', $diagram_id ) && 'publish' !== $diagram->status()->value() ) {
			return false;
		}

		return true;
	}

	/**
	 * Can current user download SVG for a given diagram.
	 *
	 * @param Diagram $diagram Diagram domain aggregate.
	 * @param int     $user_id User ID checking download access.
	 * @return bool
	 */
	public function can_download_svg( Diagram $diagram, int $user_id = 0 ): bool {
		$download_settings = $this->settings_repository->get_section( SettingsSchema::SECTION_DOWNLOADS );
		if ( empty( $download_settings['allowSvg'] ) ) {
			return false;
		}

		$config_allow = $diagram->render_config()?->get( 'allowSvgDownload', true ) ?? true;
		if ( ! $config_allow ) {
			return false;
		}

		$diagram_id = $diagram->id()?->value() ?? 0;
		if ( $user_id > 0 && ! current_user_can( 'read_post', $diagram_id ) && ! current_user_can( 'edit_post', $diagram_id ) && 'publish' !== $diagram->status()->value() ) {
			return false;
		}

		return true;
	}

	/**
	 * Format safe download filename.
	 *
	 * @param string $title     Diagram title.
	 * @param int    $id        Diagram ID.
	 * @param string $extension Extension without dot (e.g. 'mmd' or 'svg').
	 * @return string
	 */
	public static function format_filename( string $title, int $id, string $extension ): string {
		$slug = sanitize_title( $title );
		if ( empty( $slug ) ) {
			$slug = 'diagram';
		}
		return sprintf( '%s-%d.%s', $slug, $id, ltrim( $extension, '.' ) );
	}
}
