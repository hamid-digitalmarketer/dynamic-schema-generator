<?php
/**
 * Plugin Documentation & Guide View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Documentation & Developer Guide', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Architectural concepts, filter hooks, and best practices for search engines & AI discovery.', 'dynamic-schema-generator' ); ?></p>
		</div>
		<div class="dsg-header-actions">
			<a href="https://wa.me/923167417135?text=Hello%20Hamid%2C%20I%20reviewed%20Dynamic%20Schema%20Generator%20and%20would%20like%20to%20hire%20you%20for%20an%20enterprise%20SEO%20and%20Schema%20architecture%20project." target="_blank" rel="noopener noreferrer" class="button button-primary dsg-btn-wa"><?php esc_html_e( 'Direct WhatsApp Inquiries (+92 316 7417135)', 'dynamic-schema-generator' ); ?></a>
		</div>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Understanding Entity Structured Data & GEO', 'dynamic-schema-generator' ); ?></h2>
		<p><?php esc_html_e( 'Modern search engines (Google, Bing) and Large Language Models (Gemini, ChatGPT, Perplexity) analyze structured web documents through Knowledge Graphs. When an AI search engine crawls your website, it attempts to resolve:', 'dynamic-schema-generator' ); ?></p>
		<ul class="dsg-bullet-list">
			<li><strong><?php esc_html_e( 'Who is the publisher / business?', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Resolved by Organization and LocalBusiness schema linked to authoritative external sameAs URLs.', 'dynamic-schema-generator' ); ?></li>
			<li><strong><?php esc_html_e( 'What is this document about?', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Resolved by WebPage and Article entities with exact headlines, published dates, and verified authors.', 'dynamic-schema-generator' ); ?></li>
			<li><strong><?php esc_html_e( 'What specific services or solutions are offered?', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Resolved by Service entities with clear provider links and explicit offer pricing.', 'dynamic-schema-generator' ); ?></li>
			<li><strong><?php esc_html_e( 'What are the answers to core customer questions?', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Resolved by FAQPage entities extracted from visible page content.', 'dynamic-schema-generator' ); ?></li>
		</ul>
		<p class="description">
			<em><?php esc_html_e( 'Important Note: Dynamic Schema Generator provides high-accuracy, valid structured data to maximize machine readability. Search engines decide independently when to trigger rich snippets or cite pages in AI summaries.', 'dynamic-schema-generator' ); ?></em>
		</p>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Developer Filter Hooks & Extensibility', 'dynamic-schema-generator' ); ?></h2>
		<p><?php esc_html_e( 'Developers can customize or augment any part of the generated schema using standard WordPress filters:', 'dynamic-schema-generator' ); ?></p>

		<h3><code>apply_filters( 'dsg_should_render_schema', bool $should_render )</code></h3>
		<p class="description"><?php esc_html_e( 'Conditionally suppress schema output based on custom logic or query variables.', 'dynamic-schema-generator' ); ?></p>
		<pre style="background:#f0f0f1;padding:10px;border-radius:4px;"><code>add_filter( 'dsg_should_render_schema', function( $should_render ) {
    if ( is_cart() || is_checkout() ) {
        return false; // Suppress on WooCommerce transactional pages
    }
    return $should_render;
} );</code></pre>

		<h3 style="margin-top:20px;"><code>apply_filters( 'dsg_schema_graph', array $graph )</code></h3>
		<p class="description"><?php esc_html_e( 'Modify, append, or enrich the complete @graph array before it is encoded into JSON-LD and injected into wp_head.', 'dynamic-schema-generator' ); ?></p>
		<pre style="background:#f0f0f1;padding:10px;border-radius:4px;"><code>add_filter( 'dsg_schema_graph', function( $graph ) {
    // Append a custom Product or Event entity to the graph
    $graph['@graph'][] = array(
        '@type' => 'SpecialAnnouncement',
        'name'  => 'Holiday Operating Hours Update',
    );
    return $graph;
} );</code></pre>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Author & Commercial Implementation Services', 'dynamic-schema-generator' ); ?></h2>
		<p><?php esc_html_e( 'This open-source plugin is engineered by Muhammad Hamid, Head of Digital Marketing & MarTech specialist.', 'dynamic-schema-generator' ); ?></p>
		<p><?php esc_html_e( 'If your team requires enterprise Schema architecture, Generative Engine Optimization (GEO) audits, Google Ads waste detection, or custom server-side conversion tracking pipelines:', 'dynamic-schema-generator' ); ?></p>
		<p>
			<a href="https://wa.me/923167417135?text=Hello%20Hamid%2C%20I%20am%20using%20Dynamic%20Schema%20Generator%20and%20would%20like%20to%20discuss%20a%20commercial%20SEO%20and%20Schema%20consulting%20project." target="_blank" rel="noopener noreferrer" class="button button-primary dsg-btn-wa">
				<?php esc_html_e( 'Chat on WhatsApp: +92 316 7417135', 'dynamic-schema-generator' ); ?>
			</a>
			&nbsp;
			<a href="https://hamid-resume.web.app/" target="_blank" rel="noopener noreferrer" class="button button-secondary">
				<?php esc_html_e( 'View Portfolio & Verified Case Studies &rarr;', 'dynamic-schema-generator' ); ?>
			</a>
		</p>
	</div>
</div>
