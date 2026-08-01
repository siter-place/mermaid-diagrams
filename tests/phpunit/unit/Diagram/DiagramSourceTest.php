<?php
/**
 * Test DiagramSource value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramSourceException;

/**
 * Class DiagramSourceTest
 */
class DiagramSourceTest extends TestCase {

	/**
	 * Test line ending normalization to LF.
	 */
	public function test_normalizes_line_endings(): void {
		$raw    = "flowchart LR\r\n    A-->B\r    C-->D";
		$source = new DiagramSource( $raw );

		$this->assertSame( "flowchart LR\n    A-->B\n    C-->D", $source->value() );
	}

	/**
	 * Test rejection of null bytes.
	 */
	public function test_rejects_null_bytes(): void {
		$this->expectException( InvalidDiagramSourceException::class );
		new DiagramSource( "flowchart LR\n A\0-->B" );
	}

	/**
	 * Test SHA-256 hash generation.
	 */
	public function test_generates_source_hash(): void {
		$source = new DiagramSource( "flowchart LR\n A-->B" );
		$hash   = $source->hash();

		$this->assertStringStartsWith( 'sha256:', $hash->value() );
		$this->assertSame( 'sha256:' . hash( 'sha256', "flowchart LR\n A-->B" ), $hash->value() );
	}

	/**
	 * Test equality comparison.
	 */
	public function test_equals(): void {
		$s1 = new DiagramSource( "flowchart LR\n A-->B" );
		$s2 = new DiagramSource( "flowchart LR\r\n A-->B" );
		$s3 = new DiagramSource( "flowchart TD\n A-->B" );

		$this->assertTrue( $s1->equals( $s2 ) );
		$this->assertFalse( $s1->equals( $s3 ) );
	}
}
