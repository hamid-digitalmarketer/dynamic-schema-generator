<?php
/**
 * Tests for Schema_Validator.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Schema_Validator;

/**
 * Class Test_Schema_Validation
 */
class Test_Schema_Validation extends TestCase {

	public function test_validate_valid_payload() {
		$valid_json = json_encode( array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'Organization',
					'@id'   => 'https://example.com/#org',
					'name'  => 'Example Corp',
					'url'   => 'https://example.com',
				),
				array(
					'@type' => 'WebSite',
					'@id'   => 'https://example.com/#website',
					'name'  => 'Example Corp Website',
					'url'   => 'https://example.com',
				),
			),
		) );

		$result = Schema_Validator::validate( $valid_json );

		$this->assertTrue( $result['valid'] );
		$this->assertEmpty( $result['errors'] );
		$this->assertContains( 'Organization', $result['types'] );
		$this->assertContains( 'WebSite', $result['types'] );
	}

	public function test_validate_malformed_json_syntax() {
		$bad_json = '{ "@context": "https://schema.org", "name": "Broken JSON" '; // Missing closing brace

		$result = Schema_Validator::validate( $bad_json );

		$this->assertFalse( $result['valid'] );
		$this->assertNotEmpty( $result['errors'] );
		$this->assertStringContainsString( 'JSON Syntax Error', $result['errors'][0] );
	}

	public function test_validate_missing_type_error() {
		$missing_type = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'name' => 'Nameless Entity with no type',
				),
			),
		);

		$result = Schema_Validator::validate( $missing_type );

		$this->assertFalse( $result['valid'] );
		$this->assertStringContainsString( "missing required '@type'", $result['errors'][0] );
	}

	public function test_validate_detects_duplicate_id_warning() {
		$duplicate_id = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'Organization',
					'@id'   => 'https://example.com/#dup',
					'name'  => 'Org One',
					'url'   => 'https://example.com',
				),
				array(
					'@type' => 'LocalBusiness',
					'@id'   => 'https://example.com/#dup',
					'name'  => 'Org Two with same ID',
					'url'   => 'https://example.com',
				),
			),
		);

		$result = Schema_Validator::validate( $duplicate_id );

		$this->assertNotEmpty( $result['warnings'] );
		$all_warnings = implode( ' ', $result['warnings'] );
		$this->assertStringContainsString( "Duplicate entity '@id' detected", $all_warnings );
	}

	public function test_validate_faq_requires_main_entity() {
		$bad_faq = array(
			'@context' => 'https://schema.org',
			'@graph'   => array(
				array(
					'@type' => 'FAQPage',
					'name'  => 'Empty FAQ',
				),
			),
		);

		$result = Schema_Validator::validate( $bad_faq );

		$this->assertFalse( $result['valid'] );
		$this->assertStringContainsString( "missing required 'mainEntity'", $result['errors'][0] );
	}
}
