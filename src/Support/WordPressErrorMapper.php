<?php
/**
 * WordPress Error Mapper.
 *
 * @package WebFalcon\MermaidDiagrams\Support
 */

namespace WebFalcon\MermaidDiagrams\Support;

use Throwable;
use WP_Error;
use WebFalcon\MermaidDiagrams\Diagram\Application\DTO\DiagramDetailDTO;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\EditConflictException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\InvalidBulkOperationException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\InvalidValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Application\Exception\MissingValidationReceiptException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\DiagramNotFoundException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramSourceException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramStatusException;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\InvalidDiagramTitleException;
use WebFalcon\MermaidDiagrams\Settings\Application\Exception\InvalidSettingsSectionException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps application exceptions to standard WP_Error responses with appropriate HTTP status codes.
 */
class WordPressErrorMapper {

	/**
	 * Convert an exception into a WP_Error instance.
	 *
	 * @param Throwable $exception Exception instance.
	 * @return WP_Error
	 */
	public static function to_wp_error( Throwable $exception ): WP_Error {
		if ( $exception instanceof EditConflictException ) {
			$current = DiagramDetailDTO::from_aggregate( $exception->current_diagram(), get_current_user_id() );
			return new WP_Error(
				'mdm_edit_conflict',
				$exception->getMessage(),
				array(
					'status'          => 409,
					'expectedVersion' => $exception->expected_version(),
					'currentDiagram'  => $current,
				)
			);
		}

		if (
			$exception instanceof InvalidValidationReceiptException ||
			$exception instanceof MissingValidationReceiptException
		) {
			return new WP_Error(
				'mdm_invalid_mermaid',
				$exception->getMessage(),
				array( 'status' => 422 )
			);
		}

		if ( $exception instanceof DiagramNotFoundException ) {
			return new WP_Error(
				'mdm_diagram_not_found',
				$exception->getMessage(),
				array( 'status' => 404 )
			);
		}

		if (
			$exception instanceof InvalidDiagramSourceException ||
			$exception instanceof InvalidDiagramTitleException ||
			$exception instanceof InvalidDiagramStatusException ||
			$exception instanceof InvalidBulkOperationException ||
			$exception instanceof InvalidSettingsSectionException
		) {
			return new WP_Error(
				'mdm_invalid_request',
				$exception->getMessage(),
				array( 'status' => 400 )
			);
		}

		return new WP_Error(
			'mdm_server_error',
			$exception->getMessage(),
			array( 'status' => 500 )
		);
	}
}
