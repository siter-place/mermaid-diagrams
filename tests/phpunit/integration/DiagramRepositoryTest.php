<?php
/**
 * Integration tests for WordPressDiagramRepository.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramId;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramStatus;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\WordPressDiagramRepository;

/**
 * Class DiagramRepositoryTest
 */
class DiagramRepositoryTest extends TestCase {

	private WordPressDiagramRepository $repository;

	/**
	 * Setup test dependencies.
	 */
	public function set_up(): void {
		parent::set_up();
		DiagramPostType::register();
		DiagramTaxonomies::register();
		$this->repository = new WordPressDiagramRepository();
	}

	/**
	 * Test create, find, update, and persist workflow.
	 */
	public function test_create_and_find_diagram(): void {
		$fixture_path = MDM_PLUGIN_DIR . 'tests/fixtures/diagrams/valid-flowchart.mmd';
		$source_code  = file_get_contents( $fixture_path );

		$title   = new DiagramTitle( 'Test Flowchart' );
		$source  = new DiagramSource( $source_code );
		$diagram = new Diagram( $title, $source );

		$saved = $this->repository->save( $diagram );

		$this->assertNotNull( $saved->id() );
		$this->assertSame( 'Test Flowchart', $saved->title()->value() );
		$this->assertSame( 'flowchart', $saved->type()->value() );
		$this->assertNotNull( $saved->version() );

		// Fetch back.
		$found = $this->repository->find( $saved->id() );
		$this->assertNotNull( $found );
		$this->assertSame( $saved->id()->value(), $found->id()->value() );
		$this->assertSame( 'Test Flowchart', $found->title()->value() );
	}

	/**
	 * Test taxonomy category and tag term assignment.
	 */
	public function test_category_and_tag_terms(): void {
		$cat_term = wp_insert_term( 'Architecture', DiagramTaxonomies::TAXONOMY_CATEGORY );
		$tag_term = wp_insert_term( 'System', DiagramTaxonomies::TAXONOMY_TAG );

		$this->assertIsArray( $cat_term );
		$this->assertIsArray( $tag_term );

		$cat_id = (int) $cat_term['term_id'];
		$tag_id = (int) $tag_term['term_id'];

		$title   = new DiagramTitle( 'Term Test' );
		$source  = new DiagramSource( "flowchart LR\n A-->B" );
		$diagram = new Diagram( $title, $source, null, null, null, null, 0, null, array( $cat_id ), array( $tag_id ) );

		$saved = $this->repository->save( $diagram );

		$this->assertSame( array( $cat_id ), $saved->category_ids() );
		$this->assertSame( array( $tag_id ), $saved->tag_ids() );

		$found = $this->repository->find( $saved->id() );
		$this->assertNotNull( $found );
		$this->assertSame( array( $cat_id ), $found->category_ids() );
		$this->assertSame( array( $tag_id ), $found->tag_ids() );
	}

	/**
	 * Test revision creation on source update.
	 */
	public function test_revisions_on_source_update(): void {
		$title   = new DiagramTitle( 'Revision Test' );
		$source1 = new DiagramSource( "flowchart LR\n A-->B" );
		$diagram = new Diagram( $title, $source1, null, null, DiagramStatus::from_string( 'publish' ) );

		$saved = $this->repository->save( $diagram );

		// Update source.
		$source2 = new DiagramSource( "flowchart LR\n A-->C" );
		$saved->update_source( $source2 );

		$updated = $this->repository->save( $saved );
		$this->assertSame( "flowchart LR\n A-->C", $updated->source()->value() );

		$revisions = wp_get_post_revisions( $updated->id()->value() );
		$this->assertNotEmpty( $revisions );
	}

	/**
	 * Test trash, restore, and delete operations.
	 */
	public function test_trash_restore_delete(): void {
		$title   = new DiagramTitle( 'Trash Test' );
		$source  = new DiagramSource( "flowchart LR\n A-->B" );
		$diagram = new Diagram( $title, $source );

		$saved = $this->repository->save( $diagram );
		$id    = $saved->id();

		// Trash.
		$this->assertTrue( $this->repository->trash( $id ) );
		$trashed = $this->repository->find( $id );
		$this->assertNotNull( $trashed );
		$this->assertTrue( $trashed->status()->is_trashed() );

		// Restore.
		$this->assertTrue( $this->repository->restore( $id ) );
		$restored = $this->repository->find( $id );
		$this->assertNotNull( $restored );
		$this->assertFalse( $restored->status()->is_trashed() );

		// Delete permanently.
		$this->assertTrue( $this->repository->delete( $id, true ) );
		$this->assertNull( $this->repository->find( $id ) );
	}

	/**
	 * Test duplicate diagram creates new draft copy.
	 */
	public function test_duplicate_diagram(): void {
		$title    = new DiagramTitle( 'Original Diagram' );
		$source   = new DiagramSource( "flowchart LR\n A-->B" );
		$original = $this->repository->save( new Diagram( $title, $source ) );

		$copy = $this->repository->duplicate( $original->id(), 'Custom Copy Title' );

		$this->assertNotEquals( $original->id()->value(), $copy->id()->value() );
		$this->assertSame( 'Custom Copy Title', $copy->title()->value() );
		$this->assertTrue( $copy->status()->is_draft() );
		$this->assertSame( $original->source()->value(), $copy->source()->value() );
	}
}
