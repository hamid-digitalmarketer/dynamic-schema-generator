<?php
/**
 * Main plugin orchestrator.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Plugin
 */
class Plugin {

	/**
	 * Singleton instance.
	 *
	 * @var Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Admin controller.
	 *
	 * @var Admin
	 */
	protected $admin;

	/**
	 * Output controller.
	 *
	 * @var Schema_Output
	 */
	protected $output;

	/**
	 * Get singleton instance.
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Protected constructor.
	 */
	protected function __construct() {
		$this->admin  = new Admin();
		$this->output = new Schema_Output();
	}

	/**
	 * Execute plugin lifecycle.
	 */
	public function run() {
		$this->load_textdomain();

		if ( is_admin() ) {
			$this->admin->register_hooks();
		}

		$this->output->register_hooks();
	}

	/**
	 * Load plugin internationalization textdomain.
	 */
	protected function load_textdomain() {
		load_plugin_textdomain(
			'dynamic-schema-generator',
			false,
			dirname( DSG_PLUGIN_BASENAME ) . '/languages'
		);
	}
}
