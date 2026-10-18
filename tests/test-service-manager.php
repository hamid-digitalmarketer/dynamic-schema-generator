<?php
/**
 * Tests for Service_Manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Service_Manager;

/**
 * Class Test_Service_Manager
 */
class Test_Service_Manager extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_options;
		$mock_options = array();
	}

	public function test_save_and_retrieve_service() {
		$id = Service_Manager::save( array(
			'name'        => 'PPC Waste Audit',
			'description' => 'Reclaims wasted spend in Google Ads.',
			'price'       => '1499.00',
			'currency'    => 'usd',
			'page_id'     => 55,
		) );

		$this->assertNotEmpty( $id );

		$service = Service_Manager::get_by_page_id( 55 );
		$this->assertNotNull( $service );
		$this->assertEquals( 'PPC Waste Audit', $service['name'] );
		$this->assertEquals( '1499.00', $service['price'] );
		$this->assertEquals( 'USD', $service['currency'] );

		// Delete
		$deleted = Service_Manager::delete( $id );
		$this->assertTrue( $deleted );
		$this->assertNull( Service_Manager::get_by_page_id( 55 ) );
	}

	public function test_empty_service_name_rejected() {
		$id = Service_Manager::save( array(
			'name'  => '',
			'price' => '200',
		) );

		$this->assertEmpty( $id );
	}
}
