<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Dynamic_Schema_Generator
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Option to keep data on uninstall by default unless user opted into clean uninstall.
$settings = get_option( 'dsg_settings', array() );
$delete_data = isset( $settings['delete_data_on_uninstall'] ) && $settings['delete_data_on_uninstall'];

if ( $delete_data ) {
	delete_option( 'dsg_settings' );
	delete_option( 'dsg_business_info' );
	delete_option( 'dsg_services' );
	delete_option( 'dsg_faqs' );
	delete_option( 'dsg_diagnostics_log' );
	delete_option( 'dsg_version' );

	// Clean post meta created by DSG.
	delete_post_meta_by_key( '_dsg_schema_override' );
	delete_post_meta_by_key( '_dsg_exclude_schema' );
	delete_post_meta_by_key( '_dsg_custom_faqs' );
	delete_post_meta_by_key( '_dsg_service_id' );
}
