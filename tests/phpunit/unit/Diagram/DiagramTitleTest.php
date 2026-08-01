<?php
/**
 * Test DiagramTitle value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramTitleException;

/**
 * Class DiagramTitleTest
 */
class DiagramTitleTest extends TestCase {

	/**
	 * Test valid title trimming and value.
	 */
	public function test_valid_title(): void {
		$title = new DiagramTitle( '  Architecture Overview  ' );
		$this->assertSame( 'Architecture Overview', $title->value() );
	}

	/**
	 * Test rejection of empty title.
	 */
	public function test_rejects_empty_title(): void {
		$this->expectException( InvalidDiagramTitleException::class );
		new DiagramTitle( '   ' );
	}

	/**
	 * Test rejection of title exceeding max length.
	 */
	public function test_rejects_long_title(): void {
		$long = str_repeat( 'a', 256 );
		$this->expectException( InvalidDiagramTitleException::class );
		new DiagramTitle( $long );
	}
}
