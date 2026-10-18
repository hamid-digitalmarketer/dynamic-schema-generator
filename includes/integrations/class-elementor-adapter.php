<?php
/**
 * Elementor Page Builder Adapter.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Integrations;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Elementor_Adapter
 */
class Elementor_Adapter implements Page_Builder_Adapter_Interface {

	/**
	 * Check if Elementor plugin is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return did_action( 'elementor/loaded' ) || class_exists( '\\Elementor\\Plugin' );
	}

	/**
	 * Check if post is built with Elementor.
	 *
	 * @param int $post_id Post ID.
	 * @return bool
	 */
	public function is_built_with( $post_id ) {
		$post_id = absint( $post_id );
		if ( ! $post_id ) {
			return false;
		}

		$edit_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
		return 'builder' === $edit_mode;
	}

	/**
	 * Get Elementor raw document data.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public function get_document_data( $post_id ) {
		$raw = get_post_meta( $post_id, '_elementor_data', true );
		if ( empty( $raw ) ) {
			return array();
		}

		if ( is_string( $raw ) ) {
			$data = json_decode( $raw, true );
			return is_array( $data ) ? $data : array();
		}

		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * Extract FAQ items from Elementor accordion and toggle widgets.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public function extract_faqs( $post_id ) {
		$data = $this->get_document_data( $post_id );
		if ( empty( $data ) ) {
			return array();
		}

		$faqs = array();
		$this->find_faq_widgets_recursive( $data, $faqs );

		return \DSG\FAQ_Manager::sanitize_faq_list( $faqs );
	}

	/**
	 * Recursively search Elementor element tree for accordion / toggle widgets.
	 *
	 * @param array $elements Array of elements.
	 * @param array $results Reference to results accumulator.
	 */
	protected function find_faq_widgets_recursive( array $elements, array &$results ) {
		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			$el_type = isset( $element['elType'] ) ? $element['elType'] : '';
			$widget  = isset( $element['widgetType'] ) ? $element['widgetType'] : '';
			$settings = isset( $element['settings'] ) && is_array( $element['settings'] ) ? $element['settings'] : array();

			if ( 'widget' === $el_type ) {
				// Elementor Accordion widget (tabs property)
				if ( 'accordion' === $widget && ! empty( $settings['tabs'] ) && is_array( $settings['tabs'] ) ) {
					foreach ( $settings['tabs'] as $tab ) {
						$q = isset( $tab['tab_title'] ) ? $tab['tab_title'] : '';
						$a = isset( $tab['tab_content'] ) ? $tab['tab_content'] : '';
						if ( ! empty( $q ) && ! empty( $a ) ) {
							$results[] = array(
								'question' => wp_strip_all_tags( $q ),
								'answer'   => wp_kses_post( $a ),
							);
						}
					}
				}

				// Elementor Toggle widget (tabs property)
				if ( 'toggle' === $widget && ! empty( $settings['tabs'] ) && is_array( $settings['tabs'] ) ) {
					foreach ( $settings['tabs'] as $tab ) {
						$q = isset( $tab['tab_title'] ) ? $tab['tab_title'] : '';
						$a = isset( $tab['tab_content'] ) ? $tab['tab_content'] : '';
						if ( ! empty( $q ) && ! empty( $a ) ) {
							$results[] = array(
								'question' => wp_strip_all_tags( $q ),
								'answer'   => wp_kses_post( $a ),
							);
						}
					}
				}
			}

			// Traverse inner elements (sections, columns, nested containers)
			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$this->find_faq_widgets_recursive( $element['elements'], $results );
			}
		}
	}

	/**
	 * Extract visible headings from Elementor widgets.
	 *
	 * @param int $post_id Post ID.
	 * @return array
	 */
	public function extract_headings( $post_id ) {
		$data = $this->get_document_data( $post_id );
		if ( empty( $data ) ) {
			return array();
		}

		$headings = array();
		$this->find_headings_recursive( $data, $headings );

		return array_values( array_unique( $headings ) );
	}

	/**
	 * Recursively search for headings in Elementor data tree.
	 *
	 * @param array $elements Elements list.
	 * @param array $headings Reference to headings array.
	 */
	protected function find_headings_recursive( array $elements, array &$headings ) {
		foreach ( $elements as $element ) {
			if ( ! is_array( $element ) ) {
				continue;
			}

			$el_type  = isset( $element['elType'] ) ? $element['elType'] : '';
			$widget   = isset( $element['widgetType'] ) ? $element['widgetType'] : '';
			$settings = isset( $element['settings'] ) && is_array( $element['settings'] ) ? $element['settings'] : array();

			if ( 'widget' === $el_type && 'heading' === $widget && ! empty( $settings['title'] ) ) {
				$clean = wp_strip_all_tags( $settings['title'] );
				if ( ! empty( $clean ) ) {
					$headings[] = $clean;
				}
			}

			if ( ! empty( $element['elements'] ) && is_array( $element['elements'] ) ) {
				$this->find_headings_recursive( $element['elements'], $headings );
			}
		}
	}
}
