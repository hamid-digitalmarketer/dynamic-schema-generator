<?php
/**
 * System diagnostics and health inspector.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

use DSG\Integrations\Elementor_Adapter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Diagnostics
 */
class Diagnostics {

	/**
	 * Option key for logs.
	 */
	const LOG_OPTION = 'dsg_diagnostics_log';

	/**
	 * Maximum entries to retain.
	 */
	const MAX_LOGS = 50;

	/**
	 * Collect comprehensive system and plugin health report.
	 *
	 * @return array
	 */
	public static function get_report() {
		global $wp_version;

		$elementor_adapter = new Elementor_Adapter();
		$settings          = Settings::get_all();
		$business          = Business_Manager::get_info();
		$services          = Service_Manager::get_all();
		$faqs              = FAQ_Manager::get_all();
		$conflicts         = Schema_Output::get_active_seo_plugins();

		// Count enabled schema modules
		$enabled_modules = 0;
		$modules = array( 'enable_organization', 'enable_local_business', 'enable_website', 'enable_breadcrumbs', 'enable_articles', 'enable_services', 'enable_faqs' );
		foreach ( $modules as $m ) {
			if ( ! empty( $settings[ $m ] ) ) {
				$enabled_modules++;
			}
		}

		$status_flags = array();
		if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
			$status_flags[] = array( 'level' => 'error', 'message' => __( 'PHP version is below 7.4. Upgrade recommended.', 'dynamic-schema-generator' ) );
		}
		if ( ! empty( $conflicts ) && ! empty( $settings['conflict_detection'] ) ) {
			$status_flags[] = array( 'level' => 'warning', 'message' => sprintf( __( 'Detected active SEO plugins: %s. Verify duplicate schema settings.', 'dynamic-schema-generator' ), implode( ', ', $conflicts ) ) );
		}
		if ( empty( $business['name'] ) ) {
			$status_flags[] = array( 'level' => 'warning', 'message' => __( 'Business Name is missing in Business Information settings.', 'dynamic-schema-generator' ) );
		}
		if ( ! empty( $settings['enable_local_business'] ) && empty( $business['street_address'] ) ) {
			$status_flags[] = array( 'level' => 'info', 'message' => __( 'LocalBusiness is enabled but street address is not set.', 'dynamic-schema-generator' ) );
		}

		return array(
			'plugin_version'   => DSG_VERSION,
			'wordpress_version'=> $wp_version,
			'php_version'      => PHP_VERSION,
			'web_server'       => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( $_SERVER['SERVER_SOFTWARE'] ) : 'Unknown',
			'elementor_active' => $elementor_adapter->is_active(),
			'detected_seo_plugins' => $conflicts,
			'enabled_modules'  => $enabled_modules,
			'total_services'   => count( $services ),
			'total_faqs'       => count( $faqs ),
			'post_types'       => isset( $settings['post_types'] ) ? $settings['post_types'] : array(),
			'status_flags'     => $status_flags,
			'recent_logs'      => self::get_logs(),
		);
	}

	/**
	 * Log a message to diagnostics.
	 *
	 * @param string $message Log message.
	 * @param string $type Log type (info, warning, error).
	 */
	public static function log( $message, $type = 'info' ) {
		$logs   = self::get_logs();
		$entry  = array(
			'timestamp' => current_time( 'mysql' ),
			'type'      => sanitize_key( $type ),
			'message'   => sanitize_text_field( $message ),
		);

		array_unshift( $logs, $entry );
		if ( count( $logs ) > self::MAX_LOGS ) {
			$logs = array_slice( $logs, 0, self::MAX_LOGS );
		}

		update_option( self::LOG_OPTION, $logs );
	}

	/**
	 * Retrieve stored logs.
	 *
	 * @return array
	 */
	public static function get_logs() {
		$logs = get_option( self::LOG_OPTION, array() );
		return is_array( $logs ) ? $logs : array();
	}

	/**
	 * Clear diagnostics log.
	 *
	 * @return bool
	 */
	public static function clear_logs() {
		return update_option( self::LOG_OPTION, array() );
	}
}
