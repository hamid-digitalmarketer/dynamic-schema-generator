<?php
/**
 * Service schema manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Service_Manager
 */
class Service_Manager {

	/**
	 * Option key.
	 */
	const OPTION_KEY = 'dsg_services';

	/**
	 * Get all configured services.
	 *
	 * @return array
	 */
	public static function get_all() {
		$services = get_option( self::OPTION_KEY, array() );
		return is_array( $services ) ? $services : array();
	}

	/**
	 * Get service mapped to a specific post or page ID.
	 *
	 * @param int $post_id Post ID.
	 * @return array|null
	 */
	public static function get_by_page_id( $post_id ) {
		$post_id  = absint( $post_id );
		$services = self::get_all();

		foreach ( $services as $service ) {
			if ( ! empty( $service['page_id'] ) && absint( $service['page_id'] ) === $post_id ) {
				return $service;
			}
		}

		return null;
	}

	/**
	 * Add or update a service.
	 *
	 * @param array $data Service data.
	 * @return string Service ID on success, empty string on failure.
	 */
	public static function save( array $data ) {
		$services = self::get_all();

		$id = ! empty( $data['id'] ) ? sanitize_key( $data['id'] ) : 'srv_' . wp_generate_password( 8, false );

		$name = isset( $data['name'] ) ? sanitize_text_field( $data['name'] ) : '';
		if ( empty( $name ) ) {
			return '';
		}

		$clean = array(
			'id'          => $id,
			'name'        => $name,
			'description' => isset( $data['description'] ) ? sanitize_textarea_field( $data['description'] ) : '',
			'service_type'=> isset( $data['service_type'] ) ? sanitize_text_field( $data['service_type'] ) : '',
			'area_served' => isset( $data['area_served'] ) ? sanitize_text_field( $data['area_served'] ) : '',
			'page_id'     => isset( $data['page_id'] ) ? absint( $data['page_id'] ) : 0,
			'image_url'   => isset( $data['image_url'] ) ? esc_url_raw( $data['image_url'] ) : '',
			'price'       => '',
			'currency'    => 'USD',
		);

		// Pricing is strictly optional and sanitized only if valid.
		if ( ! empty( $data['price'] ) && is_numeric( $data['price'] ) ) {
			$clean['price']    = number_format( (float) $data['price'], 2, '.', '' );
			$clean['currency'] = ! empty( $data['currency'] ) ? strtoupper( substr( sanitize_text_field( $data['currency'] ), 0, 3 ) ) : 'USD';
		}

		$services[ $id ] = $clean;
		update_option( self::OPTION_KEY, $services );

		return $id;
	}

	/**
	 * Delete a service by ID.
	 *
	 * @param string $id Service ID.
	 * @return bool
	 */
	public static function delete( $id ) {
		$id       = sanitize_key( $id );
		$services = self::get_all();

		if ( isset( $services[ $id ] ) ) {
			unset( $services[ $id ] );
			return update_option( self::OPTION_KEY, $services );
		}

		return false;
	}
}
