<?php
/**
 * Diagram Repository Port Interface.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use WebFalcon\MermaidDiagrams\Diagram\Domain\Exception\DiagramNotFoundException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface for Diagram persistence storage adapters.
 */
interface DiagramRepository {

	/**
	 * Find a diagram by ID.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return Diagram|null Returns null if not found.
	 */
	public function find( DiagramId $id ): ?Diagram;

	/**
	 * Save a diagram (create or update).
	 *
	 * @param Diagram $diagram Diagram aggregate instance.
	 * @return Diagram Saved aggregate instance with updated ID, dates, and version token.
	 */
	public function save( Diagram $diagram ): Diagram;

	/**
	 * Permanently delete or trash a diagram.
	 *
	 * @param DiagramId $id    Diagram ID.
	 * @param bool      $force Whether to bypass trash and delete permanently.
	 * @return bool True on success.
	 * @throws DiagramNotFoundException If diagram does not exist.
	 */
	public function delete( DiagramId $id, bool $force = false ): bool;

	/**
	 * Move a diagram to trash.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return bool True on success.
	 * @throws DiagramNotFoundException If diagram does not exist.
	 */
	public function trash( DiagramId $id ): bool;

	/**
	 * Restore a diagram from trash.
	 *
	 * @param DiagramId $id Diagram ID.
	 * @return bool True on success.
	 * @throws DiagramNotFoundException If diagram does not exist.
	 */
	public function restore( DiagramId $id ): bool;

	/**
	 * Duplicate a diagram into a new draft copy.
	 *
	 * @param DiagramId   $id        Original diagram ID.
	 * @param string|null $new_title Optional custom title for duplicate.
	 * @return Diagram Newly created draft diagram.
	 * @throws DiagramNotFoundException If original diagram does not exist.
	 */
	public function duplicate( DiagramId $id, ?string $new_title = null ): Diagram;
}
