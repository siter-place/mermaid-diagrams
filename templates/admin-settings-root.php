<?php
/**
 * Admin Settings Root HTML Shell Template.
 *
 * @package WebFalcon\MermaidDiagrams\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap mdm-admin-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Mermaid Diagrams Settings', 'mermaid-diagrams' ); ?></h1>
	<hr class="wp-header-end">

	<div id="mdm-settings-root" class="mdm-app-root"></div>
</div>
