<?php
/**
 * Integration tests for uninstall data retention policy.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Domain\Diagram;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramSource;
use WebFalcon\MermaidDiagrams\Diagram\Domain\DiagramTitle;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\WordPressDiagramRepository;

/**
 * Class UninstallPolicyTest
 */
class UninstallPolicyTest extends TestCase {

	/**
	 * Test default preserve policy retains posts and options.
	 */
	public function test_default_preserve_policy(): void {
		DiagramPostType::register();
		$repository = new WordPressDiagramRepository();

		$diagram = $repository->save( new Diagram( new DiagramTitle( 'Preserved Diagram' ), new DiagramSource( "flowchart LR\n A-->B" ) ) );
		$this->assertNotNull( $diagram->id() );

		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', 'mermaid-diagrams/mermaid-diagrams.php' );
		}

		// Execute uninstall file in current context with admin permissions.
		wp_set_current_user( 1 );
		require MDM_PLUGIN_DIR . 'uninstall.php';

		// Verify diagram post remains intact under default preserve policy.
		$found = $repository->find( $diagram->id() );
		$this->assertNotNull( $found );
		$this->assertSame( 'Preserved Diagram', $found->title()->value() );
	}
}
