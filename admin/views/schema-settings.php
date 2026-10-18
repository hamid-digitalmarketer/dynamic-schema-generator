<?php
/**
 * Schema Settings View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings   = \DSG\Settings::get_all();
$post_types = get_post_types( array( 'public' => true ), 'objects' );
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Schema Engine Settings', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Control enabled schema types, supported post types, exclusions, and conflict detection.', 'dynamic-schema-generator' ); ?></p>
		</div>
	</div>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings updated successfully.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'dsg_settings_action', 'dsg_settings_nonce' ); ?>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Master Engine Toggle', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'JSON-LD Output', 'dynamic-schema-generator' ); ?></th>
					<td>
						<label>
							<input name="dsg_settings[enable_schema]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_schema'] ) ); ?>>
							<strong><?php esc_html_e( 'Enable Dynamic Schema Generation', 'dynamic-schema-generator' ); ?></strong>
						</label>
						<p class="description"><?php esc_html_e( 'Master switch. When disabled, no JSON-LD schema tags will be printed on the frontend.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Enabled Schema Modules', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Global Schemas', 'dynamic-schema-generator' ); ?></th>
					<td>
						<fieldset>
							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_organization]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_organization'] ) ); ?>>
								<strong><?php esc_html_e( 'Organization Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Emits brand identity, logo, sameAs social links, and publisher data.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_local_business]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_local_business'] ) ); ?>>
								<strong><?php esc_html_e( 'LocalBusiness Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Emits physical address, phone, geo coordinates, and opening hours.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_website]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_website'] ) ); ?>>
								<strong><?php esc_html_e( 'WebSite Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Emits website entity with site search potentialAction for sitelinks search box.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_breadcrumbs]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_breadcrumbs'] ) ); ?>>
								<strong><?php esc_html_e( 'BreadcrumbList Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Hierarchical navigation path for all posts, pages, and categories.', 'dynamic-schema-generator' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Content & Service Schemas', 'dynamic-schema-generator' ); ?></th>
					<td>
						<fieldset>
							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_articles]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_articles'] ) ); ?>>
								<strong><?php esc_html_e( 'Article / BlogPosting Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Emits author, headline, datePublished, dateModified, and featured images for blog posts.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_services]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_services'] ) ); ?>>
								<strong><?php esc_html_e( 'Service Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Emits structured Service offerings with optional pricing when mapped to landing pages.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_faqs]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_faqs'] ) ); ?>>
								<strong><?php esc_html_e( 'FAQPage Schema', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Generates question/answer pairs from post meta or Elementor accordion widgets.', 'dynamic-schema-generator' ); ?>
							</label>

							<label style="display:block;margin-bottom:8px;">
								<input name="dsg_settings[enable_elementor_adapter]" type="checkbox" value="1" <?php checked( ! empty( $settings['enable_elementor_adapter'] ) ); ?>>
								<strong><?php esc_html_e( 'Elementor Page Builder Integration', 'dynamic-schema-generator' ); ?></strong> &mdash; <?php esc_html_e( 'Automatically extracts accordion/toggle widgets into FAQPage schema.', 'dynamic-schema-generator' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		</div>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Post Types & Exclusions', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'Supported Post Types', 'dynamic-schema-generator' ); ?></th>
					<td>
						<fieldset>
							<?php foreach ( $post_types as $pt ) : ?>
								<label style="margin-right:15px;display:inline-block;">
									<input name="dsg_settings[post_types][]" type="checkbox" value="<?php echo esc_attr( $pt->name ); ?>" <?php checked( in_array( $pt->name, (array) $settings['post_types'], true ) ); ?>>
									<?php echo esc_html( $pt->label ); ?> (<code><?php echo esc_html( $pt->name ); ?></code>)
								</label>
							<?php endforeach; ?>
						</fieldset>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_excluded_posts"><?php esc_html_e( 'Excluded Post IDs', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_settings[excluded_posts]" type="text" id="dsg_excluded_posts" value="<?php echo esc_attr( implode( ', ', (array) $settings['excluded_posts'] ) ); ?>" class="regular-text" placeholder="12, 45, 102">
						<p class="description"><?php esc_html_e( 'Comma-separated post or page IDs where schema generation should be completely suppressed.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Advanced & Cleanup Policies', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><?php esc_html_e( 'SEO Conflict Detection', 'dynamic-schema-generator' ); ?></th>
					<td>
						<label>
							<input name="dsg_settings[conflict_detection]" type="checkbox" value="1" <?php checked( ! empty( $settings['conflict_detection'] ) ); ?>>
							<?php esc_html_e( 'Detect and log schema conflicts with Yoast SEO, Rank Math, AIOSEO, and SEOPress.', 'dynamic-schema-generator' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Minify JSON-LD Output', 'dynamic-schema-generator' ); ?></th>
					<td>
						<label>
							<input name="dsg_settings[minify_json]" type="checkbox" value="1" <?php checked( ! empty( $settings['minify_json'] ) ); ?>>
							<?php esc_html_e( 'Minify generated JSON for maximum HTML payload performance (automatically pretty-prints when WP_DEBUG is true).', 'dynamic-schema-generator' ); ?>
						</label>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php esc_html_e( 'Clean Uninstall', 'dynamic-schema-generator' ); ?></th>
					<td>
						<label>
							<input name="dsg_settings[delete_data_on_uninstall]" type="checkbox" value="1" <?php checked( ! empty( $settings['delete_data_on_uninstall'] ) ); ?>>
							<span style="color:#d63638;"><?php esc_html_e( 'Permanently delete all DSG options and post meta when the plugin is uninstalled.', 'dynamic-schema-generator' ); ?></span>
						</label>
					</td>
				</tr>
			</table>
		</div>

		<p class="submit">
			<button type="submit" name="dsg_save_settings" class="button button-primary button-large"><?php esc_html_e( 'Save Settings', 'dynamic-schema-generator' ); ?></button>
		</p>
	</form>
</div>
