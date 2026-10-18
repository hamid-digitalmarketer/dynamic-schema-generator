<?php
/**
 * Tests for FAQ_Manager.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG\Tests;

use PHPUnit\Framework\TestCase;
use DSG\FAQ_Manager;

/**
 * Class Test_FAQ_Manager
 */
class Test_FAQ_Manager extends TestCase {

	protected function setUp(): void {
		parent::setUp();
		global $mock_options, $mock_post_meta;
		$mock_options   = array();
		$mock_post_meta = array();
	}

	public function test_save_and_deduplicate_faqs() {
		$raw = array(
			array( 'question' => 'What is Schema Markup?', 'answer' => 'Structured code for search engines.' ),
			array( 'question' => 'What is Schema Markup?', 'answer' => 'Duplicate question with another answer.' ),
			array( 'question' => '', 'answer' => 'Empty question will be dropped.' ),
			array( 'question' => 'How does GEO work?', 'answer' => 'Generative Engine Optimization structures entities.' ),
		);

		$clean = FAQ_Manager::sanitize_faq_list( $raw );

		$this->assertCount( 2, $clean );
		$this->assertEquals( 'What is Schema Markup?', $clean[0]['question'] );
		$this->assertEquals( 'How does GEO work?', $clean[1]['question'] );
	}

	public function test_save_post_faqs() {
		$post_id = 101;
		$faqs = array(
			array( 'question' => 'Is this post covered?', 'answer' => 'Yes, mapped to post 101.' ),
		);

		FAQ_Manager::save_post_faqs( $post_id, $faqs );
		$retrieved = FAQ_Manager::get_by_post_id( $post_id );

		$this->assertCount( 1, $retrieved );
		$this->assertEquals( 'Is this post covered?', $retrieved[0]['question'] );
	}
}
