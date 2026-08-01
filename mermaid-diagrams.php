<?php
/**
 * Plugin Name: Mermaid Diagrams
 * Plugin URI: https://github.com/webfalcon/mermaid-diagrams
 * Description: Reusable Mermaid diagrams for WordPress.
 * Version: 1.3.0
 * Requires at least: 7.0
 * Requires PHP: 8.3
 * Author: WebFalcon
 * License: GPL-2.0-or-later
 * Text Domain: mermaid-diagrams
 * Domain Path: /languages
 *
 * @package WebFalcon\MermaidDiagrams
 */

use WebFalcon\MermaidDiagrams\Bootstrap\Activation;
use WebFalcon\MermaidDiagrams\Bootstrap\Deactivation;
use WebFalcon\MermaidDiagrams\Bootstrap\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MDM_PLUGIN_FILE', __FILE__ );
define( 'MDM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MDM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MDM_VERSION', '1.3.0' );
define( 'MDM_MERMAID_VERSION', '11.4.1' );

if ( file_exists( MDM_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once MDM_PLUGIN_DIR . 'vendor/autoload.php';
}

register_activation_hook( __FILE__, array( Activation::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( Deactivation::class, 'deactivate' ) );

add_action(
	'plugins_loaded',
	function () {
		Plugin::instance()->boot();
	}
);
