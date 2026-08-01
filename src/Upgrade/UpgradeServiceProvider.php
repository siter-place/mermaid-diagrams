<?php
/**
 * Upgrade Service Provider.
 *
 * @package WebFalcon\MermaidDiagrams\Upgrade
 */

namespace WebFalcon\MermaidDiagrams\Upgrade;

use WebFalcon\MermaidDiagrams\Bootstrap\Container;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service provider for Database migrations and upgrade routines.
 */
class UpgradeServiceProvider implements ServiceProvider {

	/**
	 * Register upgrade services in container.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	public function register( Container $container ): void {
		$container->bind(
			UpgradeRunner::class,
			function () {
				return new UpgradeRunner();
			}
		);
	}

	/**
	 * Boot upgrade hooks.
	 *
	 * @return void
	 */
	public function boot(): void {
		$runner = new UpgradeRunner();
		$runner->run();
	}
}
