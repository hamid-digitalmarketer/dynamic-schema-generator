<?php
/**
 * Tests for Elementor_Adapter.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\Integrations\Elementor_Adapter;

/**
 * Class Test_Elementor_Adapter
 */
class Test_Elementor_Adapter extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_post_meta;
		$mock_post_meta = array();
	}

	public function test_adapter_inactive_when_elementor_missing() {
		$adapter = new Elementor_Adapter();
		$this->assertFalse( $adapter->is_active() );
	}

	public function test_extract_faqs_from_elementor_accordion_data() {
		$post_id = 77;
		$elementor_payload = array(
			array(
				'elType'   => 'section',
				'elements' => array(
					array(
						'elType'   => 'column',
						'elements' => array(
							array(
								'elType'     => 'widget',
								'widgetType' => 'accordion',
								'settings'   => array(
									'tabs' => array(
										array(
											'tab_title'   => 'What is Local SEO?',
											'tab_content' => 'Optimizing your local presence in Google Maps.',
										),
										array(
											'tab_title'   => 'How to fix duplicate conversions?',
											'tab_content' => 'Use event_id deduplication across browser and server CAPI.',
										),
									),
								),
							),
							array(
								'elType'     => 'widget',
								'widgetType' => 'heading',
								'settings'   => array(
									'title' => 'Why Choose Our Agency',
								),
							),
						),
					),
				),
			),
		);

		update_post_meta( $post_id, '_elementor_data', json_encode( $elementor_payload ) );
		update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );

		$adapter = new Elementor_Adapter();
		$this->assertTrue( $adapter->is_built_with( $post_id ) );

		$faqs = $adapter->extract_faqs( $post_id );
		$this->assertCount( 2, $faqs );
		$this->assertEquals( 'What is Local SEO?', $faqs[0]['question'] );
		$this->assertEquals( 'How to fix duplicate conversions?', $faqs[1]['question'] );

		$headings = $adapter->extract_headings( $post_id );
		$this->assertContains( 'Why Choose Our Agency', $headings );
	}

	public function test_corrupted_data_handled_gracefully() {
		$post_id = 88;
		update_post_meta( $post_id, '_elementor_data', 'invalid{broken[json' );

		$adapter = new Elementor_Adapter();
		$faqs = $adapter->extract_faqs( $post_id );
		$this->assertEmpty( $faqs );
	}
}
