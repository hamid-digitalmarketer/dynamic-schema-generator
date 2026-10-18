<?php
/**
 * Fired during plugin activation.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Activator
 */
class Activator {

	/**
	 * Activate plugin and install defaults.
	 */
	public static function activate() {
		// Default plugin settings.
		if ( false === get_option( 'dsg_settings' ) ) {
			$default_settings = array(
				'enable_schema'              => true,
				'enable_organization'        => true,
				'enable_local_business'      => false,
				'enable_website'             => true,
				'enable_breadcrumbs'         => true,
				'enable_articles'            => true,
				'enable_services'            => true,
				'enable_faqs'                => true,
				'enable_elementor_adapter'   => true,
				'post_types'                 => array( 'post', 'page' ),
				'excluded_posts'             => array(),
				'conflict_detection'         => true,
				'minify_json'                => true,
				'delete_data_on_uninstall'   => false,
			);
			update_option( 'dsg_settings', $default_settings );
		}

		// Default business information.
		if ( false === get_option( 'dsg_business_info' ) ) {
			$site_name = get_bloginfo( 'name' );
			$site_url  = home_url( '/' );
			$default_business = array(
				'business_type'        => 'Organization',
				'name'                 => $site_name ? $site_name : 'My Business',
				'legal_name'           => '',
				'description'          => get_bloginfo( 'description' ),
				'url'                  => $site_url,
				'logo_url'             => '',
				'telephone'            => '',
				'email'                => '',
				'street_address'       => '',
				'address_locality'     => '',
				'address_region'       => '',
				'postal_code'          => '',
				'address_country'      => '',
				'latitude'             => '',
				'longitude'            => '',
				'price_range'          => '',
				'opening_hours'        => array(),
				'same_as'              => array(),
				'area_served'          => '',
			);
			update_option( 'dsg_business_info', $default_business );
		}

		// Initialize empty services and FAQs collections if absent.
		if ( false === get_option( 'dsg_services' ) ) {
			update_option( 'dsg_services', array() );
		}
		if ( false === get_option( 'dsg_faqs' ) ) {
			update_option( 'dsg_faqs', array() );
		}
		if ( false === get_option( 'dsg_diagnostics_log' ) ) {
			update_option( 'dsg_diagnostics_log', array() );
		}

		update_option( 'dsg_version', DSG_VERSION );
	}
}
