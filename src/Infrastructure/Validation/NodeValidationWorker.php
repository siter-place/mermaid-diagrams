<?php
/**
 * Node Validation Worker Adapter.
 *
 * @package WebFalcon\MermaidDiagrams\Infrastructure\Validation
 */

namespace WebFalcon\MermaidDiagrams\Infrastructure\Validation;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Executes local Node validation worker subprocess.
 */
class NodeValidationWorker {

	/**
	 * Path to worker script relative to plugin root.
	 */
	public const WORKER_SCRIPT = 'tools/validation-worker/validate.mjs';

	/**
	 * Validate a Mermaid source string using the Node validation worker.
	 *
	 * @param string $source  Raw Mermaid source code.
	 * @param string $profile Validation profile ('worker' or 'browser').
	 * @return array{
	 *     valid: bool,
	 *     diagramType?: string,
	 *     sourceHash?: string,
	 *     mermaidVersion?: string,
	 *     validatedAt?: string,
	 *     profile?: string,
	 *     error?: string,
	 *     diagnostics?: array
	 * }
	 */
	public function validate( string $source, string $profile = 'worker' ): array {
		$script_path = MDM_PLUGIN_DIR . self::WORKER_SCRIPT;
		if ( ! file_exists( $script_path ) ) {
			return array(
				'valid' => false,
				'error' => 'Validation worker script not found.',
			);
		}

		$input   = wp_json_encode(
			array(
				'source'  => $source,
				'profile' => $profile,
			)
		);
		$command = sprintf( 'node %s', escapeshellarg( $script_path ) );

		$descriptors = array(
			0 => array( 'pipe', 'r' ),
			1 => array( 'pipe', 'w' ),
			2 => array( 'pipe', 'w' ),
		);

		$process = proc_open( $command, $descriptors, $pipes );
		if ( ! is_resource( $process ) ) {
			return array(
				'valid' => false,
				'error' => 'Failed to spawn Node validation worker process.',
			);
		}

		fwrite( $pipes[0], (string) $input );
		fclose( $pipes[0] );

		$stdout = stream_get_contents( $pipes[1] );
		fclose( $pipes[1] );

		$stderr = stream_get_contents( $pipes[2] );
		fclose( $pipes[2] );

		proc_close( $process );

		$decoded = json_decode( (string) $stdout, true );
		if ( is_array( $decoded ) ) {
			return $decoded;
		}

		return array(
			'valid' => false,
			'error' => ! empty( $stderr ) ? trim( (string) $stderr ) : 'Validation worker returned invalid JSON.',
		);
	}
}
