<?php
/**
 * Main plugin bootstrap class.
 *
 * @package WebFalcon\MermaidDiagrams\Bootstrap
 */

namespace WebFalcon\MermaidDiagrams\Bootstrap;

use WebFalcon\MermaidDiagrams\Admin\AdminServiceProvider;
use WebFalcon\MermaidDiagrams\Diagram\DiagramServiceProvider;
use WebFalcon\MermaidDiagrams\Rest\RestServiceProvider;
use WebFalcon\MermaidDiagrams\Upgrade\UpgradeServiceProvider;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Singleton bootstrap class for Mermaid Diagrams.
 */
class Plugin {

	/**
	 * Plugin version.
	 */
	public const VERSION = '1.4.1';

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * Whether the plugin has been booted.
	 *
	 * @var bool
	 */
	private bool $booted = false;

	/**
	 * DI Container.
	 *
	 * @var Container|null
	 */
	private ?Container $container = null;

	/**
	 * Service Provider Registry.
	 *
	 * @var ServiceProviderRegistry|null
	 */
	private ?ServiceProviderRegistry $registry = null;

	/**
	 * Get singleton instance.
	 *
	 * @return Plugin
	 */
	public static function instance(): Plugin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor.
	 */
	private function __construct() {}

	/**
	 * Boot the plugin services.
	 */
	public function boot(): void {
		if ( $this->booted ) {
			return;
		}

		if ( ! Compatibility::check() ) {
			return;
		}

		$this->booted = true;

		add_action( 'init', array( $this, 'on_init' ) );
	}

	/**
	 * WordPress init hook callback.
	 */
	public function on_init(): void {
		if ( null !== $this->container ) {
			return;
		}

		$this->container = new Container();
		$this->registry  = new ServiceProviderRegistry( $this->container );

		$this->registry->add_provider( new DiagramServiceProvider() );
		$this->registry->add_provider( new RestServiceProvider() );
		$this->registry->add_provider( new AdminServiceProvider() );
		$this->registry->add_provider( new UpgradeServiceProvider() );

		$this->registry->register_all();
		$this->registry->boot_all();
	}

	/**
	 * Check if plugin is booted.
	 *
	 * @return bool
	 */
	public function is_booted(): bool {
		return $this->booted;
	}

	/**
	 * Get DI container.
	 *
	 * @return Container|null
	 */
	public function container(): ?Container {
		return $this->container;
	}

	/**
	 * Get service provider registry.
	 *
	 * @return ServiceProviderRegistry|null
	 */
	public function registry(): ?ServiceProviderRegistry {
		return $this->registry;
	}
}
