<?php
/**
 * Validation Receipt Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Encapsulates client/worker validation proof.
 */
readonly class ValidationReceipt {

	public const PROFILE_BROWSER = 'browser';
	public const PROFILE_WORKER  = 'worker';

	/**
	 * Constructor.
	 *
	 * @param SourceHash  $source_hash     SHA-256 hash of validated source.
	 * @param string      $mermaid_version Pinned Mermaid JS version string.
	 * @param DiagramType $diagram_type    Detected diagram type.
	 * @param string      $validated_at    ISO 8601 UTC timestamp.
	 * @param string      $profile         Validation profile ('browser' or 'worker').
	 * @throws InvalidArgumentException If profile is invalid.
	 */
	public function __construct(
		private SourceHash $source_hash,
		private string $mermaid_version,
		private DiagramType $diagram_type,
		private string $validated_at,
		private string $profile = self::PROFILE_BROWSER
	) {
		if ( ! in_array( $this->profile, array( self::PROFILE_BROWSER, self::PROFILE_WORKER ), true ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidArgumentException( 'Validation profile must be "browser" or "worker".' );
		}
	}

	/**
	 * Create from associative array payload.
	 *
	 * @param array{
	 *     sourceHash: string,
	 *     mermaidVersion: string,
	 *     diagramType: string,
	 *     validatedAt: string,
	 *     profile?: string
	 * } $data Payload array.
	 * @return self
	 */
	public static function from_array( array $data ): self {
		return new self(
			SourceHash::from_string( $data['sourceHash'] ),
			$data['mermaidVersion'],
			DiagramType::from_string( $data['diagramType'] ),
			$data['validatedAt'],
			$data['profile'] ?? self::PROFILE_BROWSER
		);
	}

	/**
	 * Convert receipt to array.
	 *
	 * @return array{
	 *     sourceHash: string,
	 *     mermaidVersion: string,
	 *     diagramType: string,
	 *     validatedAt: string,
	 *     profile: string
	 * }
	 */
	public function to_array(): array {
		return array(
			'sourceHash'     => $this->source_hash->value(),
			'mermaidVersion' => $this->mermaid_version,
			'diagramType'    => $this->diagram_type->value(),
			'validatedAt'    => $this->validated_at,
			'profile'        => $this->profile,
		);
	}

	/**
	 * Get source hash.
	 *
	 * @return SourceHash
	 */
	public function source_hash(): SourceHash {
		return $this->source_hash;
	}

	/**
	 * Get Mermaid version.
	 *
	 * @return string
	 */
	public function mermaid_version(): string {
		return $this->mermaid_version;
	}

	/**
	 * Get diagram type.
	 *
	 * @return DiagramType
	 */
	public function diagram_type(): DiagramType {
		return $this->diagram_type;
	}

	/**
	 * Get validation timestamp.
	 *
	 * @return string
	 */
	public function validated_at(): string {
		return $this->validated_at;
	}

	/**
	 * Get profile.
	 *
	 * @return string
	 */
	public function profile(): string {
		return $this->profile;
	}

	/**
	 * Verify receipt matches a given source object.
	 *
	 * @param DiagramSource $source Diagram source.
	 * @return bool
	 */
	public function matches_source( DiagramSource $source ): bool {
		return $this->source_hash->equals( $source->hash() );
	}
}
