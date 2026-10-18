<?php
/**
 * Tests for Settings manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Settings;

/**
 * Class Test_Settings
 */
class Test_Settings extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_options;
		$mock_options = array();
	}

	public function test_get_defaults() {
		$settings = Settings::get_all();

		$this->assertTrue( $settings['enable_schema'] );
		$this->assertTrue( $settings['enable_organization'] );
		$this->assertFalse( $settings['enable_local_business'] );
		$this->assertContains( 'post', $settings['post_types'] );
		$this->assertContains( 'page', $settings['post_types'] );
	}

	public function test_update_and_sanitize_settings() {
		$input = array(
			'enable_schema'         => '1',
			'enable_local_business' => '1',
			'post_types'            => array( 'post', 'custom_service', 'drop;table' ),
			'excluded_posts'        => '10, 25, abc, 50',
			'minify_json'           => '1',
		);

		Settings::update( $input );

		$this->assertTrue( Settings::get( 'enable_local_business' ) );

		$types = Settings::get( 'post_types' );
		$this->assertContains( 'post', $types );
		$this->assertContains( 'custom_service', $types );
		$this->assertContains( 'droptable', $types ); // Sanitized key

		$excluded = Settings::get( 'excluded_posts' );
		$this->assertEquals( array( 10, 25, 50 ), array_values( $excluded ) );
	}
}
