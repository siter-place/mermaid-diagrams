<?php
/**
 * REST Settings Controller (/mdm/v1/settings).
 *
 * @package WebFalcon\MermaidDiagrams\Rest\Controller
 */

namespace WebFalcon\MermaidDiagrams\Rest\Controller;

use Throwable;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WebFalcon\MermaidDiagrams\Settings\Application\Command\UpdateSettingsSectionCommand;
use WebFalcon\MermaidDiagrams\Settings\Application\Query\GetSettingsQuery;
use WebFalcon\MermaidDiagrams\Settings\Application\Service\SettingsApplicationService;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsSchema;
use WebFalcon\MermaidDiagrams\Support\WordPressErrorMapper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Controller for managing plugin settings endpoints.
 */
class SettingsController extends WP_REST_Controller {

	/**
	 * REST namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'mdm/v1';

	/**
	 * REST route base.
	 *
	 * @var string
	 */
	protected $rest_base = 'settings';

	/**
	 * Constructor.
	 *
	 * @param SettingsApplicationService $settings_service Application service.
	 */
	public function __construct(
		private SettingsApplicationService $settings_service
	) {
	}

	/**
	 * Register REST routes.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_settings' ),
					'permission_callback' => array( $this, 'settings_permissions_check' ),
				),
			)
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base . '/(?P<section>[\w\-]+)',
			array(
				array(
					'methods'             => WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_section' ),
					'permission_callback' => array( $this, 'settings_permissions_check' ),
					'args'                => array(
						'section' => array(
							'type'     => 'string',
							'required' => true,
							'enum'     => SettingsSchema::SECTIONS,
						),
					),
				),
			)
		);
	}

	/**
	 * Permission check for settings endpoints.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return bool
	 */
	public function settings_permissions_check( $request ): bool {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Get full settings schema and values.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function get_settings( $request ) {
		try {
			$query    = new GetSettingsQuery();
			$settings = $this->settings_service->get_settings( $query );

			return rest_ensure_response( $settings );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}

	/**
	 * Update a single settings section.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|\WP_Error
	 */
	public function update_section( $request ) {
		try {
			$section = (string) $request->get_param( 'section' );
			$params  = $request->get_json_params();

			if ( ! is_array( $params ) ) {
				$params = $request->get_body_params();
			}

			$command = new UpdateSettingsSectionCommand( $section, $params );
			$updated = $this->settings_service->update_section( $command );

			return rest_ensure_response( $updated );
		} catch ( Throwable $ex ) {
			return WordPressErrorMapper::to_wp_error( $ex );
		}
	}
}
