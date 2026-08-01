<?php
/**
 * Admin Service Provider.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

namespace WebFalcon\MermaidDiagrams\Admin;

use WebFalcon\MermaidDiagrams\Admin\Cli\MdmCliCommand;
use WebFalcon\MermaidDiagrams\Bootstrap\Container;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service provider for WordPress Admin integrations.
 */
class AdminServiceProvider implements ServiceProvider {

	/**
	 * Register admin services in container.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	public function register( Container $container ): void {
		// Admin services bound in subsequent tasks.
	}

	/**
	 * Boot admin hooks (menus, CLI commands, screens).
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action( 'admin_menu', array( AdminMenu::class, 'register_menu' ) );
		MdmCliCommand::register();
	}
}
