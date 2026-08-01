<?php
/**
 * Validation Receipt Verifier Service.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Service
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Service;

use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\InvalidValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\MissingValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\SourceConstraintsPolicy;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Verifies validation receipt validity, source binding, pinned version, and profile policy.
 */
class ValidationReceiptVerifier {

	/**
	 * Max staleness window for validation receipts in seconds (15 minutes).
	 */
	public const MAX_STALENESS_SECONDS = 900;

	/**
	 * Verify a validation receipt against a diagram source.
	 *
	 * @param ValidationReceipt|null $receipt        Validation receipt VO.
	 * @param DiagramSource          $source         Diagram source VO.
	 * @param string                 $writer_profile Writer profile ('browser', 'worker', or 'autonomous').
	 * @throws MissingValidationReceiptException If receipt is missing.
	 * @throws InvalidValidationReceiptException If receipt fails verification checks.
	 */
	public function verify( ?ValidationReceipt $receipt, DiagramSource $source, string $writer_profile = 'browser' ): void {
		if ( null === $receipt ) {
			throw new MissingValidationReceiptException();
		}

		// Check domain source constraints.
		SourceConstraintsPolicy::verify( $source );

		// Check hash match.
		if ( ! $receipt->matches_source( $source ) ) {
			throw new InvalidValidationReceiptException( 'Validation receipt source hash does not match submitted source.' );
		}

		// Check Mermaid version pin.
		if ( defined( 'MDM_MERMAID_VERSION' ) && $receipt->mermaid_version() !== MDM_MERMAID_VERSION ) {
			throw new InvalidValidationReceiptException( sprintf( 'Validation receipt version (%s) does not match pinned version (%s).', $receipt->mermaid_version(), MDM_MERMAID_VERSION ) );
		}

		// Check profile policy: autonomous writers require worker receipt.
		if ( 'autonomous' === $writer_profile && ValidationReceipt::PROFILE_WORKER !== $receipt->profile() ) {
			throw new InvalidValidationReceiptException( 'Autonomous writes require a worker validation receipt.' );
		}

		// Check staleness window if validatedAt is available.
		$validated_at = $receipt->validated_at();
		if ( ! empty( $validated_at ) ) {
			$timestamp = strtotime( $validated_at );
			if ( false !== $timestamp && ( time() - $timestamp ) > self::MAX_STALENESS_SECONDS ) {
				throw new InvalidValidationReceiptException( 'Validation receipt has expired (stale timestamp).' );
			}
		}
	}
}
