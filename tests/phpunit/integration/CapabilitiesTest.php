<?php
/**
 * Integration tests for WordPress capabilities matrix and role maps.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\WordPressDiagramRepository;

/**
 * Class CapabilitiesTest
 */
class CapabilitiesTest extends TestCase {

	/**
	 * Setup test dependencies.
	 */
	public function set_up(): void {
		parent::set_up();
		DiagramPostType::register();
		DiagramCapabilities::assign_default_capabilities();
	}

	/**
	 * Test role capability matrix for Admin, Editor, and Subscriber.
	 */
	public function test_role_capability_matrix(): void {
		$admin_user = wp_insert_user(
			array(
				'user_login' => 'test_admin_user',
				'user_pass'  => 'password',
				'role'       => 'administrator',
			)
		);

		$editor_user = wp_insert_user(
			array(
				'user_login' => 'test_editor_user',
				'user_pass'  => 'password',
				'role'       => 'editor',
			)
		);

		$subscriber_user = wp_insert_user(
			array(
				'user_login' => 'test_subscriber_user',
				'user_pass'  => 'password',
				'role'       => 'subscriber',
			)
		);

		$this->assertIsInt( $admin_user );
		$this->assertIsInt( $editor_user );
		$this->assertIsInt( $subscriber_user );

		// Administrator permissions.
		wp_set_current_user( $admin_user );
		$this->assertTrue( current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) );
		$this->assertTrue( current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ) );
		$this->assertTrue( current_user_can( DiagramCapabilities::CAP_MANAGE_TERMS ) );

		// Editor permissions.
		wp_set_current_user( $editor_user );
		$this->assertTrue( current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) );
		$this->assertTrue( current_user_can( DiagramCapabilities::CAP_MANAGE_TERMS ) );
		$this->assertFalse( current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ) );

		// Subscriber permissions.
		wp_set_current_user( $subscriber_user );
		$this->assertFalse( current_user_can( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) );
		$this->assertFalse( current_user_can( DiagramCapabilities::CAP_MANAGE_TERMS ) );
		$this->assertFalse( current_user_can( DiagramCapabilities::CAP_MANAGE_SETTINGS ) );
	}

	/**
	 * Test meta capability mapping on post edit/delete operations.
	 */
	public function test_post_meta_capability_mapping(): void {
		$repository = new WordPressDiagramRepository();

		$admin_id = wp_insert_user( array( 'user_login' => 'cap_admin', 'user_pass' => 'pass', 'role' => 'administrator' ) );
		$editor_id = wp_insert_user( array( 'user_login' => 'cap_editor', 'user_pass' => 'pass', 'role' => 'editor' ) );

		$this->assertIsInt( $admin_id );
		$this->assertIsInt( $editor_id );

		wp_set_current_user( $admin_id );
		$diagram = $repository->save( new Diagram( new DiagramTitle( 'Cap Diagram' ), new DiagramSource( "flowchart LR\n A-->B" ), null, null, null, null, $admin_id ) );

		$post_id = $diagram->id()->value();

		// Admin can edit and delete.
		$this->assertTrue( current_user_can( 'edit_post', $post_id ) );
		$this->assertTrue( current_user_can( 'delete_post', $post_id ) );

		// Editor can edit and delete others diagrams.
		wp_set_current_user( $editor_id );
		$this->assertTrue( current_user_can( 'edit_post', $post_id ) );
		$this->assertTrue( current_user_can( 'delete_post', $post_id ) );
	}
}
