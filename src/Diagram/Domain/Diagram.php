<?php
/**
 * Diagram Aggregate Root.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Domain
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Domain;

use DateTimeImmutable;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Diagram entity and aggregate root.
 */
class Diagram {

	/**
	 * User ID of author.
	 *
	 * @var int
	 */
	private int $author_id;

	/**
	 * User ID of last editor.
	 *
	 * @var int|null
	 */
	private ?int $last_editor_id;

	/**
	 * Category term IDs.
	 *
	 * @var array<int>
	 */
	private array $category_ids;

	/**
	 * Tag term IDs.
	 *
	 * @var array<int>
	 */
	private array $tag_ids;

	/**
	 * Presentation render configuration.
	 *
	 * @var RenderConfig
	 */
	private RenderConfig $render_config;

	/**
	 * Validation receipt.
	 *
	 * @var ValidationReceipt|null
	 */
	private ?ValidationReceipt $validation_receipt;

	/**
	 * Creation timestamp.
	 *
	 * @var DateTimeImmutable|null
	 */
	private ?DateTimeImmutable $created_at;

	/**
	 * Modification timestamp.
	 *
	 * @var DateTimeImmutable|null
	 */
	private ?DateTimeImmutable $modified_at;

	/**
	 * Constructor.
	 *
	 * @param DiagramTitle           $title              Diagram title.
	 * @param DiagramSource          $source             Mermaid source code.
	 * @param DiagramId|null         $id                 Optional post ID.
	 * @param DiagramDescription|null  $description        Optional description.
	 * @param DiagramStatus|null     $status             Post status (default draft).
	 * @param DiagramType|null       $type               Detected diagram type.
	 * @param int                    $author_id          User ID of author.
	 * @param int|null               $last_editor_id     User ID of last editor.
	 * @param array<int>             $category_ids       Term IDs for categories.
	 * @param array<int>             $tag_ids            Term IDs for tags.
	 * @param RenderConfig|null      $render_config      Presentation configuration.
	 * @param DiagramVersion|null    $version            Optimistic version token.
	 * @param ValidationReceipt|null $validation_receipt Client/worker validation receipt.
	 * @param DateTimeImmutable|null $created_at          Creation timestamp.
	 * @param DateTimeImmutable|null $modified_at         Modification timestamp.
	 */
	public function __construct(
		private DiagramTitle $title,
		private DiagramSource $source,
		private ?DiagramId $id = null,
		private ?DiagramDescription $description = null,
		private ?DiagramStatus $status = null,
		private ?DiagramType $type = null,
		int $author_id = 0,
		?int $last_editor_id = null,
		array $category_ids = array(),
		array $tag_ids = array(),
		?RenderConfig $render_config = null,
		private ?DiagramVersion $version = null,
		?ValidationReceipt $validation_receipt = null,
		?DateTimeImmutable $created_at = null,
		?DateTimeImmutable $modified_at = null
	) {
		$this->description        = $description ?? new DiagramDescription();
		$this->status             = $status ?? DiagramStatus::draft();
		$this->type               = $type ?? $this->source->detect_type();
		$this->author_id          = $author_id;
		$this->last_editor_id     = $last_editor_id;
		$this->render_config      = $render_config ?? new RenderConfig();
		$this->validation_receipt = $validation_receipt;
		$this->created_at         = $created_at;
		$this->modified_at        = $modified_at;
		$this->category_ids       = array_values( array_unique( array_map( 'intval', $category_ids ) ) );
		$this->tag_ids            = array_values( array_unique( array_map( 'intval', $tag_ids ) ) );
	}

	/**
	 * Get Diagram ID.
	 *
	 * @return DiagramId|null
	 */
	public function id(): ?DiagramId {
		return $this->id;
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
	 * Get Description.
	 *
	 * @return DiagramDescription
	 */
	public function description(): DiagramDescription {
		return $this->description;
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
	 * Get computed SourceHash.
	 *
	 * @return SourceHash
	 */
	public function source_hash(): SourceHash {
		return $this->source->hash();
	}

	/**
	 * Get DiagramType.
	 *
	 * @return DiagramType
	 */
	public function type(): DiagramType {
		return $this->type;
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
	 * Get Author User ID.
	 *
	 * @return int
	 */
	public function author_id(): int {
		return $this->author_id;
	}

	/**
	 * Get Last Editor User ID.
	 *
	 * @return int|null
	 */
	public function last_editor_id(): ?int {
		return $this->last_editor_id;
	}

	/**
	 * Get Category Term IDs.
	 *
	 * @return array<int>
	 */
	public function category_ids(): array {
		return $this->category_ids;
	}

	/**
	 * Get Tag Term IDs.
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
	 * Get Version token.
	 *
	 * @return DiagramVersion|null
	 */
	public function version(): ?DiagramVersion {
		return $this->version;
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
	 * Get Created At timestamp.
	 *
	 * @return DateTimeImmutable|null
	 */
	public function created_at(): ?DateTimeImmutable {
		return $this->created_at;
	}

	/**
	 * Get Modified At timestamp.
	 *
	 * @return DateTimeImmutable|null
	 */
	public function modified_at(): ?DateTimeImmutable {
		return $this->modified_at;
	}

	/**
	 * Update source and recalculate diagram type.
	 *
	 * @param DiagramSource $source New source.
	 * @return void
	 */
	public function update_source( DiagramSource $source ): void {
		$this->source = $source;
		$this->type   = $this->source->detect_type();
	}

	/**
	 * Update title.
	 *
	 * @param DiagramTitle $title New title.
	 * @return void
	 */
	public function update_title( DiagramTitle $title ): void {
		$this->title = $title;
	}

	/**
	 * Update status.
	 *
	 * @param DiagramStatus $status New status.
	 * @return void
	 */
	public function update_status( DiagramStatus $status ): void {
		$this->status = $status;
	}

	/**
	 * Update categories.
	 *
	 * @param array<int> $category_ids Term IDs.
	 * @return void
	 */
	public function set_categories( array $category_ids ): void {
		$this->category_ids = array_values( array_unique( array_map( 'intval', $category_ids ) ) );
	}

	/**
	 * Update tags.
	 *
	 * @param array<int> $tag_ids Term IDs.
	 * @return void
	 */
	public function set_tags( array $tag_ids ): void {
		$this->tag_ids = array_values( array_unique( array_map( 'intval', $tag_ids ) ) );
	}

	/**
	 * Assign validation receipt.
	 *
	 * @param ValidationReceipt|null $receipt Receipt object.
	 * @return void
	 */
	public function set_validation_receipt( ?ValidationReceipt $receipt ): void {
		$this->validation_receipt = $receipt;
	}
}
