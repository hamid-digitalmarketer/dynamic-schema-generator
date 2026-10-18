<?php
/**
 * Admin Dashboard View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$report   = \DSG\Diagnostics::get_report();
$settings = \DSG\Settings::get_all();
$business = \DSG\Business_Manager::get_info();
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Dynamic Schema Generator', 'dynamic-schema-generator' ); ?> <span class="dsg-badge dsg-badge-version">v<?php echo esc_html( DSG_VERSION ); ?></span></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Automated, production-grade JSON-LD structured data engine with Elementor integration.', 'dynamic-schema-generator' ); ?></p>
		</div>
		<div class="dsg-header-actions">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=dsg-preview' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Preview Live Schema', 'dynamic-schema-generator' ); ?></a>
			<a href="https://wa.me/923167417135?text=Hello%20Hamid%2C%20I%20am%20using%20Dynamic%20Schema%20Generator%20and%20would%20like%20to%20discuss%20an%20enterprise%20SEO%20and%20Schema%20consultation." target="_blank" rel="noopener noreferrer" class="button button-secondary dsg-btn-wa"><?php esc_html_e( 'WhatsApp Consultation', 'dynamic-schema-generator' ); ?></a>
		</div>
	</div>

	<!-- Status Metrics -->
	<div class="dsg-grid-4">
		<div class="dsg-card dsg-metric-card">
			<span class="dsg-metric-label"><?php esc_html_e( 'Active Schema Status', 'dynamic-schema-generator' ); ?></span>
			<span class="dsg-metric-val <?php echo ! empty( $settings['enable_schema'] ) ? 'dsg-text-success' : 'dsg-text-danger'; ?>">
				<?php echo ! empty( $settings['enable_schema'] ) ? esc_html__( 'Enabled', 'dynamic-schema-generator' ) : esc_html__( 'Disabled', 'dynamic-schema-generator' ); ?>
			</span>
			<span class="dsg-metric-sub"><?php echo esc_html( $report['enabled_modules'] ); ?> <?php esc_html_e( 'schema modules active', 'dynamic-schema-generator' ); ?></span>
		</div>

		<div class="dsg-card dsg-metric-card">
			<span class="dsg-metric-label"><?php esc_html_e( 'Elementor Integration', 'dynamic-schema-generator' ); ?></span>
			<span class="dsg-metric-val <?php echo $report['elementor_active'] ? 'dsg-text-success' : 'dsg-text-muted'; ?>">
				<?php echo $report['elementor_active'] ? esc_html__( 'Active', 'dynamic-schema-generator' ) : esc_html__( 'Standby', 'dynamic-schema-generator' ); ?>
			</span>
			<span class="dsg-metric-sub"><?php echo $report['elementor_active'] ? esc_html__( 'Widget parsing ready', 'dynamic-schema-generator' ) : esc_html__( 'Elementor not loaded', 'dynamic-schema-generator' ); ?></span>
		</div>

		<div class="dsg-card dsg-metric-card">
			<span class="dsg-metric-label"><?php esc_html_e( 'Managed Services', 'dynamic-schema-generator' ); ?></span>
			<span class="dsg-metric-val dsg-text-info"><?php echo esc_html( $report['total_services'] ); ?></span>
			<span class="dsg-metric-sub"><?php esc_html_e( 'Structured Service definitions', 'dynamic-schema-generator' ); ?></span>
		</div>

		<div class="dsg-card dsg-metric-card">
			<span class="dsg-metric-label"><?php esc_html_e( 'Configured FAQs', 'dynamic-schema-generator' ); ?></span>
			<span class="dsg-metric-val dsg-text-purple"><?php echo esc_html( $report['total_faqs'] ); ?></span>
			<span class="dsg-metric-sub"><?php esc_html_e( 'Verified Q&A pairs', 'dynamic-schema-generator' ); ?></span>
		</div>
	</div>

	<!-- AI Search Readiness & Reality Check Banner -->
	<div class="dsg-banner dsg-banner-info">
		<h3><?php esc_html_e( 'AI Search Readiness & Entity Clarity (GEO)', 'dynamic-schema-generator' ); ?></h3>
		<p><?php esc_html_e( 'Search engines (Google, Bing) and AI discovery models (Gemini, ChatGPT, Perplexity) rely on structured data to accurately index business entities, services, authors, and FAQs without hallucination. Dynamic Schema Generator emits connected @graph entities linked via persistent @id URIs. Note: Valid schema increases machine readability, but search engines determine rich snippet display independently.', 'dynamic-schema-generator' ); ?></p>
	</div>

	<!-- Checklist & Setup Assistant -->
	<div class="dsg-card">
		<h2><?php esc_html_e( 'Schema Setup Checklist', 'dynamic-schema-generator' ); ?></h2>
		<ul class="dsg-checklist">
			<li>
				<span class="dsg-check-icon <?php echo ! empty( $business['name'] ) ? 'dsg-check-ok' : 'dsg-check-warn'; ?>">
					<?php echo ! empty( $business['name'] ) ? '&#10003;' : '!'; ?>
				</span>
				<div>
					<strong><?php esc_html_e( 'Business Information', 'dynamic-schema-generator' ); ?></strong>
					<p><?php echo ! empty( $business['name'] ) ? sprintf( esc_html__( 'Configured as "%s" (%s).', 'dynamic-schema-generator' ), esc_html( $business['name'] ), esc_html( $business['business_type'] ) ) : esc_html__( 'Please configure your Business Name and Address in Business Info.', 'dynamic-schema-generator' ); ?></p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dsg-business' ) ); ?>" class="button button-small"><?php esc_html_e( 'Configure', 'dynamic-schema-generator' ); ?></a>
			</li>

			<li>
				<span class="dsg-check-icon <?php echo ! empty( $settings['enable_breadcrumbs'] ) ? 'dsg-check-ok' : 'dsg-check-info'; ?>">
					<?php echo ! empty( $settings['enable_breadcrumbs'] ) ? '&#10003;' : 'o'; ?>
				</span>
				<div>
					<strong><?php esc_html_e( 'Hierarchical BreadcrumbList', 'dynamic-schema-generator' ); ?></strong>
					<p><?php esc_html_e( 'Generates machine-readable path hierarchy for all standard posts and pages.', 'dynamic-schema-generator' ); ?></p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dsg-settings' ) ); ?>" class="button button-small"><?php esc_html_e( 'Settings', 'dynamic-schema-generator' ); ?></a>
			</li>

			<li>
				<span class="dsg-check-icon <?php echo $report['total_services'] > 0 ? 'dsg-check-ok' : 'dsg-check-info'; ?>">
					<?php echo $report['total_services'] > 0 ? '&#10003;' : 'o'; ?>
				</span>
				<div>
					<strong><?php esc_html_e( 'Structured Services & Pricing', 'dynamic-schema-generator' ); ?></strong>
					<p><?php echo $report['total_services'] > 0 ? sprintf( esc_html__( '%d service(s) configured and mapped to pages.', 'dynamic-schema-generator' ), $report['total_services'] ) : esc_html__( 'Map your core services to landing pages with optional pricing.', 'dynamic-schema-generator' ); ?></p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dsg-services' ) ); ?>" class="button button-small"><?php esc_html_e( 'Manage Services', 'dynamic-schema-generator' ); ?></a>
			</li>

			<li>
				<span class="dsg-check-icon <?php echo empty( $report['detected_seo_plugins'] ) ? 'dsg-check-ok' : 'dsg-check-warn'; ?>">
					<?php echo empty( $report['detected_seo_plugins'] ) ? '&#10003;' : '!'; ?>
				</span>
				<div>
					<strong><?php esc_html_e( 'SEO Plugin Compatibility & Conflict Safeguards', 'dynamic-schema-generator' ); ?></strong>
					<p><?php echo empty( $report['detected_seo_plugins'] ) ? esc_html__( 'No third-party conflicting SEO schema generators detected.', 'dynamic-schema-generator' ) : sprintf( esc_html__( 'Active SEO plugins detected: %s. Automatic deduplication and conflict filters are active.', 'dynamic-schema-generator' ), esc_html( implode( ', ', $report['detected_seo_plugins'] ) ) ); ?></p>
				</div>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=dsg-diagnostics' ) ); ?>" class="button button-small"><?php esc_html_e( 'Diagnostics', 'dynamic-schema-generator' ); ?></a>
			</li>
		</ul>
	</div>
</div>
