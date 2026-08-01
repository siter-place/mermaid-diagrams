<?php
/**
 * Source Constraints Policy.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramSourceException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates domain source constraints and forbidden directive patterns.
 */
class SourceConstraintsPolicy {

	/**
	 * Verify that source code passes all constraint rules.
	 *
	 * @param DiagramSource     $source        Diagram source.
	 * @param RenderConfig|null $render_config Render configuration.
	 * @throws InvalidDiagramSourceException If constraints are violated.
	 */
	public static function verify( DiagramSource $source, ?RenderConfig $render_config = null ): void {
		$raw = $source->value();

		// Check for forbidden click or callback directives.
		if ( preg_match( '/^\s*(click|callback)\s+[A-Za-z0-9_-]+/im', $raw ) ) {
			throw new InvalidDiagramSourceException( 'Author-defined click and callback directives are forbidden.' );
		}

		// Check for securityLevel override attempt in init directive or frontmatter.
		if ( preg_match( '/securityLevel/i', $raw ) ) {
			throw new InvalidDiagramSourceException( 'Custom securityLevel overrides in Mermaid source are forbidden.' );
		}

		// Check for inline script tags.
		if ( preg_match( '/<script[\s\S]*?>/i', $raw ) ) {
			throw new InvalidDiagramSourceException( 'Inline script tags in Mermaid source are forbidden.' );
		}

		if ( null !== $render_config ) {
			$config = $render_config->to_array();
			if ( isset( $config['securityLevel'] ) && 'strict' !== $config['securityLevel'] ) {
				throw new InvalidDiagramSourceException( 'Rendering securityLevel must be "strict".' );
			}
		}
	}
}
