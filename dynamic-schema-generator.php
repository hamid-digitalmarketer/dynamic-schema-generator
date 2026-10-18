<?php
/**
 * Plugin Name:       Dynamic Schema Generator for WordPress
 * Plugin URI:        https://github.com/hamid-digitalmarketer/dynamic-schema-generator
 * Description:       Automatically generates accurate, structured JSON-LD Schema.org markup from WordPress content, landing pages, and Elementor layouts with zero bloat and full conflict detection.
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Muhammad Hamid
 * Author URI:        https://hamid-resume.web.app/
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       dynamic-schema-generator
 * Domain Path:       /languages
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin constants with conditional check.
if ( ! defined( 'DSG_VERSION' ) ) {
	define( 'DSG_VERSION', '1.0.0' );
}
if ( ! defined( 'DSG_PLUGIN_FILE' ) ) {
	define( 'DSG_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'DSG_PLUGIN_DIR' ) ) {
	define( 'DSG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'DSG_PLUGIN_URL' ) ) {
	define( 'DSG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'DSG_PLUGIN_BASENAME' ) ) {
	define( 'DSG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

/**
 * Autoloader for DSG classes.
 *
 * @param string $class Class name.
 */
spl_autoload_register( function ( $class ) {
	$prefix = 'DSG\\';
	$base_dir = DSG_PLUGIN_DIR . 'includes/';

	$len = strlen( $prefix );
	if ( strncmp( $prefix, $class, $len ) !== 0 ) {
		return;
	}

	$relative_class = substr( $class, $len );

	// Handle sub-namespaces like DSG\Integrations\...
	if ( strpos( $relative_class, 'Integrations\\' ) === 0 ) {
		$sub_class = substr( $relative_class, strlen( 'Integrations\\' ) );
		$file_name = 'class-' . strtolower( str_replace( '_', '-', $sub_class ) ) . '.php';
		if ( strpos( $sub_class, 'Interface_' ) === 0 || strpos( $sub_class, 'Page_Builder' ) !== false ) {
			if ( $sub_class === 'Page_Builder_Adapter_Interface' ) {
				$file_name = 'interface-page-builder-adapter.php';
			}
		}
		$file = $base_dir . 'integrations/' . $file_name;
	} else {
		$file_name = 'class-' . strtolower( str_replace( '_', '-', $relative_class ) ) . '.php';
		$file = $base_dir . $file_name;
	}

	if ( file_exists( $file ) ) {
		require_once $file;
	}
} );

/**
 * Plugin activation hook.
 */
function activate_dynamic_schema_generator() {
	require_once DSG_PLUGIN_DIR . 'includes/class-activator.php';
	Activator::activate();
}
if ( function_exists( 'register_activation_hook' ) ) {
	register_activation_hook( __FILE__, 'DSG\\activate_dynamic_schema_generator' );
}

/**
 * Plugin deactivation hook.
 */
function deactivate_dynamic_schema_generator() {
	require_once DSG_PLUGIN_DIR . 'includes/class-deactivator.php';
	Deactivator::deactivate();
}
if ( function_exists( 'register_deactivation_hook' ) ) {
	register_deactivation_hook( __FILE__, 'DSG\\deactivate_dynamic_schema_generator' );
}

/**
 * Initialize the plugin.
 */
function run_dynamic_schema_generator() {
	require_once DSG_PLUGIN_DIR . 'includes/class-plugin.php';
	$plugin = Plugin::get_instance();
	$plugin->run();
}
if ( function_exists( 'add_action' ) ) {
	add_action( 'plugins_loaded', 'DSG\\run_dynamic_schema_generator' );
}
