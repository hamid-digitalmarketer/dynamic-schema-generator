<?php
/**
 * FAQ schema manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class FAQ_Manager
 */
class FAQ_Manager {

	/**
	 * Option key for global / standalone FAQs.
	 */
	const OPTION_KEY = 'dsg_faqs';

	/**
	 * Post meta key for page-specific FAQs.
	 */
	const META_KEY = '_dsg_custom_faqs';

	/**
	 * Get all site-level FAQs.
	 *
	 * @return array
	 */
	public static function get_all() {
		$faqs = get_option( self::OPTION_KEY, array() );
		return is_array( $faqs ) ? $faqs : array();
	}

	/**
	 * Get FAQs for a specific post or page.
	 *
	 * Checks page-level custom meta first, then falls back to mapped global FAQs.
	 *
	 * @param int $post_id Post ID.
	 * @return array Array of array( 'question' => '...', 'answer' => '...' )
	 */
	public static function get_by_post_id( $post_id ) {
		$post_id = absint( $post_id );
		$meta    = get_post_meta( $post_id, self::META_KEY, true );

		if ( ! empty( $meta ) && is_array( $meta ) ) {
			return self::sanitize_faq_list( $meta );
		}

		// Check global FAQs mapped to this page.
		$all    = self::get_all();
		$mapped = array();
		foreach ( $all as $item ) {
			if ( ! empty( $item['page_id'] ) && absint( $item['page_id'] ) === $post_id ) {
				$mapped[] = $item;
			}
		}

		return self::sanitize_faq_list( $mapped );
	}

	/**
	 * Save page-level FAQs.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $faqs Array of question/answer pairs.
	 * @return bool
	 */
	public static function save_post_faqs( $post_id, array $faqs ) {
		$post_id = absint( $post_id );
		$clean   = self::sanitize_faq_list( $faqs );

		if ( empty( $clean ) ) {
			return delete_post_meta( $post_id, self::META_KEY );
		}

		return update_post_meta( $post_id, self::META_KEY, $clean );
	}

	/**
	 * Add or update global FAQ entry.
	 *
	 * @param array $data FAQ entry.
	 * @return string Entry ID on success, empty on failure.
	 */
	public static function save( array $data ) {
		$all = self::get_all();
		$id  = ! empty( $data['id'] ) ? sanitize_key( $data['id'] ) : 'faq_' . wp_generate_password( 8, false );

		$question = isset( $data['question'] ) ? sanitize_text_field( $data['question'] ) : '';
		$answer   = isset( $data['answer'] ) ? wp_kses_post( $data['answer'] ) : '';

		if ( empty( $question ) || empty( $answer ) ) {
			return '';
		}

		$all[ $id ] = array(
			'id'       => $id,
			'question' => $question,
			'answer'   => $answer,
			'page_id'  => isset( $data['page_id'] ) ? absint( $data['page_id'] ) : 0,
		);

		update_option( self::OPTION_KEY, $all );
		return $id;
	}

	/**
	 * Delete global FAQ entry.
	 *
	 * @param string $id Entry ID.
	 * @return bool
	 */
	public static function delete( $id ) {
		$id  = sanitize_key( $id );
		$all = self::get_all();

		if ( isset( $all[ $id ] ) ) {
			unset( $all[ $id ] );
			return update_option( self::OPTION_KEY, $all );
		}

		return false;
	}

	/**
	 * Sanitize an array of FAQ items and deduplicate by question.
	 *
	 * @param array $raw Raw items.
	 * @return array
	 */
	public static function sanitize_faq_list( array $raw ) {
		$clean = array();
		$seen  = array();

		foreach ( $raw as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$q = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
			$a = isset( $item['answer'] ) ? wp_kses_post( $item['answer'] ) : '';

			if ( empty( $q ) || empty( $a ) ) {
				continue;
			}

			$q_key = strtolower( trim( $q ) );
			if ( isset( $seen[ $q_key ] ) ) {
				continue; // Prevent duplicates.
			}

			$seen[ $q_key ] = true;
			$clean[]        = array(
				'question' => $q,
				'answer'   => $a,
			);
		}

		return $clean;
	}
}
