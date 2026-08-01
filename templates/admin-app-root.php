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

	<div id="mdm-diagram-library-root" class="mdm-app-root">
		<div class="notice notice-info inline" style="margin-top: 20px;">
			<p>
				<strong><?php esc_html_e( 'Mermaid Diagrams Plugin Kernel Initialized', 'mermaid-diagrams' ); ?></strong>
			</p>
			<p>
				<?php esc_html_e( 'The Phase 01 plugin kernel, domain model, and storage repositories are active. The React Diagram Library UI will mount here in Phase 04.', 'mermaid-diagrams' ); ?>
			</p>
		</div>
	</div>
</div>
