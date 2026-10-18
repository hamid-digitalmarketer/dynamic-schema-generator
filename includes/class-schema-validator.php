<?php
/**
 * Schema validator and linting engine.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Schema_Validator
 */
class Schema_Validator {

	/**
	 * Validate a JSON string or PHP array schema.
	 *
	 * @param string|array $schema Input schema.
	 * @return array Validation result containing 'valid' (bool), 'errors' (array), 'warnings' (array), 'types' (array).
	 */
	public static function validate( $schema ) {
		$errors   = array();
		$warnings = array();
		$types    = array();

		// 1. JSON syntax check if string.
		if ( is_string( $schema ) ) {
			$decoded = json_decode( $schema, true );
			if ( null === $decoded && JSON_ERROR_NONE !== json_last_error() ) {
				return array(
					'valid'    => false,
					'errors'   => array( 'JSON Syntax Error: ' . json_last_error_msg() ),
					'warnings' => array(),
					'types'    => array(),
				);
			}
		} elseif ( is_array( $schema ) ) {
			$decoded = $schema;
		} else {
			return array(
				'valid'    => false,
				'errors'   => array( 'Input must be a valid JSON string or PHP array.' ),
				'warnings' => array(),
				'types'    => array(),
			);
		}

		if ( empty( $decoded ) ) {
			return array(
				'valid'    => false,
				'errors'   => array( 'Schema payload is empty.' ),
				'warnings' => array(),
				'types'    => array(),
			);
		}

		// Check for @graph or single entity
		$entities = array();
		if ( isset( $decoded['@graph'] ) && is_array( $decoded['@graph'] ) ) {
			$entities = $decoded['@graph'];
		} else {
			$entities = array( $decoded );
		}

		$seen_ids   = array();
		$seen_types = array();

		foreach ( $entities as $index => $entity ) {
			if ( ! is_array( $entity ) ) {
				$errors[] = "Entity #{$index} is not a valid JSON-LD object.";
				continue;
			}

			// Context check
			$context = isset( $entity['@context'] ) ? $entity['@context'] : ( isset( $decoded['@context'] ) ? $decoded['@context'] : '' );
			if ( empty( $context ) || strpos( (string) $context, 'schema.org' ) === false ) {
				$warnings[] = "Entity #{$index} is missing or has non-standard '@context' (expected 'https://schema.org').";
			}

			// Type check
			if ( empty( $entity['@type'] ) ) {
				$errors[] = "Entity #{$index} is missing required '@type'.";
				continue;
			}

			$type    = is_array( $entity['@type'] ) ? implode( '/', $entity['@type'] ) : (string) $entity['@type'];
			$types[] = $type;

			// Duplicate @id check
			if ( ! empty( $entity['@id'] ) ) {
				$id = (string) $entity['@id'];
				if ( isset( $seen_ids[ $id ] ) ) {
					$warnings[] = "Duplicate entity '@id' detected: '{$id}'.";
				}
				$seen_ids[ $id ] = true;
			}

			// Check duplicate type in single page
			if ( in_array( $type, array( 'Organization', 'LocalBusiness', 'WebSite', 'FAQPage' ), true ) ) {
				if ( isset( $seen_types[ $type ] ) ) {
					$warnings[] = "Multiple instances of '{$type}' detected in the same schema payload. Google recommends consolidating single-entity types.";
				}
				$seen_types[ $type ] = true;
			}

			// Property validation by schema type
			self::validate_entity_properties( $entity, $type, $errors, $warnings );
		}

		return array(
			'valid'    => empty( $errors ),
			'errors'   => $errors,
			'warnings' => $warnings,
			'types'    => array_unique( $types ),
		);
	}

	/**
	 * Validate properties of an individual entity based on Schema.org expectations.
	 *
	 * @param array  $entity Entity data.
	 * @param string $type Schema type.
	 * @param array  $errors Reference to errors list.
	 * @param array  $warnings Reference to warnings list.
	 */
	protected static function validate_entity_properties( array $entity, $type, array &$errors, array &$warnings ) {
		switch ( $type ) {
			case 'Organization':
			case 'LocalBusiness':
			case 'ProfessionalService':
			case 'LegalService':
			case 'MedicalBusiness':
			case 'Store':
				if ( empty( $entity['name'] ) ) {
					$errors[] = "{$type} is missing required 'name' property.";
				}
				if ( empty( $entity['url'] ) ) {
					$warnings[] = "{$type} is missing recommended 'url' property.";
				}
				if ( empty( $entity['logo'] ) && empty( $entity['image'] ) ) {
					$warnings[] = "{$type} is missing recommended 'logo' or 'image' property.";
				}
				if ( 'Organization' !== $type && empty( $entity['address'] ) ) {
					$warnings[] = "{$type} is missing recommended 'address' (PostalAddress) for local search visibility.";
				}
				break;

			case 'WebSite':
				if ( empty( $entity['name'] ) ) {
					$errors[] = "WebSite is missing required 'name' property.";
				}
				if ( empty( $entity['url'] ) ) {
					$errors[] = "WebSite is missing required 'url' property.";
				}
				break;

			case 'Article':
			case 'BlogPosting':
			case 'NewsArticle':
				if ( empty( $entity['headline'] ) ) {
					$errors[] = "{$type} is missing required 'headline' property.";
				}
				if ( empty( $entity['author'] ) ) {
					$warnings[] = "{$type} is missing recommended 'author' property.";
				}
				if ( empty( $entity['datePublished'] ) ) {
					$warnings[] = "{$type} is missing recommended 'datePublished' property.";
				}
				break;

			case 'Service':
				if ( empty( $entity['name'] ) ) {
					$errors[] = "Service is missing required 'name' property.";
				}
				if ( empty( $entity['provider'] ) ) {
					$warnings[] = "Service is missing recommended 'provider' entity.";
				}
				break;

			case 'FAQPage':
				if ( empty( $entity['mainEntity'] ) || ! is_array( $entity['mainEntity'] ) ) {
					$errors[] = "FAQPage is missing required 'mainEntity' question list.";
				} else {
					foreach ( $entity['mainEntity'] as $idx => $q ) {
						if ( empty( $q['@type'] ) || 'Question' !== $q['@type'] ) {
							$warnings[] = "FAQ item #{$idx} missing '@type' of 'Question'.";
						}
						if ( empty( $q['name'] ) ) {
							$errors[] = "FAQ item #{$idx} missing question text ('name').";
						}
						if ( empty( $q['acceptedAnswer']['text'] ) ) {
							$errors[] = "FAQ item #{$idx} missing 'acceptedAnswer.text'.";
						}
					}
				}
				break;

			case 'BreadcrumbList':
				if ( empty( $entity['itemListElement'] ) || ! is_array( $entity['itemListElement'] ) ) {
					$errors[] = "BreadcrumbList missing 'itemListElement' entries.";
				}
				break;
		}
	}
}
