<?php
/**
 * FAQ Schema Manager View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$faqs  = \DSG\FAQ_Manager::get_all();
$pages = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'FAQ Schema Manager', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Configure structured FAQPage markup. Auto-extracts from Elementor Accordion & Toggle widgets.', 'dynamic-schema-generator' ); ?></p>
		</div>
	</div>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'FAQ saved successfully.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'FAQ deleted.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>

	<div class="dsg-banner dsg-banner-info">
		<strong><?php esc_html_e( 'Search Engine Policy Notice:', 'dynamic-schema-generator' ); ?></strong>
		<?php esc_html_e( 'Google guidelines stipulate that FAQ schema must accurately reflect user-visible questions and answers already present on the page. Do not use FAQ schema for advertising or hidden text.', 'dynamic-schema-generator' ); ?>
	</div>

	<div class="dsg-grid-2">
		<!-- Left: FAQ List Table -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Configured Site-Level FAQs', 'dynamic-schema-generator' ); ?></h2>
			<?php if ( empty( $faqs ) ) : ?>
				<p class="description"><?php esc_html_e( 'No global FAQs configured yet. Use the form on the right or configure FAQs directly in the post edit screen.', 'dynamic-schema-generator' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Question', 'dynamic-schema-generator' ); ?></th>
							<th><?php esc_html_e( 'Mapped Page', 'dynamic-schema-generator' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'dynamic-schema-generator' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $faqs as $f ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $f['question'] ); ?></strong></td>
								<td><?php echo $f['page_id'] ? esc_html( get_the_title( $f['page_id'] ) ) : '<em>' . esc_html__( 'All Pages / Global', 'dynamic-schema-generator' ) . '</em>'; ?></td>
								<td>
									<?php
									$del_url = wp_nonce_url(
										add_query_arg( array( 'page' => 'dsg-faqs', 'dsg_action' => 'delete_faq', 'id' => $f['id'] ), admin_url( 'admin.php' ) ),
										'dsg_delete_faq_' . $f['id']
									);
									?>
									<a href="<?php echo esc_url( $del_url ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Delete this FAQ?', 'dynamic-schema-generator' ); ?>');"><?php esc_html_e( 'Delete', 'dynamic-schema-generator' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<!-- Right: Add FAQ Form -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Add New FAQ Entry', 'dynamic-schema-generator' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'dsg_faq_action', 'dsg_faq_nonce' ); ?>

				<p>
					<label for="dsg_faq_q"><strong><?php esc_html_e( 'Question Text', 'dynamic-schema-generator' ); ?> <span class="dsg-required">*</span></strong></label><br>
					<input type="text" name="dsg_faq[question]" id="dsg_faq_q" class="widefat" required placeholder="e.g. How does technical SEO improve AI search visibility?">
				</p>

				<p>
					<label for="dsg_faq_a"><strong><?php esc_html_e( 'Accepted Answer Text', 'dynamic-schema-generator' ); ?> <span class="dsg-required">*</span></strong></label><br>
					<textarea name="dsg_faq[answer]" id="dsg_faq_a" rows="4" class="widefat" required placeholder="Concise, factual answer. HTML links and formatting permitted."></textarea>
				</p>

				<p>
					<label for="dsg_faq_page"><strong><?php esc_html_e( 'Target Page', 'dynamic-schema-generator' ); ?></strong></label><br>
					<select name="dsg_faq[page_id]" id="dsg_faq_page" class="widefat">
						<option value="0"><?php esc_html_e( '-- Global (Included when relevant) --', 'dynamic-schema-generator' ); ?></option>
						<?php foreach ( $pages as $p ) : ?>
							<option value="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?> (ID: <?php echo esc_html( $p->ID ); ?>)</option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="submit">
					<button type="submit" name="dsg_save_faq" class="button button-primary"><?php esc_html_e( 'Save FAQ Entry', 'dynamic-schema-generator' ); ?></button>
				</p>
			</form>
		</div>
	</div>
</div>
