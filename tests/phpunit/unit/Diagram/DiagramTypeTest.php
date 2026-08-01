<?php
/**
 * Test DiagramType value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramType;

/**
 * Class DiagramTypeTest
 */
class DiagramTypeTest extends TestCase {

	/**
	 * Test type detection from source headers.
	 */
	public function test_detects_diagram_types(): void {
		$this->assertSame( 'flowchart', DiagramType::detect_from_source( "flowchart TD\n A-->B" )->value() );
		$this->assertSame( 'flowchart', DiagramType::detect_from_source( "graph LR\n A-->B" )->value() );
		$this->assertSame( 'sequenceDiagram', DiagramType::detect_from_source( "sequenceDiagram\n Alice->>Bob: Hi" )->value() );
		$this->assertSame( 'classDiagram', DiagramType::detect_from_source( "classDiagram\n Class01 <|-- Class02" )->value() );
		$this->assertSame( 'gantt', DiagramType::detect_from_source( "gantt\n title A Gantt" )->value() );
		$this->assertSame( 'unknown', DiagramType::detect_from_source( "random text" )->value() );
	}

	/**
	 * Test handling of frontmatter or comments before diagram header.
	 */
	public function test_detects_type_with_leading_comments(): void {
		$source = "%%\n%% Diagram comment\n%%\nflowchart LR\n A-->B";
		$this->assertSame( 'flowchart', DiagramType::detect_from_source( $source )->value() );
	}
}
