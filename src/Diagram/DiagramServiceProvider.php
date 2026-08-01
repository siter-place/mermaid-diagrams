<?php
/**
 * Diagram Service Provider.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Diagram;

use WebFalcon\MermaidDiagrams\Bootstrap\Container;
use WebFalcon\MermaidDiagrams\Bootstrap\ServiceProvider;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramRepository;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\WordPressDiagramRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Service provider for Diagram context (CPT, taxonomies, meta, repository).
 */
class DiagramServiceProvider implements ServiceProvider {

	/**
	 * Register diagram services in container.
	 *
	 * @param Container $container Container instance.
	 * @return void
	 */
	public function register( Container $container ): void {
		$container->bind(
			DiagramRepository::class,
			function () {
				return new WordPressDiagramRepository();
			}
		);
	}

	/**
	 * Boot diagram hooks (CPT, taxonomies, meta registration, capabilities).
	 *
	 * @return void
	 */
	public function boot(): void {
		DiagramPostType::register();
		DiagramTaxonomies::register();
		DiagramMeta::register();
		DiagramCapabilities::assign_default_capabilities();
	}
}
