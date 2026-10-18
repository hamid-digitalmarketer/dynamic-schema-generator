<?php
/**
 * Schema Preview and Live Validation View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$generator = new \DSG\Schema_Generator();
$initial_graph = $generator->generate();
$initial_json  = wp_json_encode( $initial_graph, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
$initial_val   = \DSG\Schema_Validator::validate( $initial_graph );

$recent_posts = get_posts( array(
	'post_type'      => array( 'post', 'page' ),
	'post_status'    => 'publish',
	'posts_per_page' => 25,
) );
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Schema Preview & In-House Validation', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Inspect the exact JSON-LD payload emitted for any page on your site and run Schema.org syntax checks.', 'dynamic-schema-generator' ); ?></p>
		</div>
		<div class="dsg-header-actions">
			<button type="button" id="dsg-copy-json" class="button button-secondary"><?php esc_html_e( 'Copy JSON-LD', 'dynamic-schema-generator' ); ?></button>
			<a href="https://validator.schema.org/" target="_blank" rel="noopener noreferrer" class="button button-secondary"><?php esc_html_e( 'Schema.org Validator &rarr;', 'dynamic-schema-generator' ); ?></a>
			<a href="https://search.google.com/test/rich-results" target="_blank" rel="noopener noreferrer" class="button button-primary"><?php esc_html_e( 'Google Rich Results Test &rarr;', 'dynamic-schema-generator' ); ?></a>
		</div>
	</div>

	<!-- Selector Toolbar -->
	<div class="dsg-card" style="margin-bottom:20px;">
		<div style="display:flex;align-items:center;gap:15px;flex-wrap:wrap;">
			<label for="dsg-preview-target"><strong><?php esc_html_e( 'Select Target URL / Page Context:', 'dynamic-schema-generator' ); ?></strong></label>
			<select id="dsg-preview-target" class="regular-text" style="min-width:320px;">
				<option value="0"><?php esc_html_e( 'Homepage / Global Context', 'dynamic-schema-generator' ); ?></option>
				<?php foreach ( $recent_posts as $p ) : ?>
					<option value="<?php echo absint( $p->ID ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?> (<?php echo esc_html( $p->post_type ); ?> #<?php echo absint( $p->ID ); ?>)</option>
				<?php endforeach; ?>
			</select>
			<button type="button" id="dsg-fetch-preview" class="button button-secondary"><?php esc_html_e( 'Refresh Preview', 'dynamic-schema-generator' ); ?></button>
			<span id="dsg-preview-spinner" class="spinner" style="float:none;"></span>
		</div>
	</div>

	<!-- Validation Status Banner -->
	<div id="dsg-val-banner" class="dsg-banner <?php echo $initial_val['valid'] ? 'dsg-banner-success' : 'dsg-banner-error'; ?>">
		<h3 id="dsg-val-title">
			<?php echo $initial_val['valid'] ? esc_html__( '✓ Schema Validated: 0 Syntax Errors Found', 'dynamic-schema-generator' ) : esc_html__( '⚠ Schema Validation Issues Detected', 'dynamic-schema-generator' ); ?>
		</h3>
		<p id="dsg-val-types">
			<strong><?php esc_html_e( 'Detected Types:', 'dynamic-schema-generator' ); ?></strong>
			<span id="dsg-val-types-list"><?php echo esc_html( implode( ', ', $initial_val['types'] ) ); ?></span>
		</p>
		<div id="dsg-val-messages">
			<?php if ( ! empty( $initial_val['errors'] ) ) : ?>
				<ul style="color:#d63638;margin-top:5px;">
					<?php foreach ( $initial_val['errors'] as $err ) : ?>
						<li>&bull; <?php echo esc_html( $err ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php if ( ! empty( $initial_val['warnings'] ) ) : ?>
				<ul style="color:#b26200;margin-top:5px;">
					<?php foreach ( $initial_val['warnings'] as $warn ) : ?>
						<li>&bull; <?php echo esc_html( $warn ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<!-- JSON Payload Viewer -->
	<div class="dsg-card">
		<h2><?php esc_html_e( 'Generated JSON-LD Payload', 'dynamic-schema-generator' ); ?></h2>
		<textarea id="dsg-json-viewer" class="large-text code" rows="22" readonly style="font-family:Consolas, Monaco, monospace;font-size:12px;background:#0d1117;color:#58a6ff;padding:15px;border-radius:6px;"><?php echo esc_textarea( $initial_json ); ?></textarea>
	</div>
</div>
