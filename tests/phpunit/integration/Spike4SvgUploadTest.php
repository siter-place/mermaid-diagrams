<?php
/**
 * Integration test for Spike 4: Controlled SVG Media Upload as Featured Image.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Class Spike4SvgUploadTest
 */
class Spike4SvgUploadTest extends TestCase {

	/**
	 * Test SVG MIME type filter and attachment creation as featured image.
	 */
	public function test_svg_upload_and_featured_image(): void {
		// Enable SVG MIME type for test.
		add_filter(
			'upload_mimes',
			function ( $mimes ) {
				$mimes['svg'] = 'image/svg+xml';
				return $mimes;
			}
		);

		$svg_content = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"><rect width="100" height="100" fill="blue"/></svg>';
		$upload      = wp_upload_bits( 'test-mermaid-diagram.svg', null, $svg_content );

		$this->assertFalse( $upload['error'] );
		$this->assertFileExists( $upload['file'] );

		$attachment = array(
			'post_mime_type' => 'image/svg+xml',
			'post_title'     => 'Test Mermaid SVG Diagram',
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		$this->assertGreaterThan( 0, $attach_id );

		// Create a sample post and set SVG as thumbnail.
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'Sample Post with Mermaid SVG',
				'post_status'  => 'publish',
				'post_type'    => 'post',
			)
		);

		set_post_thumbnail( $post_id, $attach_id );

		$retrieved_thumbnail_id = get_post_thumbnail_id( $post_id );
		$this->assertEquals( $attach_id, $retrieved_thumbnail_id );

		$retrieved_svg_path = get_attached_file( $attach_id );
		$this->assertEquals( $upload['file'], $retrieved_svg_path );
		$this->assertStringContainsString( '<rect width="100" height="100"', file_get_contents( $retrieved_svg_path ) );
	}
}
