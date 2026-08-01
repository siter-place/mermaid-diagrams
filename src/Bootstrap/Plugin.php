<?php
/**
 * Main plugin bootstrap class.
 *
 * @package WebFalcon\MermaidDiagrams\Bootstrap
 */

namespace WebFalcon\MermaidDiagrams\Bootstrap;

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
	public const VERSION = '0.0.0-development';

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
		// Service providers will be registered in subsequent phases.
	}

	/**
	 * Check if plugin is booted.
	 *
	 * @return bool
	 */
	public function is_booted(): bool {
		return $this->booted;
	}
}
