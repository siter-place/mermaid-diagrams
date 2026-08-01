<?php
/**
 * Diagram Version Token Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents an opaque version token for optimistic concurrency.
 */
readonly class DiagramVersion {

	/**
	 * Opaque token string.
	 *
	 * @var string
	 */
	private string $token;

	/**
	 * Constructor.
	 *
	 * @param string $token Opaque version token.
	 * @throws InvalidArgumentException If token is empty.
	 */
	public function __construct( string $token ) {
		$trimmed = trim( $token );
		if ( '' === $trimmed ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			throw new InvalidArgumentException( 'Version token cannot be empty.' );
		}
		$this->token = $trimmed;
	}

	/**
	 * Create version token from existing token string.
	 *
	 * @param string $token Token string.
	 * @return self
	 */
	public static function from_string( string $token ): self {
		return new self( $token );
	}

	/**
	 * Generate an opaque version token for a diagram record state.
	 *
	 * @param int        $post_id           Post ID.
	 * @param string     $post_modified_gmt GMT modified timestamp string.
	 * @param SourceHash $source_hash       Source hash.
	 * @param int        $revision_id       Latest revision ID.
	 * @param string     $secret_key        Secret key for HMAC generation.
	 * @return self
	 */
	public static function generate(
		int $post_id,
		string $post_modified_gmt,
		SourceHash $source_hash,
		int $revision_id,
		string $secret_key = 'mdm_version_secret'
	): self {
		$payload  = sprintf( '%d|%s|%s|%d', $post_id, $post_modified_gmt, $source_hash->value(), $revision_id );
		$raw_hmac = hash_hmac( 'sha256', $payload, $secret_key, true );
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$token = rtrim( strtr( base64_encode( $raw_hmac ), '+/', '-_' ), '=' );

		return new self( $token );
	}

	/**
	 * Get token value.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->token;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other version object.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return hash_equals( $this->token, $other->token );
	}
}
