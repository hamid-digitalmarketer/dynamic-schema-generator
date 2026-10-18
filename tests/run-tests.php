<?php
/**
 * Standalone Zero-Dependency Test Runner for Dynamic Schema Generator.
 *
 * Runs all test cases with PHP CLI without requiring Composer or global PHPUnit.
 *
 * @package Dynamic_Schema_Generator
 */

namespace PHPUnit\Framework;

if ( ! class_exists( '\\PHPUnit\\Framework\\TestCase' ) ) {
	class TestCase {
		protected static $passed = 0;
		protected static $failed = 0;
		protected static $total_assertions = 0;

		public static function get_stats() {
			return array(
				'passed'     => self::$passed,
				'failed'     => self::$failed,
				'assertions' => self::$total_assertions,
			);
		}

		protected function setUp(): void {}
		protected function tearDown(): void {}

		protected function assertTrue( $condition, $message = '' ) {
			self::$total_assertions++;
			if ( ! $condition ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is true.' );
			}
		}

		protected function assertFalse( $condition, $message = '' ) {
			self::$total_assertions++;
			if ( $condition ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is false.' );
			}
		}

		protected function assertEquals( $expected, $actual, $message = '' ) {
			self::$total_assertions++;
			if ( $expected !== $actual ) {
				throw new \Exception( ( $message ? $message . "\n" : '' ) . "Expected: " . var_export( $expected, true ) . "\nActual: " . var_export( $actual, true ) );
			}
		}

		protected function assertIsArray( $actual, $message = '' ) {
			self::$total_assertions++;
			if ( ! is_array( $actual ) ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is an array.' );
			}
		}

		protected function assertNotEmpty( $actual, $message = '' ) {
			self::$total_assertions++;
			if ( empty( $actual ) ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is not empty.' );
			}
		}

		protected function assertEmpty( $actual, $message = '' ) {
			self::$total_assertions++;
			if ( ! empty( $actual ) ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is empty.' );
			}
		}

		protected function assertContains( $needle, $haystack, $message = '' ) {
			self::$total_assertions++;
			if ( ! is_array( $haystack ) || ! in_array( $needle, $haystack, true ) ) {
				throw new \Exception( $message ? $message : 'Failed asserting that array contains item: ' . var_export( $needle, true ) );
			}
		}

		protected function assertCount( $expected_count, $haystack, $message = '' ) {
			self::$total_assertions++;
			$actual = is_countable( $haystack ) ? count( $haystack ) : 0;
			if ( $actual !== $expected_count ) {
				throw new \Exception( "Failed asserting that count {$actual} matches expected {$expected_count}." );
			}
		}

		protected function assertNotNull( $actual, $message = '' ) {
			self::$total_assertions++;
			if ( null === $actual ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is not null.' );
			}
		}

		protected function assertNull( $actual, $message = '' ) {
			self::$total_assertions++;
			if ( null !== $actual ) {
				throw new \Exception( $message ? $message : 'Failed asserting that value is null.' );
			}
		}

		protected function assertStringContainsString( $needle, $haystack, $message = '' ) {
			self::$total_assertions++;
			if ( strpos( (string) $haystack, (string) $needle ) === false ) {
				throw new \Exception( "Failed asserting that '{$haystack}' contains '{$needle}'." );
			}
		}

		public static function record_pass() {
			self::$passed++;
		}

		public static function record_fail() {
			self::$failed++;
		}
	}
}

namespace DSG\Runner;

require_once __DIR__ . '/bootstrap.php';

$test_files = array(
	__DIR__ . '/test-settings.php',
	__DIR__ . '/test-business-manager.php',
	__DIR__ . '/test-service-manager.php',
	__DIR__ . '/test-faq-manager.php',
	__DIR__ . '/test-elementor-adapter.php',
	__DIR__ . '/test-schema-generator.php',
	__DIR__ . '/test-schema-validation.php',
);

echo "============================================================\n";
echo "Dynamic Schema Generator for WordPress - Test Suite Runner\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "============================================================\n\n";

$failures = array();

foreach ( $test_files as $file ) {
	require_once $file;

	$base = basename( $file, '.php' );
	$parts = explode( '-', $base );
	$capitalized = array_map( 'ucfirst', $parts );
	$class_name = 'DSG\\Tests\\' . implode( '_', $capitalized );

	if ( ! class_exists( $class_name ) ) {
		echo "Warning: Class {$class_name} not found.\n";
		continue;
	}

	$ref = new \ReflectionClass( $class_name );
	$test_instance = new $class_name();

	echo "Running " . $ref->getShortName() . "...\n";

	foreach ( $ref->getMethods( \ReflectionMethod::IS_PUBLIC ) as $method ) {
		if ( strpos( $method->name, 'test_' ) === 0 ) {
			if ( $ref->hasMethod( 'setUp' ) ) {
				$setUp = $ref->getMethod( 'setUp' );
				$setUp->setAccessible( true );
				$setUp->invoke( $test_instance );
			}

			try {
				$method->invoke( $test_instance );
				\PHPUnit\Framework\TestCase::record_pass();
				echo "  [PASS] " . $method->name . "\n";
			} catch ( \Throwable $t ) {
				\PHPUnit\Framework\TestCase::record_fail();
				$failures[] = array(
					'test'    => $ref->getShortName() . '::' . $method->name,
					'message' => $t->getMessage(),
					'file'    => $t->getFile() . ':' . $t->getLine(),
				);
				echo "  [FAIL] " . $method->name . " -> " . $t->getMessage() . "\n";
			}
		}
	}
	echo "\n";
}

$stats = \PHPUnit\Framework\TestCase::get_stats();

echo "============================================================\n";
echo "Test Execution Summary:\n";
echo "Total Tests: " . ( $stats['passed'] + $stats['failed'] ) . "\n";
echo "Passed:      " . $stats['passed'] . "\n";
echo "Failed:      " . $stats['failed'] . "\n";
echo "Assertions:  " . $stats['assertions'] . "\n";
echo "============================================================\n";

if ( ! empty( $failures ) ) {
	echo "\nFailures breakdown:\n";
	foreach ( $failures as $f ) {
		echo " - " . $f['test'] . " at " . $f['file'] . "\n";
		echo "   " . $f['message'] . "\n";
	}
	exit( 1 );
}

echo "\n[SUCCESS] All unit tests passed cleanly with 0 errors!\n";
exit( 0 );
