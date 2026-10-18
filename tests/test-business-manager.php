<?php
/**
 * Tests for Business_Manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Business_Manager;

/**
 * Class Test_Business_Manager
 */
class Test_Business_Manager extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_options;
		$mock_options = array();
	}

	public function test_save_and_retrieve_business_info() {
		$data = array(
			'business_type'    => 'ProfessionalService',
			'name'             => 'Pro Event Solutions',
			'telephone'        => '+92-316-7417135',
			'email'            => 'info@example.com',
			'street_address'   => 'Bahria Complex',
			'address_locality' => 'Lahore',
			'address_country'  => 'pk',
			'latitude'         => '31.5204',
			'longitude'        => '74.3587',
			'same_as'          => "https://linkedin.com/company/example\nhttps://github.com/hamid-digitalmarketer\ninvalid-url-drop",
		);

		Business_Manager::save_info( $data );
		$saved = Business_Manager::get_info();

		$this->assertEquals( 'ProfessionalService', $saved['business_type'] );
		$this->assertEquals( 'Pro Event Solutions', $saved['name'] );
		$this->assertEquals( 'PK', $saved['address_country'] ); // Uppercase ISO
		$this->assertEquals( '31.5204', $saved['latitude'] );
		$this->assertEquals( '74.3587', $saved['longitude'] );

		// Invalid URL filtered out
		$this->assertCount( 2, $saved['same_as'] );
		$this->assertContains( 'https://linkedin.com/company/example', $saved['same_as'] );
		$this->assertContains( 'https://github.com/hamid-digitalmarketer', $saved['same_as'] );
	}

	public function test_opening_hours_validation() {
		$data = array(
			'opening_hours' => array(
				array( 'day' => 'Monday', 'opens' => '09:00', 'closes' => '17:00' ),
				array( 'day' => 'InvalidDay', 'opens' => '09:00', 'closes' => '17:00' ), // Ignored
				array( 'day' => 'Tuesday', 'opens' => 'invalid-time', 'closes' => '17:00' ), // Ignored
				array( 'day' => 'Wednesday', 'opens' => '08:30', 'closes' => '18:00' ),
			),
		);

		Business_Manager::save_info( $data );
		$saved = Business_Manager::get_info();

		$this->assertCount( 2, $saved['opening_hours'] );
		$this->assertEquals( 'Monday', $saved['opening_hours'][0]['day'] );
		$this->assertEquals( 'Wednesday', $saved['opening_hours'][1]['day'] );
	}
}
