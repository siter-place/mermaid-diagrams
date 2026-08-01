<?php
/**
 * Source Constraints Policy Test.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramSourceException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceConstraintsPolicy;

/**
 * Unit tests for SourceConstraintsPolicy.
 */
class SourceConstraintsPolicyTest extends TestCase {

	public function test_valid_source_passes_verification(): void {
		$source = new DiagramSource( "flowchart LR\n  A --> B" );
		SourceConstraintsPolicy::verify( $source );
		$this->assertTrue( true );
	}

	public function test_rejects_click_directive(): void {
		$source = new DiagramSource( "flowchart LR\n  A --> B\n  click A call alert()" );
		$this->expectException( InvalidDiagramSourceException::class );
		SourceConstraintsPolicy::verify( $source );
	}

	public function test_rejects_security_level_directive(): void {
		$source = new DiagramSource( "%%{init: {\"securityLevel\": \"loose\"}}%%\nflowchart LR\n  A --> B" );
		$this->expectException( InvalidDiagramSourceException::class );
		SourceConstraintsPolicy::verify( $source );
	}

	public function test_rejects_inline_script_tags(): void {
		$source = new DiagramSource( "flowchart LR\n  A[\"<script>alert(1)</script>\"] --> B" );
		$this->expectException( InvalidDiagramSourceException::class );
		SourceConstraintsPolicy::verify( $source );
	}

	public function test_rejects_non_strict_render_config_security_level(): void {
		$source = new DiagramSource( "flowchart LR\n  A --> B" );
		$config = new RenderConfig( array( 'securityLevel' => 'loose' ) );
		$this->expectException( InvalidDiagramSourceException::class );
		SourceConstraintsPolicy::verify( $source, $config );
	}
}
