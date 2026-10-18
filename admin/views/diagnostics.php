<?php
/**
 * System Diagnostics View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$report = \DSG\Diagnostics::get_report();
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'System Diagnostics & Health', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Environment telemetry, active integrations, conflict inspection, and audit logs.', 'dynamic-schema-generator' ); ?></p>
		</div>
		<div class="dsg-header-actions">
			<button type="button" id="dsg-clear-logs-btn" class="button button-secondary"><?php esc_html_e( 'Clear Diagnostics Log', 'dynamic-schema-generator' ); ?></button>
		</div>
	</div>

	<div class="dsg-grid-2">
		<!-- System Environment -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Environment Specifications', 'dynamic-schema-generator' ); ?></h2>
			<table class="widefat striped">
				<tbody>
					<tr>
						<td><strong><?php esc_html_e( 'Plugin Version', 'dynamic-schema-generator' ); ?></strong></td>
						<td><code><?php echo esc_html( $report['plugin_version'] ); ?></code></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'WordPress Core', 'dynamic-schema-generator' ); ?></strong></td>
						<td><code><?php echo esc_html( $report['wordpress_version'] ); ?></code></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'PHP Version', 'dynamic-schema-generator' ); ?></strong></td>
						<td><code><?php echo esc_html( $report['php_version'] ); ?></code></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Web Server', 'dynamic-schema-generator' ); ?></strong></td>
						<td><code><?php echo esc_html( $report['web_server'] ); ?></code></td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Elementor Page Builder', 'dynamic-schema-generator' ); ?></strong></td>
						<td>
							<?php if ( $report['elementor_active'] ) : ?>
								<span class="dsg-badge dsg-badge-success"><?php esc_html_e( 'Active', 'dynamic-schema-generator' ); ?></span>
							<?php else : ?>
								<span class="dsg-badge dsg-badge-muted"><?php esc_html_e( 'Inactive / Not Installed', 'dynamic-schema-generator' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td><strong><?php esc_html_e( 'Detected SEO Plugins', 'dynamic-schema-generator' ); ?></strong></td>
						<td>
							<?php if ( ! empty( $report['detected_seo_plugins'] ) ) : ?>
								<span class="dsg-badge dsg-badge-warn"><?php echo esc_html( implode( ', ', $report['detected_seo_plugins'] ) ); ?></span>
							<?php else : ?>
								<span class="dsg-badge dsg-badge-success"><?php esc_html_e( 'None (Zero Conflicts)', 'dynamic-schema-generator' ); ?></span>
							<?php endif; ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Status Flags -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Health Status & Notifications', 'dynamic-schema-generator' ); ?></h2>
			<?php if ( empty( $report['status_flags'] ) ) : ?>
				<div class="dsg-banner dsg-banner-success">
					<p><?php esc_html_e( '✓ All system checks passed cleanly. No configuration errors or version incompatibilities detected.', 'dynamic-schema-generator' ); ?></p>
				</div>
			<?php else : ?>
				<ul class="dsg-bullet-list">
					<?php foreach ( $report['status_flags'] as $flag ) : ?>
						<li>
							<span class="dsg-badge dsg-badge-<?php echo esc_attr( $flag['level'] ); ?>"><?php echo esc_html( strtoupper( $flag['level'] ) ); ?></span>
							<?php echo esc_html( $flag['message'] ); ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>

	<!-- Recent Diagnostics Log -->
	<div class="dsg-card" style="margin-top:20px;">
		<h2><?php esc_html_e( 'Recent System Activity & Audit Trail', 'dynamic-schema-generator' ); ?></h2>
		<?php if ( empty( $report['recent_logs'] ) ) : ?>
			<p id="dsg-no-logs" class="description"><?php esc_html_e( 'No events recorded yet. Administrative operations and schema alerts will appear here.', 'dynamic-schema-generator' ); ?></p>
		<?php else : ?>
			<table class="widefat striped" id="dsg-logs-table">
				<thead>
					<tr>
						<th style="width:160px;"><?php esc_html_e( 'Timestamp', 'dynamic-schema-generator' ); ?></th>
						<th style="width:100px;"><?php esc_html_e( 'Level', 'dynamic-schema-generator' ); ?></th>
						<th><?php esc_html_e( 'Message', 'dynamic-schema-generator' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $report['recent_logs'] as $log ) : ?>
						<tr>
							<td><small><?php echo esc_html( $log['timestamp'] ); ?></small></td>
							<td><span class="dsg-badge dsg-badge-info"><?php echo esc_html( strtoupper( $log['type'] ) ); ?></span></td>
							<td><?php echo esc_html( $log['message'] ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
</div>
