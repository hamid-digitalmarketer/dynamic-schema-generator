<?php
/**
 * Plugin settings manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 */
class Settings {

	/**
	 * Option key.
	 */
	const OPTION_KEY = 'dsg_settings';

	/**
	 * Get all settings merged with defaults.
	 *
	 * @return array
	 */
	public static function get_all() {
		$defaults = array(
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

		$saved = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return wp_parse_args( $saved, $defaults );
	}

	/**
	 * Get a specific setting.
	 *
	 * @param string $key Setting key.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	public static function get( $key, $default = null ) {
		$all = self::get_all();
		return isset( $all[ $key ] ) ? $all[ $key ] : $default;
	}

	/**
	 * Update settings with sanitization.
	 *
	 * @param array $input Raw input.
	 * @return bool
	 */
	public static function update( array $input ) {
		$sanitized = array(
			'enable_schema'              => ! empty( $input['enable_schema'] ),
			'enable_organization'        => ! empty( $input['enable_organization'] ),
			'enable_local_business'      => ! empty( $input['enable_local_business'] ),
			'enable_website'             => ! empty( $input['enable_website'] ),
			'enable_breadcrumbs'         => ! empty( $input['enable_breadcrumbs'] ),
			'enable_articles'            => ! empty( $input['enable_articles'] ),
			'enable_services'            => ! empty( $input['enable_services'] ),
			'enable_faqs'                => ! empty( $input['enable_faqs'] ),
			'enable_elementor_adapter'   => ! empty( $input['enable_elementor_adapter'] ),
			'post_types'                 => array(),
			'excluded_posts'             => array(),
			'conflict_detection'         => ! empty( $input['conflict_detection'] ),
			'minify_json'                => ! empty( $input['minify_json'] ),
			'delete_data_on_uninstall'   => ! empty( $input['delete_data_on_uninstall'] ),
		);

		if ( isset( $input['post_types'] ) && is_array( $input['post_types'] ) ) {
			$sanitized['post_types'] = array_map( 'sanitize_key', $input['post_types'] );
		}

		if ( isset( $input['excluded_posts'] ) ) {
			if ( is_string( $input['excluded_posts'] ) ) {
				$parts = explode( ',', $input['excluded_posts'] );
				$sanitized['excluded_posts'] = array_filter( array_map( 'absint', $parts ) );
			} elseif ( is_array( $input['excluded_posts'] ) ) {
				$sanitized['excluded_posts'] = array_filter( array_map( 'absint', $input['excluded_posts'] ) );
			}
		}

		return update_option( self::OPTION_KEY, $sanitized );
	}
}
