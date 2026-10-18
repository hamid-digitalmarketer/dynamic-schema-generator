<?php
/**
 * Tests for Schema_Generator.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Schema_Generator;
use DSG\Settings;
use DSG\Business_Manager;
use DSG\Service_Manager;

/**
 * Class Test_Schema_Generator
 */
class Test_Schema_Generator extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_options, $mock_post_meta, $mock_posts;
		$mock_options   = array();
		$mock_post_meta = array();
		$mock_posts     = array();

		\DSG\Activator::activate();
	}

	public function test_generate_default_organization_and_website() {
		$generator = new Schema_Generator();
		$schema    = $generator->generate();

		$this->assertIsArray( $schema );
		$this->assertEquals( 'https://schema.org', $schema['@context'] );
		$this->assertNotEmpty( $schema['@graph'] );

		$types = array_column( $schema['@graph'], '@type' );
		$this->assertContains( 'Organization', $types );
		$this->assertContains( 'WebSite', $types );
	}

	public function test_generate_local_business_when_enabled() {
		Settings::update( array(
			'enable_schema'         => true,
			'enable_local_business' => true,
		) );

		Business_Manager::save_info( array(
			'business_type'    => 'LocalBusiness',
			'name'             => 'Hamid SEO Services',
			'street_address'   => 'Bahria Complex, Mall Road',
			'address_locality' => 'Lahore',
			'address_country'  => 'PK',
			'telephone'        => '+923167417135',
		) );

		$generator = new Schema_Generator();
		$schema    = $generator->generate();

		$types = array_column( $schema['@graph'], '@type' );
		$this->assertContains( 'LocalBusiness', $types );

		$biz = null;
		foreach ( $schema['@graph'] as $entity ) {
			if ( 'LocalBusiness' === $entity['@type'] ) {
				$biz = $entity;
				break;
			}
		}

		$this->assertNotNull( $biz );
		$this->assertEquals( 'Hamid SEO Services', $biz['name'] );
		$this->assertEquals( 'Bahria Complex, Mall Road', $biz['address']['streetAddress'] );
		$this->assertEquals( 'PK', $biz['address']['addressCountry'] );
	}

	public function test_generate_service_entity_when_mapped() {
		global $mock_posts;

		$post_id = 42;
		$mock_post = new \WP_Post();
		$mock_post->ID          = $post_id;
		$mock_post->post_type   = 'page';
		$mock_post->post_status = 'publish';
		$mock_post->post_title  = 'Enterprise Technical SEO Audit';
		$mock_posts[ $post_id ] = $mock_post;

		Service_Manager::save( array(
			'name'        => 'Forensic SEO Audit',
			'description' => 'Comprehensive technical crawl, canonical loops, and indexation audit.',
			'page_id'     => $post_id,
			'price'       => '999.00',
			'currency'    => 'USD',
		) );

		$generator = new Schema_Generator();
		$schema    = $generator->generate( $post_id );

		$types = array_column( $schema['@graph'], '@type' );
		$this->assertContains( 'Service', $types );

		$service = null;
		foreach ( $schema['@graph'] as $entity ) {
			if ( 'Service' === $entity['@type'] ) {
				$service = $entity;
				break;
			}
		}

		$this->assertNotNull( $service );
		$this->assertEquals( 'Forensic SEO Audit', $service['name'] );
		$this->assertEquals( '999.00', $service['offers']['price'] );
		$this->assertEquals( 'USD', $service['offers']['priceCurrency'] );
	}

	public function test_exclusion_suppresses_schema() {
		Settings::update( array(
			'enable_schema'  => true,
			'excluded_posts' => array( 99 ),
		) );

		$generator = new Schema_Generator();
		$schema    = $generator->generate( 99 );

		$this->assertEmpty( $schema );
	}
}
