<?php
/**
 * Elementor Integration View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$adapter  = new \DSG\Integrations\Elementor_Adapter();
$is_active = $adapter->is_active();
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Elementor Page Builder Integration', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Automatic schema extraction and mapping from Elementor landing pages and widgets.', 'dynamic-schema-generator' ); ?></p>
		</div>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Elementor Connection Status', 'dynamic-schema-generator' ); ?></h2>
		<p>
			<?php if ( $is_active ) : ?>
				<span class="dsg-badge dsg-badge-success"><?php esc_html_e( '● Elementor Detected and Active', 'dynamic-schema-generator' ); ?></span>
			<?php else : ?>
				<span class="dsg-badge dsg-badge-muted"><?php esc_html_e( '○ Elementor is not installed or active', 'dynamic-schema-generator' ); ?></span>
			<?php endif; ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'Dynamic Schema Generator uses Elementor official post document metadata (_elementor_data) to traverse section, column, and container trees without requiring rendering overhead or fragile DOM scraping.', 'dynamic-schema-generator' ); ?>
		</p>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Supported Elementor Widgets', 'dynamic-schema-generator' ); ?></h2>
		<table class="widefat striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Widget Type', 'dynamic-schema-generator' ); ?></th>
					<th><?php esc_html_e( 'Target Schema Entity', 'dynamic-schema-generator' ); ?></th>
					<th><?php esc_html_e( 'Extraction Mechanism', 'dynamic-schema-generator' ); ?></th>
					<th><?php esc_html_e( 'Compatibility Status', 'dynamic-schema-generator' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><strong><?php esc_html_e( 'Accordion (widgetType: accordion)', 'dynamic-schema-generator' ); ?></strong></td>
					<td><code>FAQPage &rarr; Question / Answer</code></td>
					<td><?php esc_html_e( 'Extracts tab_title as question and tab_content as acceptedAnswer.', 'dynamic-schema-generator' ); ?></td>
					<td><span class="dsg-badge dsg-badge-success"><?php esc_html_e( 'Supported', 'dynamic-schema-generator' ); ?></span></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Toggle (widgetType: toggle)', 'dynamic-schema-generator' ); ?></strong></td>
					<td><code>FAQPage &rarr; Question / Answer</code></td>
					<td><?php esc_html_e( 'Extracts toggle title and text payload into valid FAQ Schema.', 'dynamic-schema-generator' ); ?></td>
					<td><span class="dsg-badge dsg-badge-success"><?php esc_html_e( 'Supported', 'dynamic-schema-generator' ); ?></span></td>
				</tr>
				<tr>
					<td><strong><?php esc_html_e( 'Heading (widgetType: heading)', 'dynamic-schema-generator' ); ?></strong></td>
					<td><code>Article / WebPage outline</code></td>
					<td><?php esc_html_e( 'Extracts visible title strings for content entity validation.', 'dynamic-schema-generator' ); ?></td>
					<td><span class="dsg-badge dsg-badge-success"><?php esc_html_e( 'Supported', 'dynamic-schema-generator' ); ?></span></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="dsg-card">
		<h2><?php esc_html_e( 'Documented Limitations & Architectural Safeguards', 'dynamic-schema-generator' ); ?></h2>
		<ul class="dsg-bullet-list">
			<li><strong><?php esc_html_e( 'Zero DOM Injection Overhead:', 'dynamic-schema-generator' ); ?></strong> <?php esc_html_e( 'The adapter inspects persisted JSON document trees. It never executes external frontend JavaScript or slows down page rendering.', 'dynamic-schema-generator' ); ?></li>
			<li><strong><?php esc_html_e( 'Priority Overrides:', 'dynamic-schema-generator' ); ?></strong> <?php esc_html_e( 'If you enter custom FAQ pairs in the DSG meta box on an individual post, the manual pairs take precedence over automatic Elementor widget extraction.', 'dynamic-schema-generator' ); ?></li>
			<li><strong><?php esc_html_e( 'Container & Nested Sections Compatibility:', 'dynamic-schema-generator' ); ?></strong> <?php esc_html_e( 'Supports both classic Elementor Sections/Columns and modern Flexbox/Grid Containers recursively.', 'dynamic-schema-generator' ); ?></li>
		</ul>
	</div>
</div>
