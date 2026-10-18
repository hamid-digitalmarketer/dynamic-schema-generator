<?php
/**
 * PHPUnit test bootstrap file with WordPress function mocks.
 *
 * @package Dynamic_Schema_Generator
 */

// Define constants
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/../' );
}
if ( ! defined( 'DSG_VERSION' ) ) {
	define( 'DSG_VERSION', '1.0.0' );
}
if ( ! defined( 'DSG_PLUGIN_FILE' ) ) {
	define( 'DSG_PLUGIN_FILE', __DIR__ . '/../dynamic-schema-generator.php' );
}
if ( ! defined( 'DSG_PLUGIN_DIR' ) ) {
	define( 'DSG_PLUGIN_DIR', dirname( __DIR__ ) . '/' );
}
if ( ! defined( 'DSG_PLUGIN_URL' ) ) {
	define( 'DSG_PLUGIN_URL', 'http://example.com/wp-content/plugins/dynamic-schema-generator/' );
}
if ( ! defined( 'DSG_PLUGIN_BASENAME' ) ) {
	define( 'DSG_PLUGIN_BASENAME', 'dynamic-schema-generator/dynamic-schema-generator.php' );
}

// Mock WP_Post class
if ( ! class_exists( 'WP_Post' ) ) {
	class WP_Post {
		public $ID = 0;
		public $post_type = 'post';
		public $post_status = 'publish';
		public $post_title = '';
		public $post_author = 1;
		public $post_parent = 0;
	}
}

// Mock options and meta in-memory storage
global $mock_options, $mock_post_meta, $mock_posts;
$mock_options   = array();
$mock_post_meta = array();
$mock_posts     = array();

// Basic WordPress helper functions needed before loading plugin
if ( ! function_exists( 'plugin_dir_path' ) ) {
	function plugin_dir_path( $file ) {
		return dirname( $file ) . '/';
	}
}

if ( ! function_exists( 'plugin_dir_url' ) ) {
	function plugin_dir_url( $file ) {
		return 'https://example.com/wp-content/plugins/' . basename( dirname( $file ) ) . '/';
	}
}

if ( ! function_exists( 'plugin_basename' ) ) {
	function plugin_basename( $file ) {
		return basename( dirname( $file ) ) . '/' . basename( $file );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'register_activation_hook' ) ) {
	function register_activation_hook( $file, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'register_deactivation_hook' ) ) {
	function register_deactivation_hook( $file, $callback ) {
		return true;
	}
}

if ( ! function_exists( 'load_plugin_textdomain' ) ) {
	function load_plugin_textdomain( $domain, $deprecated = false, $plugin_rel_path = false ) {
		return true;
	}
}

// WordPress Mock Functions
if ( ! function_exists( 'get_option' ) ) {
	function get_option( $key, $default = false ) {
		global $mock_options;
		return isset( $mock_options[ $key ] ) ? $mock_options[ $key ] : $default;
	}
}

if ( ! function_exists( 'update_option' ) ) {
	function update_option( $key, $value ) {
		global $mock_options;
		$mock_options[ $key ] = $value;
		return true;
	}
}

if ( ! function_exists( 'delete_option' ) ) {
	function delete_option( $key ) {
		global $mock_options;
		unset( $mock_options[ $key ] );
		return true;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	function get_post_meta( $post_id, $key, $single = false ) {
		global $mock_post_meta;
		if ( isset( $mock_post_meta[ $post_id ][ $key ] ) ) {
			return $mock_post_meta[ $post_id ][ $key ];
		}
		return $single ? '' : array();
	}
}

if ( ! function_exists( 'update_post_meta' ) ) {
	function update_post_meta( $post_id, $key, $value ) {
		global $mock_post_meta;
		$mock_post_meta[ $post_id ][ $key ] = $value;
		return true;
	}
}

if ( ! function_exists( 'delete_post_meta' ) ) {
	function delete_post_meta( $post_id, $key ) {
		global $mock_post_meta;
		unset( $mock_post_meta[ $post_id ][ $key ] );
		return true;
	}
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'https://example.com' . $path;
	}
}

if ( ! function_exists( 'get_bloginfo' ) ) {
	function get_bloginfo( $show = 'name' ) {
		if ( 'description' === $show ) {
			return 'Example Marketing Agency & Consulting';
		}
		return 'Example Brand';
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	function wp_parse_args( $args, $defaults = array() ) {
		return array_merge( (array) $defaults, (array) $args );
	}
}

if ( ! function_exists( 'trailingslashit' ) ) {
	function trailingslashit( $string ) {
		return rtrim( $string, '/\\' ) . '/';
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return trim( strip_tags( (string) $str ) );
	}
}

if ( ! function_exists( 'sanitize_textarea_field' ) ) {
	function sanitize_textarea_field( $str ) {
		return trim( strip_tags( (string) $str ) );
	}
}

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) );
	}
}

if ( ! function_exists( 'sanitize_email' ) ) {
	function sanitize_email( $email ) {
		return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : '';
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	function esc_url_raw( $url ) {
		return filter_var( $url, FILTER_VALIDATE_URL ) ? $url : '';
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	function esc_url( $url ) {
		return htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'absint' ) ) {
	function absint( $maybeint ) {
		return abs( intval( $maybeint ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $string ) {
		return trim( strip_tags( (string) $string ) );
	}
}

if ( ! function_exists( 'wp_kses_post' ) ) {
	function wp_kses_post( $string ) {
		return strip_tags( (string) $string, '<p><a><br><strong><em><ul><ol><li>' );
	}
}

if ( ! function_exists( 'wp_generate_password' ) ) {
	function wp_generate_password( $length = 12 ) {
		return substr( md5( uniqid( (string) mt_rand(), true ) ), 0, $length );
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	function wp_json_encode( $data, $options = 0 ) {
		return json_encode( $data, $options );
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $tag, $value ) {
		return $value;
	}
}

if ( ! function_exists( 'did_action' ) ) {
	function did_action( $tag ) {
		return false;
	}
}

if ( ! function_exists( 'is_singular' ) ) {
	function is_singular() {
		return false;
	}
}

if ( ! function_exists( 'is_page' ) ) {
	function is_page( $post = null ) {
		return false;
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return false;
	}
}

if ( ! function_exists( 'is_feed' ) ) {
	function is_feed() {
		return false;
	}
}

if ( ! function_exists( 'is_preview' ) ) {
	function is_preview() {
		return false;
	}
}

if ( ! function_exists( 'is_embed' ) ) {
	function is_embed() {
		return false;
	}
}

if ( ! function_exists( '__' ) ) {
	function __( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( '_e' ) ) {
	function _e( $text, $domain = 'default' ) {
		echo $text;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'get_the_ID' ) ) {
	function get_the_ID() {
		return 0;
	}
}

if ( ! function_exists( 'get_post' ) ) {
	function get_post( $post_id ) {
		global $mock_posts;
		return isset( $mock_posts[ $post_id ] ) ? $mock_posts[ $post_id ] : null;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = 0 ) {
		$id = is_object( $post ) ? $post->ID : absint( $post );
		return 'https://example.com/?p=' . $id;
	}
}

if ( ! function_exists( 'get_the_title' ) ) {
	function get_the_title( $post = 0 ) {
		$id = is_object( $post ) ? $post->ID : absint( $post );
		return 'Sample Page Title ' . $id;
	}
}

if ( ! function_exists( 'get_the_date' ) ) {
	function get_the_date( $format = 'c', $post = null ) {
		return '2026-01-15T10:00:00+00:00';
	}
}

if ( ! function_exists( 'get_the_modified_date' ) ) {
	function get_the_modified_date( $format = 'c', $post = null ) {
		return '2026-02-01T15:30:00+00:00';
	}
}

if ( ! function_exists( 'get_the_author_meta' ) ) {
	function get_the_author_meta( $field, $user_id = null ) {
		return 'John Doe';
	}
}

if ( ! function_exists( 'get_author_posts_url' ) ) {
	function get_author_posts_url( $author_id ) {
		return 'https://example.com/author/john-doe/';
	}
}

if ( ! function_exists( 'has_excerpt' ) ) {
	function has_excerpt( $post = null ) {
		return false;
	}
}

if ( ! function_exists( 'get_the_excerpt' ) ) {
	function get_the_excerpt( $post = null ) {
		return '';
	}
}

if ( ! function_exists( 'has_post_thumbnail' ) ) {
	function has_post_thumbnail( $post = null ) {
		return false;
	}
}

if ( ! function_exists( 'get_the_post_thumbnail_url' ) ) {
	function get_the_post_thumbnail_url( $post = null, $size = 'full' ) {
		return '';
	}
}

if ( ! function_exists( 'get_post_ancestors' ) ) {
	function get_post_ancestors( $post ) {
		return array();
	}
}

if ( ! function_exists( 'get_the_category' ) ) {
	function get_the_category( $post_id = false ) {
		return array();
	}
}

if ( ! function_exists( 'current_time' ) ) {
	function current_time( $type ) {
		return date( 'Y-m-d H:i:s' );
	}
}

// Now load the main plugin file
require_once DSG_PLUGIN_DIR . 'dynamic-schema-generator.php';
