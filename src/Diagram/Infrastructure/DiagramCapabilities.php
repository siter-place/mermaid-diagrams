<?php
/**
 * Diagram Capabilities Management.
 *
 * @package WebFalcon\MermaidDiagrams\Diagram\Infrastructure
 */

namespace WebFalcon\MermaidDiagrams\Diagram\Infrastructure;

use WP_Role;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Defines and assigns plugin capabilities.
 */
class DiagramCapabilities {

	public const CAP_EDIT_DIAGRAM              = 'edit_mdm_diagram';
	public const CAP_READ_DIAGRAM              = 'read_mdm_diagram';
	public const CAP_DELETE_DIAGRAM            = 'delete_mdm_diagram';
	public const CAP_EDIT_DIAGRAMS             = 'edit_mdm_diagrams';
	public const CAP_EDIT_OTHERS_DIAGRAMS      = 'edit_others_mdm_diagrams';
	public const CAP_PUBLISH_DIAGRAMS          = 'publish_mdm_diagrams';
	public const CAP_READ_PRIVATE_DIAGRAMS     = 'read_private_mdm_diagrams';
	public const CAP_DELETE_DIAGRAMS           = 'delete_mdm_diagrams';
	public const CAP_DELETE_PRIVATE_DIAGRAMS   = 'delete_private_mdm_diagrams';
	public const CAP_DELETE_PUBLISHED_DIAGRAMS = 'delete_published_mdm_diagrams';
	public const CAP_DELETE_OTHERS_DIAGRAMS    = 'delete_others_mdm_diagrams';
	public const CAP_EDIT_PRIVATE_DIAGRAMS     = 'edit_private_mdm_diagrams';
	public const CAP_EDIT_PUBLISHED_DIAGRAMS   = 'edit_published_mdm_diagrams';
	public const CAP_MANAGE_TERMS              = 'manage_mdm_diagram_terms';
	public const CAP_MANAGE_SETTINGS           = 'manage_mdm_settings';

	/**
	 * Get all CPT and plugin capability strings.
	 *
	 * @return string[]
	 */
	public static function all_capabilities(): array {
		return array(
			self::CAP_EDIT_DIAGRAM,
			self::CAP_READ_DIAGRAM,
			self::CAP_DELETE_DIAGRAM,
			self::CAP_EDIT_DIAGRAMS,
			self::CAP_EDIT_OTHERS_DIAGRAMS,
			self::CAP_PUBLISH_DIAGRAMS,
			self::CAP_READ_PRIVATE_DIAGRAMS,
			self::CAP_DELETE_DIAGRAMS,
			self::CAP_DELETE_PRIVATE_DIAGRAMS,
			self::CAP_DELETE_PUBLISHED_DIAGRAMS,
			self::CAP_DELETE_OTHERS_DIAGRAMS,
			self::CAP_EDIT_PRIVATE_DIAGRAMS,
			self::CAP_EDIT_PUBLISHED_DIAGRAMS,
			self::CAP_MANAGE_TERMS,
			self::CAP_MANAGE_SETTINGS,
		);
	}

	/**
	 * Assign default capabilities to Administrator and Editor roles idempotently.
	 *
	 * @return void
	 */
	public static function assign_default_capabilities(): void {
		$admin_role = get_role( 'administrator' );
		if ( $admin_role instanceof WP_Role ) {
			foreach ( self::all_capabilities() as $cap ) {
				if ( ! $admin_role->has_cap( $cap ) ) {
					$admin_role->add_cap( $cap );
				}
			}
		}

		$editor_role = get_role( 'editor' );
		if ( $editor_role instanceof WP_Role ) {
			$editor_caps = array(
				self::CAP_EDIT_DIAGRAM,
				self::CAP_READ_DIAGRAM,
				self::CAP_DELETE_DIAGRAM,
				self::CAP_EDIT_DIAGRAMS,
				self::CAP_EDIT_OTHERS_DIAGRAMS,
				self::CAP_PUBLISH_DIAGRAMS,
				self::CAP_READ_PRIVATE_DIAGRAMS,
				self::CAP_DELETE_DIAGRAMS,
				self::CAP_DELETE_PRIVATE_DIAGRAMS,
				self::CAP_DELETE_PUBLISHED_DIAGRAMS,
				self::CAP_DELETE_OTHERS_DIAGRAMS,
				self::CAP_EDIT_PRIVATE_DIAGRAMS,
				self::CAP_EDIT_PUBLISHED_DIAGRAMS,
				self::CAP_MANAGE_TERMS,
			);

			foreach ( $editor_caps as $cap ) {
				if ( ! $editor_role->has_cap( $cap ) ) {
					$editor_role->add_cap( $cap );
				}
			}
		}
	}
}
