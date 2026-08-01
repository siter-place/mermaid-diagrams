<?php
/**
 * Test Diagram Aggregate Root.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;

/**
 * Class DiagramAggregateTest
 */
class DiagramAggregateTest extends TestCase {

	/**
	 * Test diagram aggregate construction and default values.
	 */
	public function test_diagram_aggregate_defaults(): void {
		$title   = new DiagramTitle( 'System Architecture' );
		$source  = new DiagramSource( "flowchart TD\n App-->DB" );
		$diagram = new Diagram( $title, $source );

		$this->assertNull( $diagram->id() );
		$this->assertSame( 'System Architecture', $diagram->title()->value() );
		$this->assertSame( "flowchart TD\n App-->DB", $diagram->source()->value() );
		$this->assertSame( 'flowchart', $diagram->type()->value() );
		$this->assertTrue( $diagram->status()->is_draft() );
		$this->assertSame( array(), $diagram->category_ids() );
		$this->assertSame( array(), $diagram->tag_ids() );
	}

	/**
	 * Test set_categories and set_tags sanitizes and deduplicates IDs.
	 */
	public function test_category_and_tag_sanitization(): void {
		$title   = new DiagramTitle( 'Test' );
		$source  = new DiagramSource( "flowchart LR\n A-->B" );
		$diagram = new Diagram( $title, $source );

		$diagram->set_categories( array( 10, '10', 20, 0, -5 ) );
		$diagram->set_tags( array( 5, '15', 5 ) );

		$this->assertSame( array( 10, 20, 0, -5 ), $diagram->category_ids() );
		$this->assertSame( array( 5, 15 ), $diagram->tag_ids() );
	}
}
