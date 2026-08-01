<?php
/**
 * Admin App Root HTML Shell Template.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap mdm-admin-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Diagrams', 'mermaid-diagrams' ); ?></h1>
	<hr class="wp-header-end">

	<div id="mdm-diagram-library-root" class="mdm-app-root"></div>
</div>
