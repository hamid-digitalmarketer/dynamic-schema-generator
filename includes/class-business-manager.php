<?php
/**
 * Business information manager for Organization and LocalBusiness schemas.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Business_Manager
 */
class Business_Manager {

	/**
	 * Option key.
	 */
	const OPTION_KEY = 'dsg_business_info';

	/**
	 * Supported LocalBusiness subtypes.
	 *
	 * @return array
	 */
	public static function get_supported_types() {
		return array(
			'Organization'                => __( 'Organization (Generic)', 'dynamic-schema-generator' ),
			'LocalBusiness'               => __( 'LocalBusiness (Generic)', 'dynamic-schema-generator' ),
			'ProfessionalService'         => __( 'Professional Service', 'dynamic-schema-generator' ),
			'LegalService'                => __( 'Legal Service / Law Firm', 'dynamic-schema-generator' ),
			'MedicalBusiness'             => __( 'Medical Clinic / Healthcare', 'dynamic-schema-generator' ),
			'FinancialService'            => __( 'Financial Service / Accounting', 'dynamic-schema-generator' ),
			'RealEstateAgent'             => __( 'Real Estate Agency', 'dynamic-schema-generator' ),
			'AutomotiveBusiness'          => __( 'Automotive Business', 'dynamic-schema-generator' ),
			'FoodEstablishment'           => __( 'Restaurant / Cafe', 'dynamic-schema-generator' ),
			'HomeAndConstructionBusiness' => __( 'Home & Construction / Contractor', 'dynamic-schema-generator' ),
			'Store'                       => __( 'Retail Store', 'dynamic-schema-generator' ),
			'EducationalOrganization'     => __( 'Educational Organization', 'dynamic-schema-generator' ),
		);
	}

	/**
	 * Retrieve saved business info merged with defaults.
	 *
	 * @return array
	 */
	public static function get_info() {
		$defaults = array(
			'business_type'        => 'Organization',
			'name'                 => get_bloginfo( 'name' ),
			'legal_name'           => '',
			'description'          => get_bloginfo( 'description' ),
			'url'                  => home_url( '/' ),
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
			'area_served'          => '',
			'opening_hours'        => array(),
			'same_as'              => array(),
		);

		$saved = get_option( self::OPTION_KEY, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		return wp_parse_args( $saved, $defaults );
	}

	/**
	 * Sanitize and save business information.
	 *
	 * @param array $input Raw form data.
	 * @return bool
	 */
	public static function save_info( array $input ) {
		$types = self::get_supported_types();
		$type  = isset( $input['business_type'] ) && array_key_exists( $input['business_type'], $types )
			? sanitize_text_field( $input['business_type'] )
			: 'Organization';

		$sanitized = array(
			'business_type'    => $type,
			'name'             => isset( $input['name'] ) ? sanitize_text_field( $input['name'] ) : '',
			'legal_name'       => isset( $input['legal_name'] ) ? sanitize_text_field( $input['legal_name'] ) : '',
			'description'      => isset( $input['description'] ) ? sanitize_textarea_field( $input['description'] ) : '',
			'url'              => isset( $input['url'] ) ? esc_url_raw( $input['url'] ) : '',
			'logo_url'         => isset( $input['logo_url'] ) ? esc_url_raw( $input['logo_url'] ) : '',
			'telephone'        => isset( $input['telephone'] ) ? sanitize_text_field( $input['telephone'] ) : '',
			'email'            => isset( $input['email'] ) ? sanitize_email( $input['email'] ) : '',
			'street_address'   => isset( $input['street_address'] ) ? sanitize_text_field( $input['street_address'] ) : '',
			'address_locality' => isset( $input['address_locality'] ) ? sanitize_text_field( $input['address_locality'] ) : '',
			'address_region'   => isset( $input['address_region'] ) ? sanitize_text_field( $input['address_region'] ) : '',
			'postal_code'      => isset( $input['postal_code'] ) ? sanitize_text_field( $input['postal_code'] ) : '',
			'address_country'  => isset( $input['address_country'] ) ? strtoupper( sanitize_text_field( $input['address_country'] ) ) : '',
			'latitude'         => isset( $input['latitude'] ) ? filter_var( $input['latitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : '',
			'longitude'        => isset( $input['longitude'] ) ? filter_var( $input['longitude'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION ) : '',
			'price_range'      => isset( $input['price_range'] ) ? sanitize_text_field( $input['price_range'] ) : '',
			'area_served'      => isset( $input['area_served'] ) ? sanitize_text_field( $input['area_served'] ) : '',
			'opening_hours'    => array(),
			'same_as'          => array(),
		);

		// Sanitize same_as URLs (one per line or array).
		if ( isset( $input['same_as'] ) ) {
			if ( is_string( $input['same_as'] ) ) {
				$lines = explode( "\n", str_replace( "\r", '', $input['same_as'] ) );
			} elseif ( is_array( $input['same_as'] ) ) {
				$lines = $input['same_as'];
			} else {
				$lines = array();
			}

			$valid_urls = array();
			foreach ( $lines as $line ) {
				$trimmed = trim( $line );
				if ( ! empty( $trimmed ) && filter_var( $trimmed, FILTER_VALIDATE_URL ) ) {
					$valid_urls[] = esc_url_raw( $trimmed );
				}
			}
			$sanitized['same_as'] = array_values( array_unique( $valid_urls ) );
		}

		// Sanitize opening hours specifications.
		if ( isset( $input['opening_hours'] ) && is_array( $input['opening_hours'] ) ) {
			$clean_hours = array();
			$valid_days  = array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday' );

			foreach ( $input['opening_hours'] as $spec ) {
				if ( empty( $spec['day'] ) || ! in_array( $spec['day'], $valid_days, true ) ) {
					continue;
				}
				$opens  = isset( $spec['opens'] ) ? sanitize_text_field( $spec['opens'] ) : '';
				$closes = isset( $spec['closes'] ) ? sanitize_text_field( $spec['closes'] ) : '';

				if ( preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $opens ) && preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $closes ) ) {
					$clean_hours[] = array(
						'day'    => $spec['day'],
						'opens'  => $opens,
						'closes' => $closes,
					);
				}
			}
			$sanitized['opening_hours'] = $clean_hours;
		}

		return update_option( self::OPTION_KEY, $sanitized );
	}
}
