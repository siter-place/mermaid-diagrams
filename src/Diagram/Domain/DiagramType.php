<?php
/**
 * Diagram Type Value Object.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Represents detected or assigned Mermaid diagram type.
 */
readonly class DiagramType {

	public const UNKNOWN = 'unknown';

	/**
	 * Map of lowercase header tokens to canonical Mermaid type identifiers.
	 *
	 * @var array<string, string>
	 */
	private const KNOWN_TYPES = array(
		'flowchart'       => 'flowchart',
		'graph'           => 'flowchart',
		'sequencediagram' => 'sequenceDiagram',
		'classdiagram'    => 'classDiagram',
		'gantt'           => 'gantt',
		'pie'             => 'pie',
		'erdiagram'       => 'erDiagram',
		'statediagram'    => 'stateDiagram',
		'statediagram-v2' => 'stateDiagram',
		'journey'         => 'journey',
		'gitgraph'        => 'gitGraph',
		'mindmap'         => 'mindmap',
		'timeline'        => 'timeline',
		'quadrantchart'   => 'quadrantChart',
		'sankey'          => 'sankey',
		'c4context'       => 'c4Context',
		'architecture'    => 'architecture',
	);

	/**
	 * Type string.
	 *
	 * @var string
	 */
	private string $value;

	/**
	 * Constructor.
	 *
	 * @param string $type Diagram type string.
	 */
	public function __construct( string $type ) {
		$trimmed = trim( $type );
		$lowered = strtolower( $trimmed );

		$this->value = self::KNOWN_TYPES[ $lowered ] ?? ( '' !== $trimmed ? $trimmed : self::UNKNOWN );
	}

	/**
	 * Create type from string.
	 *
	 * @param string $type Type string.
	 * @return self
	 */
	public static function from_string( string $type ): self {
		return new self( $type );
	}

	/**
	 * Detect diagram type from initial source lines.
	 *
	 * @param string $source Normalized Mermaid source string.
	 * @return self
	 */
	public static function detect_from_source( string $source ): self {
		$lines = explode( "\n", $source );

		foreach ( $lines as $line ) {
			$trimmed = trim( $line );

			// Skip empty lines, frontmatter (---), or comments (%%).
			if ( '' === $trimmed || str_starts_with( $trimmed, '%%' ) || str_starts_with( $trimmed, '---' ) ) {
				continue;
			}

			$words = explode( ' ', $trimmed, 2 );
			$head  = strtolower( $words[0] );

			if ( isset( self::KNOWN_TYPES[ $head ] ) ) {
				return new self( self::KNOWN_TYPES[ $head ] );
			}

			// First non-comment line didn't match known diagram headers.
			break;
		}

		return new self( self::UNKNOWN );
	}

	/**
	 * Get diagram type value.
	 *
	 * @return string
	 */
	public function value(): string {
		return $this->value;
	}

	/**
	 * Compare equality.
	 *
	 * @param self $other Other type.
	 * @return bool
	 */
	public function equals( self $other ): bool {
		return $this->value === $other->value;
	}
}
