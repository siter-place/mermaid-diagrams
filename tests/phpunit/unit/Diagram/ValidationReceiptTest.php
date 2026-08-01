<?php
/**
 * Test ValidationReceipt value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceHash;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

/**
 * Class ValidationReceiptTest
 */
class ValidationReceiptTest extends TestCase {

	/**
	 * Test receipt creation from array and source matching.
	 */
	public function test_receipt_from_array_and_matches_source(): void {
		$source    = new DiagramSource( "flowchart LR\n A-->B" );
		$dataArray = array(
			'sourceHash'     => $source->hash()->value(),
			'mermaidVersion' => '11.4.1',
			'diagramType'    => 'flowchart',
			'validatedAt'    => '2026-08-01T12:00:00Z',
			'profile'        => 'browser',
		);

		$receipt = ValidationReceipt::from_array( $dataArray );

		$this->assertSame( '11.4.1', $receipt->mermaid_version() );
		$this->assertSame( 'browser', $receipt->profile() );
		$this->assertTrue( $receipt->matches_source( $source ) );

		$otherSource = new DiagramSource( "flowchart TD\n X-->Y" );
		$this->assertFalse( $receipt->matches_source( $otherSource ) );
	}
}
