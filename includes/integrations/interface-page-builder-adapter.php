<?php
/**
 * Interface for Page Builder Adapters.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Integrations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Interface Page_Builder_Adapter_Interface
 */
interface Page_Builder_Adapter_Interface {

	/**
	 * Check if the page builder is installed and active.
	 *
	 * @return bool
	 */
	public function is_active();

	/**
	 * Check if a specific post/page was built with this builder.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public function is_built_with( $post_id );

	/**
	 * Extract FAQ items from page builder layout.
	 *
	 * @param int $post_id Post ID.
	 * @return array Array of array( 'question' => '...', 'answer' => '...' )
	 */
	public function extract_faqs( $post_id );

	/**
	 * Extract headings or outline from page builder layout.
	 *
	 * @param int $post_id Post ID.
	 * @return array Array of strings.
	 */
	public function extract_headings( $post_id );
}
