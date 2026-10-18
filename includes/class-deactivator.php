<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 */
class Deactivator {

	/**
	 * Deactivate plugin tasks.
	 */
	public static function deactivate() {
		// Clean temporary transients.
		delete_transient( 'dsg_schema_cache' );
		delete_transient( 'dsg_active_conflicts' );
	}
}
