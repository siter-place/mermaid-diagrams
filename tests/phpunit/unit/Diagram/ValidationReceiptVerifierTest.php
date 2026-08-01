<?php
/**
 * Validation Receipt Verifier Test.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Unit\Diagram
 */

namespace WebFalcon\MermaidDiagrams\Tests\Unit\Diagram;

use PHPUnit\Framework\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\InvalidValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\MissingValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Service\ValidationReceiptVerifier;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramType;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceHash;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

/**
 * Unit tests for ValidationReceiptVerifier.
 */
class ValidationReceiptVerifierTest extends TestCase {

	private ValidationReceiptVerifier $verifier;

	protected function setUp(): void {
		parent::setUp();
		$this->verifier = new ValidationReceiptVerifier();
	}

	public function test_throws_missing_exception_when_receipt_is_null(): void {
		$this->expectException( MissingValidationReceiptException::class );
		$source = new DiagramSource( "flowchart LR\n  A --> B" );
		$this->verifier->verify( null, $source );
	}

	public function test_passes_when_receipt_is_valid(): void {
		$source  = new DiagramSource( "flowchart LR\n  A --> B" );
		$hash    = SourceHash::from_source( $source );
		$receipt = new ValidationReceipt(
			$hash,
			'11.4.1',
			DiagramType::flowchart(),
			gmdate( 'Y-m-d\TH:i:s\Z' ),
			ValidationReceipt::PROFILE_BROWSER
		);

		$this->verifier->verify( $receipt, $source, 'browser' );
		$this->assertTrue( true );
	}

	public function test_throws_exception_on_hash_mismatch(): void {
		$source      = new DiagramSource( "flowchart LR\n  A --> B" );
		$diff_source = new DiagramSource( "flowchart LR\n  X --> Y" );
		$hash        = SourceHash::from_source( $diff_source );
		$receipt     = new ValidationReceipt(
			$hash,
			'11.4.1',
			DiagramType::flowchart(),
			gmdate( 'Y-m-d\TH:i:s\Z' ),
			ValidationReceipt::PROFILE_BROWSER
		);

		$this->expectException( InvalidValidationReceiptException::class );
		$this->verifier->verify( $receipt, $source );
	}

	public function test_throws_exception_on_version_mismatch(): void {
		$source  = new DiagramSource( "flowchart LR\n  A --> B" );
		$hash    = SourceHash::from_source( $source );
		$receipt = new ValidationReceipt(
			$hash,
			'10.9.0',
			DiagramType::flowchart(),
			gmdate( 'Y-m-d\TH:i:s\Z' ),
			ValidationReceipt::PROFILE_BROWSER
		);

		$this->expectException( InvalidValidationReceiptException::class );
		$this->verifier->verify( $receipt, $source );
	}

	public function test_throws_exception_when_autonomous_profile_lacks_worker_receipt(): void {
		$source  = new DiagramSource( "flowchart LR\n  A --> B" );
		$hash    = SourceHash::from_source( $source );
		$receipt = new ValidationReceipt(
			$hash,
			'11.4.1',
			DiagramType::flowchart(),
			gmdate( 'Y-m-d\TH:i:s\Z' ),
			ValidationReceipt::PROFILE_BROWSER
		);

		$this->expectException( InvalidValidationReceiptException::class );
		$this->verifier->verify( $receipt, $source, 'autonomous' );
	}

	public function test_throws_exception_when_receipt_is_stale(): void {
		$source  = new DiagramSource( "flowchart LR\n  A --> B" );
		$hash    = SourceHash::from_source( $source );
		$stale   = gmdate( 'Y-m-d\TH:i:s\Z', time() - 3600 ); // 1 hour ago
		$receipt = new ValidationReceipt(
			$hash,
			'11.4.1',
			DiagramType::flowchart(),
			$stale,
			ValidationReceipt::PROFILE_BROWSER
		);

		$this->expectException( InvalidValidationReceiptException::class );
		$this->verifier->verify( $receipt, $source );
	}
}
