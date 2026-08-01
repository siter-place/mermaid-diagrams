<?php
/**
 * Render Configuration Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encapsulates safe presentation/render configuration settings.
 */
readonly class RenderConfig {

	/**
	 * Configuration array.
	 *
	 * @var array<string, mixed>
	 */
	private array $config;

	/**
	 * Constructor.
	 *
	 * @param array<string, mixed> $config Configuration settings.
	 */
	public function __construct( array $config = array() ) {
		$defaults = array(
			'theme'               => 'default',
			'showToolbar'         => true,
			'allowSourceDownload' => true,
			'allowSvgDownload'    => true,
		);

		$this->config = array_merge( $defaults, $config );
	}

	/**
	 * Create from array.
	 *
	 * @param array<string, mixed> $config Array config.
	 * @return self
	 */
	public static function from_array( array $config ): self {
		return new self( $config );
	}

	/**
	 * Get configuration array.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return $this->config;
	}

	/**
	 * Get specific setting value with fallback.
	 *
	 * @param string $key      Setting key.
	 * @param mixed  $fallback Fallback value.
	 * @return mixed
	 */
	public function get( string $key, mixed $fallback = null ): mixed {
		return $this->config[ $key ] ?? $fallback;
	}
}
