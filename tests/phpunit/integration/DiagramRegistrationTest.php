<?php
/**
 * Test WordPress registration for CPT, taxonomies, meta, and capabilities.
 *
 * @package WebFalcon\MermaidDiagrams\Tests\Integration
 */

namespace WebFalcon\MermaidDiagrams\Tests\Integration;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramCapabilities;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramMeta;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramPostType;
use WebFalcon\MermaidDiagrams\Diagram\Infrastructure\DiagramTaxonomies;

/**
 * Class DiagramRegistrationTest
 */
class DiagramRegistrationTest extends TestCase {

	/**
	 * Test CPT is registered with expected args.
	 */
	public function test_cpt_registration(): void {
		DiagramPostType::register();

		$this->assertTrue( post_type_exists( DiagramPostType::CPT_SLUG ) );
		$object = get_post_type_object( DiagramPostType::CPT_SLUG );

		$this->assertNotNull( $object );
		$this->assertTrue( $object->show_in_rest );
		$this->assertSame( 'mdm-diagrams', $object->rest_base );
		$this->assertFalse( $object->public );
		$this->assertTrue( $object->map_meta_cap );
	}

	/**
	 * Test taxonomy registrations.
	 */
	public function test_taxonomy_registrations(): void {
		DiagramTaxonomies::register();

		$this->assertTrue( taxonomy_exists( DiagramTaxonomies::TAXONOMY_CATEGORY ) );
		$this->assertTrue( taxonomy_exists( DiagramTaxonomies::TAXONOMY_TAG ) );

		$category_obj = get_taxonomy( DiagramTaxonomies::TAXONOMY_CATEGORY );
		$tag_obj      = get_taxonomy( DiagramTaxonomies::TAXONOMY_TAG );

		$this->assertNotNull( $category_obj );
		$this->assertNotNull( $tag_obj );

		$this->assertTrue( $category_obj->hierarchical );
		$this->assertFalse( $tag_obj->hierarchical );

		$this->assertSame( 'mdm-diagram-categories', $category_obj->rest_base );
		$this->assertSame( 'mdm-diagram-tags', $tag_obj->rest_base );
	}

	/**
	 * Test protected post meta registration.
	 */
	public function test_meta_registrations(): void {
		DiagramPostType::register();
		DiagramMeta::register();

		$this->assertTrue( registered_meta_key_exists( 'post', DiagramMeta::META_DIAGRAM_TYPE, DiagramPostType::CPT_SLUG ) );
		$this->assertTrue( registered_meta_key_exists( 'post', DiagramMeta::META_RENDER_CONFIG, DiagramPostType::CPT_SLUG ) );
		$this->assertTrue( registered_meta_key_exists( 'post', DiagramMeta::META_SOURCE_HASH, DiagramPostType::CPT_SLUG ) );
		$this->assertTrue( registered_meta_key_exists( 'post', DiagramMeta::META_VALIDATION_STATE, DiagramPostType::CPT_SLUG ) );
		$this->assertTrue( registered_meta_key_exists( 'post', DiagramMeta::META_LAST_EDITOR_ID, DiagramPostType::CPT_SLUG ) );
	}

	/**
	 * Test capability assignment idempotency.
	 */
	public function test_capability_assignment(): void {
		DiagramCapabilities::assign_default_capabilities();

		$admin_role  = get_role( 'administrator' );
		$editor_role = get_role( 'editor' );

		$this->assertNotNull( $admin_role );
		$this->assertNotNull( $editor_role );

		foreach ( DiagramCapabilities::all_capabilities() as $cap ) {
			$this->assertTrue( $admin_role->has_cap( $cap ), "Admin missing cap: {$cap}" );
		}

		$this->assertTrue( $editor_role->has_cap( DiagramCapabilities::CAP_EDIT_DIAGRAMS ) );
		$this->assertFalse( $editor_role->has_cap( DiagramCapabilities::CAP_MANAGE_SETTINGS ) );

		// Double call check for idempotency.
		DiagramCapabilities::assign_default_capabilities();
		$this->assertTrue( $admin_role->has_cap( DiagramCapabilities::CAP_MANAGE_SETTINGS ) );
	}
}
