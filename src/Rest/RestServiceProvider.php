<?php
/**
 * REST Service Provider.
 *
 * @package WebFalcon\MermaidDiagrams\Rest
 */

namespace WebFalcon\MermaidDiagrams\Rest;

use WebFalcon\MermaidDiagrams\Bootstrap\Container;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\DiagramApplicationService;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramRepository;
use WebFalcon\MermaidDiagrams\Rest\Controller\DiagramBulkController;
use WebFalcon\MermaidDiagrams\Rest\Controller\DiagramCollectionController;
use WebFalcon\MermaidDiagrams\Rest\Controller\DiagramItemController;
use WebFalcon\MermaidDiagrams\Rest\Controller\DiagramUsageController;
use WebFalcon\MermaidDiagrams\Rest\Controller\SettingsController;
use WebFalcon\MermaidDiagrams\Settings\Application\Service\SettingsApplicationService;
use WebFalcon\MermaidDiagrams\Settings\Infrastructure\SettingsRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service provider for registering REST controllers and endpoints.
 */
class RestServiceProvider implements ServiceProvider {

	/**
	 * Container instance reference.
	 *
	 * @var Container|null
	 */
	private ?Container $container = null;

	/**
	 * Register services in DI container.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	public function register( Container $container ): void {
		$this->container = $container;

		$container->bind(
			DiagramApplicationService::class,
			function ( Container $c ) {
				return new DiagramApplicationService( $c->get( DiagramRepository::class ) );
			}
		);

		$container->bind(
			SettingsRepository::class,
			function () {
				return new SettingsRepository();
			}
		);

		$container->bind(
			SettingsApplicationService::class,
			function ( Container $c ) {
				return new SettingsApplicationService( $c->get( SettingsRepository::class ) );
			}
		);

		$container->bind(
			DiagramCollectionController::class,
			function ( Container $c ) {
				return new DiagramCollectionController( $c->get( DiagramApplicationService::class ) );
			}
		);

		$container->bind(
			DiagramItemController::class,
			function ( Container $c ) {
				return new DiagramItemController( $c->get( DiagramApplicationService::class ) );
			}
		);

		$container->bind(
			DiagramBulkController::class,
			function ( Container $c ) {
				return new DiagramBulkController( $c->get( DiagramApplicationService::class ) );
			}
		);

		$container->bind(
			DiagramUsageController::class,
			function ( Container $c ) {
				return new DiagramUsageController( $c->get( DiagramApplicationService::class ) );
			}
		);

		$container->bind(
			SettingsController::class,
			function ( Container $c ) {
				return new SettingsController( $c->get( SettingsApplicationService::class ) );
			}
		);
	}

	/**
	 * Boot hooks and route registrations.
	 *
	 * @return void
	 */
	public function boot(): void {
		add_action(
			'rest_api_init',
			function () {
				if ( null === $this->container ) {
					return;
				}
				$this->container->get( DiagramCollectionController::class )->register_routes();
				$this->container->get( DiagramItemController::class )->register_routes();
				$this->container->get( DiagramBulkController::class )->register_routes();
				$this->container->get( DiagramUsageController::class )->register_routes();
				$this->container->get( SettingsController::class )->register_routes();
			}
		);
	}
}
