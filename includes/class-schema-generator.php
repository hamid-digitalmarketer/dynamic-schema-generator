<?php
/**
 * Core schema generator engine.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

use DSG\Integrations\Elementor_Adapter;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Schema_Generator
 */
class Schema_Generator {

	/**
	 * Settings cache.
	 *
	 * @var array
	 */
	protected $settings;

	/**
	 * Business info cache.
	 *
	 * @var array
	 */
	protected $business;

	/**
	 * Elementor adapter.
	 *
	 * @var Elementor_Adapter
	 */
	protected $elementor_adapter;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->settings          = Settings::get_all();
		$this->business          = Business_Manager::get_info();
		$this->elementor_adapter = new Elementor_Adapter();
	}

	/**
	 * Generate complete schema graph for the current query context or specific post.
	 *
	 * @param int|null $post_id Optional post ID.
	 * @return array Schema graph with '@context' and '@graph'.
	 */
	public function generate( $post_id = null ) {
		if ( empty( $this->settings['enable_schema'] ) ) {
			return array();
		}

		if ( null === $post_id ) {
			if ( is_singular() ) {
				$post_id = get_the_ID();
			}
		}

		$post_id = absint( $post_id );

		// Check exclusion list
		if ( $post_id && in_array( $post_id, (array) $this->settings['excluded_posts'], true ) ) {
			return array();
		}

		// Check post-level exclusion meta
		if ( $post_id && get_post_meta( $post_id, '_dsg_exclude_schema', true ) ) {
			return array();
		}

		$graph = array();

		// 1. Business / Organization schema
		$biz_entity = $this->build_business_entity();
		if ( ! empty( $biz_entity ) ) {
			$graph[] = $biz_entity;
		}

		// 2. WebSite schema
		if ( ! empty( $this->settings['enable_website'] ) ) {
			$site_entity = $this->build_website_entity();
			if ( ! empty( $site_entity ) ) {
				$graph[] = $site_entity;
			}
		}

		// Context-specific entities if on a singular post/page
		if ( $post_id ) {
			$post = get_post( $post_id );
			if ( $post && 'publish' === $post->post_status ) {

				// 3. WebPage entity
				$page_entity = $this->build_webpage_entity( $post );
				if ( ! empty( $page_entity ) ) {
					$graph[] = $page_entity;
				}

				// 4. BreadcrumbList entity
				if ( ! empty( $this->settings['enable_breadcrumbs'] ) ) {
					$breadcrumb_entity = $this->build_breadcrumb_entity( $post );
					if ( ! empty( $breadcrumb_entity ) ) {
						$graph[] = $breadcrumb_entity;
					}
				}

				// 5. Article or BlogPosting entity
				if ( ! empty( $this->settings['enable_articles'] ) && 'post' === $post->post_type ) {
					$article_entity = $this->build_article_entity( $post );
					if ( ! empty( $article_entity ) ) {
						$graph[] = $article_entity;
					}
				}

				// 6. Service entity (if mapped to page)
				if ( ! empty( $this->settings['enable_services'] ) ) {
					$service_entity = $this->build_service_entity( $post_id );
					if ( ! empty( $service_entity ) ) {
						$graph[] = $service_entity;
					}
				}

				// 7. FAQPage entity
				if ( ! empty( $this->settings['enable_faqs'] ) ) {
					$faq_entity = $this->build_faq_entity( $post_id );
					if ( ! empty( $faq_entity ) ) {
						$graph[] = $faq_entity;
					}
				}
			}
		}

		if ( empty( $graph ) ) {
			return array();
		}

		return array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);
	}

	/**
	 * Build Organization or LocalBusiness entity.
	 *
	 * @return array|null
	 */
	public function build_business_entity() {
		$biz = $this->business;
		$use_local = ! empty( $this->settings['enable_local_business'] );

		if ( ! $use_local && empty( $this->settings['enable_organization'] ) ) {
			return null;
		}

		$site_url = ! empty( $biz['url'] ) ? esc_url( $biz['url'] ) : home_url( '/' );
		$type     = $use_local && ! empty( $biz['business_type'] ) ? $biz['business_type'] : 'Organization';

		$entity = array(
			'@type' => $type,
			'@id'   => trailingslashit( $site_url ) . '#organization',
			'name'  => ! empty( $biz['name'] ) ? $biz['name'] : get_bloginfo( 'name' ),
			'url'   => $site_url,
		);

		if ( ! empty( $biz['legal_name'] ) ) {
			$entity['legalName'] = $biz['legal_name'];
		}

		if ( ! empty( $biz['description'] ) ) {
			$entity['description'] = $biz['description'];
		}

		if ( ! empty( $biz['logo_url'] ) ) {
			$entity['logo'] = array(
				'@type' => 'ImageObject',
				'@id'   => trailingslashit( $site_url ) . '#logo',
				'url'   => esc_url( $biz['logo_url'] ),
			);
			$entity['image'] = array( '@id' => trailingslashit( $site_url ) . '#logo' );
		}

		if ( ! empty( $biz['telephone'] ) ) {
			$entity['telephone'] = $biz['telephone'];
		}

		if ( ! empty( $biz['email'] ) ) {
			$entity['email'] = $biz['email'];
		}

		if ( ! empty( $biz['same_as'] ) ) {
			$entity['sameAs'] = (array) $biz['same_as'];
		}

		if ( ! empty( $biz['area_served'] ) ) {
			$entity['areaServed'] = $biz['area_served'];
		}

		// PostalAddress for LocalBusiness or Organization with physical address
		if ( ! empty( $biz['street_address'] ) || ! empty( $biz['address_locality'] ) || ! empty( $biz['postal_code'] ) ) {
			$address = array( '@type' => 'PostalAddress' );
			if ( ! empty( $biz['street_address'] ) ) {
				$address['streetAddress'] = $biz['street_address'];
			}
			if ( ! empty( $biz['address_locality'] ) ) {
				$address['addressLocality'] = $biz['address_locality'];
			}
			if ( ! empty( $biz['address_region'] ) ) {
				$address['addressRegion'] = $biz['address_region'];
			}
			if ( ! empty( $biz['postal_code'] ) ) {
				$address['postalCode'] = $biz['postal_code'];
			}
			if ( ! empty( $biz['address_country'] ) ) {
				$address['addressCountry'] = $biz['address_country'];
			}
			$entity['address'] = $address;
		}

		// GeoCoordinates
		if ( ! empty( $biz['latitude'] ) && ! empty( $biz['longitude'] ) ) {
			$entity['geo'] = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => (float) $biz['latitude'],
				'longitude' => (float) $biz['longitude'],
			);
		}

		// Opening hours for LocalBusiness
		if ( ! empty( $biz['opening_hours'] ) && is_array( $biz['opening_hours'] ) ) {
			$specs = array();
			foreach ( $biz['opening_hours'] as $h ) {
				if ( ! empty( $h['day'] ) && ! empty( $h['opens'] ) && ! empty( $h['closes'] ) ) {
					$specs[] = array(
						'@type'     => 'OpeningHoursSpecification',
						'dayOfWeek' => $h['day'],
						'opens'     => $h['opens'],
						'closes'    => $h['closes'],
					);
				}
			}
			if ( ! empty( $specs ) ) {
				$entity['openingHoursSpecification'] = $specs;
			}
		}

		if ( ! empty( $biz['price_range'] ) ) {
			$entity['priceRange'] = $biz['price_range'];
		}

		return $entity;
	}

	/**
	 * Build WebSite entity.
	 *
	 * @return array
	 */
	public function build_website_entity() {
		$site_url = home_url( '/' );

		return array(
			'@type'           => 'WebSite',
			'@id'             => trailingslashit( $site_url ) . '#website',
			'url'             => $site_url,
			'name'            => get_bloginfo( 'name' ),
			'description'     => get_bloginfo( 'description' ),
			'publisher'       => array(
				'@id' => trailingslashit( $site_url ) . '#organization',
			),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => trailingslashit( $site_url ) . '?s={search_term_string}',
				'query-input' => 'required name=search_term_string',
			),
		);
	}

	/**
	 * Build WebPage entity.
	 *
	 * @param \WP_Post $post Post object.
	 * @return array
	 */
	public function build_webpage_entity( $post ) {
		$permalink = get_permalink( $post );
		$site_url  = home_url( '/' );

		$entity = array(
			'@type'      => 'WebPage',
			'@id'        => $permalink . '#webpage',
			'url'        => $permalink,
			'name'       => get_the_title( $post ),
			'isPartOf'   => array(
				'@id' => trailingslashit( $site_url ) . '#website',
			),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
		);

		if ( has_excerpt( $post ) ) {
			$entity['description'] = wp_strip_all_tags( get_the_excerpt( $post ) );
		}

		if ( ! empty( $this->settings['enable_breadcrumbs'] ) ) {
			$entity['breadcrumb'] = array(
				'@id' => $permalink . '#breadcrumb',
			);
		}

		return $entity;
	}

	/**
	 * Build BreadcrumbList entity.
	 *
	 * @param \WP_Post $post Post object.
	 * @return array
	 */
	public function build_breadcrumb_entity( $post ) {
		$permalink = get_permalink( $post );
		$items     = array();

		// Home position 1
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => 1,
			'name'     => __( 'Home', 'dynamic-schema-generator' ),
			'item'     => home_url( '/' ),
		);

		$pos = 2;

		// Post ancestors if page
		if ( is_page( $post ) && $post->post_parent ) {
			$ancestors = array_reverse( get_post_ancestors( $post ) );
			foreach ( $ancestors as $ancestor_id ) {
				$items[] = array(
					'@type'    => 'ListItem',
					'position' => $pos,
					'name'     => get_the_title( $ancestor_id ),
					'item'     => get_permalink( $ancestor_id ),
				);
				$pos++;
			}
		}

		// Post categories if blog post
		if ( 'post' === $post->post_type ) {
			$categories = get_the_category( $post->ID );
			if ( ! empty( $categories ) ) {
				$first_cat = $categories[0];
				$cat_link  = get_category_link( $first_cat->term_id );
				if ( $cat_link ) {
					$items[] = array(
						'@type'    => 'ListItem',
						'position' => $pos,
						'name'     => $first_cat->name,
						'item'     => $cat_link,
					);
					$pos++;
				}
			}
		}

		// Current post as last item
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => get_the_title( $post ),
			'item'     => $permalink,
		);

		return array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $permalink . '#breadcrumb',
			'itemListElement' => $items,
		);
	}

	/**
	 * Build Article or BlogPosting entity.
	 *
	 * @param \WP_Post $post Post object.
	 * @return array
	 */
	public function build_article_entity( $post ) {
		$permalink   = get_permalink( $post );
		$site_url    = home_url( '/' );
		$author_id   = $post->post_author;
		$author_name = get_the_author_meta( 'display_name', $author_id );

		$entity = array(
			'@type'         => 'Article',
			'@id'           => $permalink . '#article',
			'isPartOf'      => array(
				'@id' => $permalink . '#webpage',
			),
			'headline'      => get_the_title( $post ),
			'datePublished' => get_the_date( 'c', $post ),
			'dateModified'  => get_the_modified_date( 'c', $post ),
			'mainEntityOfPage' => array(
				'@type' => 'WebPage',
				'@id'   => $permalink,
			),
			'author'        => array(
				'@type' => 'Person',
				'name'  => $author_name ? $author_name : 'Staff',
				'url'   => get_author_posts_url( $author_id ),
			),
			'publisher'     => array(
				'@id' => trailingslashit( $site_url ) . '#organization',
			),
		);

		if ( has_excerpt( $post ) ) {
			$entity['description'] = wp_strip_all_tags( get_the_excerpt( $post ) );
		}

		if ( has_post_thumbnail( $post ) ) {
			$thumb_url = get_the_post_thumbnail_url( $post, 'full' );
			if ( $thumb_url ) {
				$entity['image'] = array(
					'@type' => 'ImageObject',
					'url'   => esc_url( $thumb_url ),
				);
			}
		}

		return $entity;
	}

	/**
	 * Build Service entity if configured for this page.
	 *
	 * @param int $post_id Post ID.
	 * @return array|null
	 */
	public function build_service_entity( $post_id ) {
		$service = Service_Manager::get_by_page_id( $post_id );
		if ( empty( $service ) || empty( $service['name'] ) ) {
			return null;
		}

		$site_url = home_url( '/' );
		$entity   = array(
			'@type'       => 'Service',
			'@id'         => get_permalink( $post_id ) . '#service',
			'name'        => $service['name'],
			'description' => ! empty( $service['description'] ) ? $service['description'] : '',
			'provider'    => array(
				'@id' => trailingslashit( $site_url ) . '#organization',
			),
		);

		if ( ! empty( $service['service_type'] ) ) {
			$entity['serviceType'] = $service['service_type'];
		}

		if ( ! empty( $service['area_served'] ) ) {
			$entity['areaServed'] = $service['area_served'];
		}

		if ( ! empty( $service['image_url'] ) ) {
			$entity['image'] = esc_url( $service['image_url'] );
		}

		if ( ! empty( $service['price'] ) ) {
			$entity['offers'] = array(
				'@type'         => 'Offer',
				'price'         => $service['price'],
				'priceCurrency' => ! empty( $service['currency'] ) ? $service['currency'] : 'USD',
			);
		}

		return $entity;
	}

	/**
	 * Build FAQPage entity from manual post FAQs or Elementor extraction.
	 *
	 * @param int $post_id Post ID.
	 * @return array|null
	 */
	public function build_faq_entity( $post_id ) {
		// 1. Check manual post FAQs
		$faqs = FAQ_Manager::get_by_post_id( $post_id );

		// 2. If empty and Elementor adapter enabled, attempt automatic widget extraction
		if ( empty( $faqs ) && ! empty( $this->settings['enable_elementor_adapter'] ) ) {
			if ( $this->elementor_adapter->is_active() && $this->elementor_adapter->is_built_with( $post_id ) ) {
				$extracted = $this->elementor_adapter->extract_faqs( $post_id );
				if ( ! empty( $extracted ) ) {
					$faqs = $extracted;
				}
			}
		}

		if ( empty( $faqs ) ) {
			return null;
		}

		$main_entity = array();
		foreach ( $faqs as $item ) {
			if ( empty( $item['question'] ) || empty( $item['answer'] ) ) {
				continue;
			}
			$main_entity[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $item['question'] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => wp_kses_post( $item['answer'] ),
				),
			);
		}

		if ( empty( $main_entity ) ) {
			return null;
		}

		return array(
			'@type'      => 'FAQPage',
			'@id'        => get_permalink( $post_id ) . '#faq',
			'mainEntity' => $main_entity,
		);
	}
}
