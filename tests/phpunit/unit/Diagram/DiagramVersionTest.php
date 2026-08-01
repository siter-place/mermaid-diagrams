<?php
/**
 * Test DiagramVersion value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramVersion;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceHash;

/**
 * Class DiagramVersionTest
 */
class DiagramVersionTest extends TestCase {

	/**
	 * Test generation and equality of version tokens.
	 */
	public function test_generate_and_equals(): void {
		$hash = SourceHash::from_source( "flowchart LR\n A-->B" );
		$v1   = DiagramVersion::generate( 123, '2026-08-01 12:00:00', $hash, 456, 'secret' );
		$v2   = DiagramVersion::generate( 123, '2026-08-01 12:00:00', $hash, 456, 'secret' );
		$v3   = DiagramVersion::generate( 123, '2026-08-01 12:00:01', $hash, 456, 'secret' );

		$this->assertNotEmpty( $v1->value() );
		$this->assertTrue( $v1->equals( $v2 ) );
		$this->assertFalse( $v1->equals( $v3 ) );
	}
}
