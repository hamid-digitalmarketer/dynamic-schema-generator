=== Dynamic Schema Generator for WordPress ===
Contributors: hamid-digitalmarketer
Donate link: https://hamid-resume.web.app/
Tags: schema, json-ld, structured data, seo, elementor, local business, faq schema, rich snippets, geo
Requires at least: 5.8
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 1.0.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Automated, production-grade JSON-LD Schema.org structured data engine for WordPress and Elementor layouts with zero bloat and full conflict detection.

== Description ==

Dynamic Schema Generator is an enterprise-grade WordPress plugin that automatically generates accurate, interconnected Schema.org JSON-LD structured data for search engines (Google, Bing) and AI discovery models (Gemini, ChatGPT, Perplexity).

Modern search experiences are moving toward AI Overviews and answer engines. To ensure your brand, services, and content are accurately understood without hallucination, your website requires clean, machine-readable Knowledge Graph entities.

Unlike heavy, bloated SEO suites that slow down page loads, Dynamic Schema Generator focuses exclusively on high-precision structured data, emitting valid `@graph` payloads with zero frontend styling bloat.

= Key Features =

* **Automatic Entity Graph:** Emits interconnected `Organization`, `LocalBusiness`, `WebSite`, `WebPage`, `Article`, `Service`, `BreadcrumbList`, and `FAQPage` schemas.
* **Elementor Integration:** Traverses Elementor document data trees to extract FAQ Accordion and Toggle widgets automatically without frontend performance penalty.
* **Business Information Manager:** Full control over NAP (Name, Address, Phone), geographic coordinates, opening hours, sameAs social links, and price range.
* **Service Schema Builder:** Map professional service offerings, descriptions, service areas, and transparent pricing to individual landing pages.
* **FAQ Schema Generator:** Manage verified Q&A pairs at the site level or per page, respecting Google structured data policies.
* **In-House Validation & Live Preview:** Inspect live JSON-LD for any URL and run syntax validation before publishing.
* **Conflict Safeguards:** Detects and respects existing schema outputs from Yoast SEO, Rank Math, AIOSEO, and SEOPress.

= Important Note =

Valid structured data helps search engines interpret and classify website entities. Search engines decide independently when and how to display rich results, local packs, or AI Overview citations.

== Installation ==

1. Upload the `dynamic-schema-generator` folder to your `/wp-content/plugins/` directory, or install the `.zip` archive via Plugins > Add New > Upload Plugin.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Dynamic Schema** in your WordPress admin menu.
4. Fill in your business details under **Business Info**.
5. Enable desired schema modules under **Schema Settings**.
6. Preview and test your JSON-LD under **Schema Preview**.

== Frequently Asked Questions ==

= Does this plugin work without Elementor? =
Yes. The plugin operates fully on standard WordPress posts, pages, and custom post types. Elementor integration is an optional adapter that activates only when Elementor is detected.

= Does this plugin guarantee Google rich snippets or AI Overviews? =
No plugin can guarantee rich snippets or AI inclusions. Dynamic Schema Generator produces strictly valid, policy-compliant Schema.org markup to give search engines the cleanest possible signals.

= Can I disable schema for a specific page? =
Yes. Use the Dynamic Schema Generator meta box on any post or page edit screen to check "Disable Schema Output for this page".

== Screenshots ==

1. Admin Dashboard with status metrics, setup checklist, and AI Search Readiness guide.
2. Business Information management with full NAP, coordinates, and sameAs links.
3. Schema Preview and In-House Validation engine with one-click copy.
4. Service Schema Builder mapping services to individual landing pages.

== Changelog ==

= 1.0.0 =
* Initial open-source release.
* Automated Organization, LocalBusiness, WebSite, Breadcrumbs, Article, Service, and FAQPage schemas.
* Elementor Accordion and Toggle widget auto-extraction.
* In-house JSON-LD validation and live preview tool.
* SEO plugin conflict detection.
