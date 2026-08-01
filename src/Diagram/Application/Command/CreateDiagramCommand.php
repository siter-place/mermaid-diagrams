<?php
/**
 * Create Diagram Command DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Command
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Command;

use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramDescription;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Domain\RenderConfig;
use WebFalcon\MermaidDiagrams\Diagram\Domain\ValidationReceipt;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Command for creating a new diagram.
 */
class CreateDiagramCommand {

	/**
	 * Constructor.
	 *
	 * @param DiagramTitle           $title              Diagram title.
	 * @param DiagramSource          $source             Mermaid source code.
	 * @param DiagramDescription|null  $description        Optional description.
	 * @param DiagramStatus|null     $status             Post status.
	 * @param array<int>             $category_ids       Category term IDs.
	 * @param array<int>             $tag_ids            Tag term IDs.
	 * @param RenderConfig|null      $render_config      Render configuration.
	 * @param ValidationReceipt|null $validation_receipt Optional validation receipt.
	 * @param string|null            $idempotency_key    Optional client idempotency key.
	 * @param int                    $author_id          User ID of author.
	 */
	public function __construct(
		private DiagramTitle $title,
		private DiagramSource $source,
		private ?DiagramDescription $description = null,
		private ?DiagramStatus $status = null,
		private array $category_ids = array(),
		private array $tag_ids = array(),
		private ?RenderConfig $render_config = null,
		private ?ValidationReceipt $validation_receipt = null,
		private ?string $idempotency_key = null,
		private int $author_id = 0,
		private string $writer_profile = 'browser'
	) {
		$this->description   = $description ?? new DiagramDescription();
		$this->status        = $status ?? DiagramStatus::draft();
		$this->render_config = $render_config ?? new RenderConfig();
		$this->category_ids  = array_values( array_unique( array_map( 'intval', $category_ids ) ) );
		$this->tag_ids       = array_values( array_unique( array_map( 'intval', $tag_ids ) ) );
	}

	/**
	 * Get Title.
	 *
	 * @return DiagramTitle
	 */
	public function title(): DiagramTitle {
		return $this->title;
	}

	/**
	 * Get Source.
	 *
	 * @return DiagramSource
	 */
	public function source(): DiagramSource {
		return $this->source;
	}

	/**
	 * Get Description.
	 *
	 * @return DiagramDescription
	 */
	public function description(): DiagramDescription {
		return $this->description;
	}

	/**
	 * Get Status.
	 *
	 * @return DiagramStatus
	 */
	public function status(): DiagramStatus {
		return $this->status;
	}

	/**
	 * Get Category IDs.
	 *
	 * @return array<int>
	 */
	public function category_ids(): array {
		return $this->category_ids;
	}

	/**
	 * Get Tag IDs.
	 *
	 * @return array<int>
	 */
	public function tag_ids(): array {
		return $this->tag_ids;
	}

	/**
	 * Get RenderConfig.
	 *
	 * @return RenderConfig
	 */
	public function render_config(): RenderConfig {
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
	 * Get Idempotency Key.
	 *
	 * @return string|null
	 */
	public function idempotency_key(): ?string {
		return $this->idempotency_key;
	}

	/**
	 * Get Author User ID.
	 *
	 * @return int
	 */
	public function author_id(): int {
		return $this->author_id;
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
