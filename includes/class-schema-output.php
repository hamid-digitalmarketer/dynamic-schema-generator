<?php
/**
 * Schema output renderer into document head.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Schema_Output
 */
class Schema_Output {

	/**
	 * Register output hook.
	 */
	public function register_hooks() {
		add_action( 'wp_head', array( $this, 'render_schema' ), 2 );
	}

	/**
	 * Check if another major SEO plugin is active.
	 *
	 * @return array List of detected active SEO plugins.
	 */
	public static function get_active_seo_plugins() {
		$detected = array();

		if ( defined( 'WPSEO_VERSION' ) || class_exists( 'WPSEO_Frontend' ) ) {
			$detected[] = 'Yoast SEO';
		}
		if ( defined( 'RANK_MATH_VERSION' ) || class_exists( '\\RankMath\\Runner' ) ) {
			$detected[] = 'Rank Math';
		}
		if ( defined( 'AIOSEO_VERSION' ) || class_exists( 'AIOSEO\\Plugin\\AIOSEO' ) ) {
			$detected[] = 'All in One SEO (AIOSEO)';
		}
		if ( defined( 'SEOPRESS_VERSION' ) ) {
			$detected[] = 'SEOPress';
		}

		return $detected;
	}

	/**
	 * Determine if schema should be rendered in current context.
	 *
	 * @return bool
	 */
	public function should_render() {
		// Never render in admin, feed, embed, preview, or XML-RPC
		if ( is_admin() || is_feed() || is_preview() || is_embed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}

		$settings = Settings::get_all();
		if ( empty( $settings['enable_schema'] ) ) {
			return false;
		}

		// Check post type support if singular
		if ( is_singular() ) {
			$post_type = get_post_type();
			$allowed   = (array) $settings['post_types'];
			if ( ! in_array( $post_type, $allowed, true ) ) {
				return false;
			}

			$post_id = get_the_ID();
			if ( in_array( $post_id, (array) $settings['excluded_posts'], true ) ) {
				return false;
			}

			if ( get_post_meta( $post_id, '_dsg_exclude_schema', true ) ) {
				return false;
			}
		}

		return apply_filters( 'dsg_should_render_schema', true );
	}

	/**
	 * Render JSON-LD schema into wp_head.
	 */
	public function render_schema() {
		if ( ! $this->should_render() ) {
			return;
		}

		$generator = new Schema_Generator();
		$graph     = $generator->generate();

		if ( empty( $graph ) ) {
			return;
		}

		/**
		 * Filter the generated schema graph before output.
		 *
		 * @param array $graph Complete schema graph array.
		 */
		$graph = apply_filters( 'dsg_schema_graph', $graph );

		if ( empty( $graph ) || ! is_array( $graph ) ) {
			return;
		}

		$settings = Settings::get_all();
		$flags    = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
		if ( empty( $settings['minify_json'] ) || ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			$flags |= JSON_PRETTY_PRINT;
		}

		$json = wp_json_encode( $graph, $flags );
		if ( empty( $json ) ) {
			return;
		}

		// Safe output with comment wrapper
		echo "\n<!-- Dynamic Schema Generator for WordPress v" . esc_html( DSG_VERSION ) . " -->\n";
		echo '<script type="application/ld+json" class="dsg-schema-graph">' . "\n";
		echo $json . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo "</script>\n";
		echo "<!-- /Dynamic Schema Generator -->\n\n";
	}
}
