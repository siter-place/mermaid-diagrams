<?php
/**
 * Search Diagrams Query DTO.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Application\Query
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Application\Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Query for searching, filtering, sorting, and paginating diagrams.
 */
class SearchDiagramsQuery {

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
	 * Diagram types filter.
	 *
	 * @var array<string>
	 */
	private array $types;

	/**
	 * Statuses filter.
	 *
	 * @var array<string>
	 */
	private array $statuses;

	/**
	 * Author user IDs filter.
	 *
	 * @var array<int>
	 */
	private array $authors;

	/**
	 * Constructor.
	 *
	 * @param string|null   $search       Search string.
	 * @param array<int>    $category_ids Category term IDs.
	 * @param array<int>    $tag_ids      Tag term IDs.
	 * @param array<string> $types        Diagram types filter.
	 * @param array<string> $statuses     Statuses filter.
	 * @param array<int>    $authors      Author user IDs filter.
	 * @param int           $page         Page number.
	 * @param int           $per_page     Page size limit.
	 * @param string        $orderby      Sort field.
	 * @param string        $order        Sort direction (ASC/DESC).
	 * @param string        $view         View format (summary, selector).
	 */
	public function __construct(
		private ?string $search = null,
		array $category_ids = array(),
		array $tag_ids = array(),
		array $types = array(),
		array $statuses = array(),
		array $authors = array(),
		private int $page = 1,
		private int $per_page = 20,
		private string $orderby = 'modified',
		private string $order = 'DESC',
		private string $view = 'summary'
	) {
		$this->category_ids = array_values( array_unique( array_map( 'intval', $category_ids ) ) );
		$this->tag_ids      = array_values( array_unique( array_map( 'intval', $tag_ids ) ) );
		$this->types        = array_values( array_unique( array_filter( $types ) ) );
		$this->statuses     = array_values( array_unique( array_filter( $statuses ) ) );
		$this->authors      = array_values( array_unique( array_map( 'intval', $authors ) ) );
		$this->page         = max( 1, $page );
		$this->per_page     = min( 100, max( 1, $per_page ) );
		$this->order        = 'ASC' === strtoupper( $order ) ? 'ASC' : 'DESC';
	}

	/**
	 * Get Search string.
	 *
	 * @return string|null
	 */
	public function search(): ?string {
		return $this->search;
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
	 * Get Types filter.
	 *
	 * @return array<string>
	 */
	public function types(): array {
		return $this->types;
	}

	/**
	 * Get Statuses filter.
	 *
	 * @return array<string>
	 */
	public function statuses(): array {
		return $this->statuses;
	}

	/**
	 * Get Authors filter.
	 *
	 * @return array<int>
	 */
	public function authors(): array {
		return $this->authors;
	}

	/**
	 * Get Page.
	 *
	 * @return int
	 */
	public function page(): int {
		return $this->page;
	}

	/**
	 * Get Per Page limit.
	 *
	 * @return int
	 */
	public function per_page(): int {
		return $this->per_page;
	}

	/**
	 * Get Orderby field.
	 *
	 * @return string
	 */
	public function orderby(): string {
		return $this->orderby;
	}

	/**
	 * Get Order direction.
	 *
	 * @return string
	 */
	public function order(): string {
		return $this->order;
	}

	/**
	 * Get View mode.
	 *
	 * @return string
	 */
	public function view(): string {
		return $this->view;
	}
}
