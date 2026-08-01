<?php
/**
 * Test DiagramStatus value object.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramStatusException;

/**
 * Class DiagramStatusTest
 */
class DiagramStatusTest extends TestCase {

	/**
	 * Test valid statuses.
	 */
	public function test_valid_statuses(): void {
		$status = new DiagramStatus( 'PUBLISH' );
		$this->assertSame( 'publish', $status->value() );
		$this->assertTrue( $status->is_published() );

		$draft = DiagramStatus::draft();
		$this->assertTrue( $draft->is_draft() );
	}

	/**
	 * Test rejection of invalid status.
	 */
	public function test_rejects_invalid_status(): void {
		$this->expectException( InvalidDiagramStatusException::class );
		new DiagramStatus( 'unknown_status' );
	}
}
