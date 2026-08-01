<?php
/**
 * Update Diagram Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for updating an existing diagram with optimistic concurrency check.
 */
class UpdateDiagramCommand {

	/**
	 * Constructor.
	 *
	 * @param DiagramId              $id                 Target diagram ID.
	 * @param DiagramTitle|null      $title              Optional title update.
	 * @param DiagramSource|null     $source             Optional source update.
	 * @param DiagramDescription|null $description        Optional description update.
	 * @param DiagramStatus|null     $status             Optional status update.
	 * @param array<int>|null        $category_ids       Optional category term IDs.
	 * @param array<int>|null        $tag_ids            Optional tag term IDs.
	 * @param RenderConfig|null      $render_config      Optional render config update.
	 * @param ValidationReceipt|null $validation_receipt Optional validation receipt.
	 * @param string|null            $expected_version   Optional expected version token for 409 conflict check.
	 * @param int                    $editor_id          User ID performing update.
	 */
	public function __construct(
		private DiagramId $id,
		private ?DiagramTitle $title = null,
		private ?DiagramSource $source = null,
		private ?DiagramDescription $description = null,
		private ?DiagramStatus $status = null,
		private ?array $category_ids = null,
		private ?array $tag_ids = null,
		private ?RenderConfig $render_config = null,
		private ?ValidationReceipt $validation_receipt = null,
		private ?string $expected_version = null,
		private int $editor_id = 0,
		private string $writer_profile = 'browser'
	) {
		if ( null !== $this->category_ids ) {
			$this->category_ids = array_values( array_unique( array_map( 'intval', $this->category_ids ) ) );
		}
		if ( null !== $this->tag_ids ) {
			$this->tag_ids = array_values( array_unique( array_map( 'intval', $this->tag_ids ) ) );
		}
	}

	/**
	 * Get Diagram ID.
	 *
	 * @return DiagramId
	 */
	public function id(): DiagramId {
		return $this->id;
	}

	/**
	 * Get Title.
	 *
	 * @return DiagramTitle|null
	 */
	public function title(): ?DiagramTitle {
		return $this->title;
	}

	/**
	 * Get Source.
	 *
	 * @return DiagramSource|null
	 */
	public function source(): ?DiagramSource {
		return $this->source;
	}

	/**
	 * Get Description.
	 *
	 * @return DiagramDescription|null
	 */
	public function description(): ?DiagramDescription {
		return $this->description;
	}

	/**
	 * Get Status.
	 *
	 * @return DiagramStatus|null
	 */
	public function status(): ?DiagramStatus {
		return $this->status;
	}

	/**
	 * Get Category IDs.
	 *
	 * @return array<int>|null
	 */
	public function category_ids(): ?array {
		return $this->category_ids;
	}

	/**
	 * Get Tag IDs.
	 *
	 * @return array<int>|null
	 */
	public function tag_ids(): ?array {
		return $this->tag_ids;
	}

	/**
	 * Get RenderConfig.
	 *
	 * @return RenderConfig|null
	 */
	public function render_config(): ?RenderConfig {
		return $this->render_config;
	}

	/**
	 * Get ValidationReceipt.
	 *
	 * @return ValidationReceipt|null
	 */
	public function validation_receipt(): ?ValidationReceipt {
		return $this->validation_receipt;
	}

	/**
	 * Get Expected Version.
	 *
	 * @return string|null
	 */
	public function expected_version(): ?string {
		return $this->expected_version;
	}

	/**
	 * Get Editor User ID.
	 *
	 * @return int
	 */
	public function editor_id(): int {
		return $this->editor_id;
	}

	/**
	 * Get Writer Profile.
	 *
	 * @return string
	 */
	public function writer_profile(): string {
		return $this->writer_profile;
	}
}
