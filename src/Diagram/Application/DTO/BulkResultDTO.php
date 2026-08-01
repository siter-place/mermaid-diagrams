<?php
/**
 * Bulk Result DTO for REST itemized operation outputs.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\DTO
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\DTO;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Data Transfer Object for reporting bulk operation itemized outcomes.
 */
class BulkResultDTO {

	/**
	 * Create structured bulk output payload.
	 *
	 * @param array<int, array{id: int, ok: bool, error?: array{code: string, message: string}}> $results Item outcomes.
	 * @return array{results: array, summary: array{requested: int, succeeded: int, failed: int}}
	 */
	public static function format( array $results ): array {
		$succeeded = 0;
		$failed    = 0;

		foreach ( $results as $item ) {
			if ( ! empty( $item['ok'] ) ) {
				++$succeeded;
			} else {
				++$failed;
			}
		}

		return array(
			'results' => array_values( $results ),
			'summary' => array(
				'requested' => count( $results ),
				'succeeded' => $succeeded,
				'failed'    => $failed,
			),
		);
	}
}
