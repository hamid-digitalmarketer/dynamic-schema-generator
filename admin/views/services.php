<?php
/**
 * Services Manager View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$services = \DSG\Service_Manager::get_all();
$pages    = get_pages( array( 'sort_column' => 'post_title', 'sort_order' => 'ASC' ) );
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Service Schema Manager', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Map professional services, pricing, and service areas to specific landing pages.', 'dynamic-schema-generator' ); ?></p>
		</div>
	</div>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Service saved successfully.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>
	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Service deleted.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>

	<div class="dsg-grid-2">
		<!-- Left: Services List Table -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Configured Services', 'dynamic-schema-generator' ); ?></h2>
			<?php if ( empty( $services ) ) : ?>
				<p class="description"><?php esc_html_e( 'No services added yet. Use the form on the right to register your core offerings.', 'dynamic-schema-generator' ); ?></p>
			<?php else : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Service Name', 'dynamic-schema-generator' ); ?></th>
							<th><?php esc_html_e( 'Mapped Page', 'dynamic-schema-generator' ); ?></th>
							<th><?php esc_html_e( 'Price', 'dynamic-schema-generator' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'dynamic-schema-generator' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $services as $s ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $s['name'] ); ?></strong><br><small><?php echo esc_html( $s['service_type'] ); ?></small></td>
								<td><?php echo $s['page_id'] ? esc_html( get_the_title( $s['page_id'] ) ) : '<em>' . esc_html__( 'Unassigned', 'dynamic-schema-generator' ) . '</em>'; ?></td>
								<td><?php echo ! empty( $s['price'] ) ? esc_html( $s['currency'] . ' ' . $s['price'] ) : '<em>' . esc_html__( 'Contact', 'dynamic-schema-generator' ) . '</em>'; ?></td>
								<td>
									<?php
									$del_url = wp_nonce_url(
										add_query_arg( array( 'page' => 'dsg-services', 'dsg_action' => 'delete_service', 'id' => $s['id'] ), admin_url( 'admin.php' ) ),
										'dsg_delete_service_' . $s['id']
									);
									?>
									<a href="<?php echo esc_url( $del_url ); ?>" class="button button-small button-link-delete" onclick="return confirm('<?php esc_attr_e( 'Delete this service definition?', 'dynamic-schema-generator' ); ?>');"><?php esc_html_e( 'Delete', 'dynamic-schema-generator' ); ?></a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<!-- Right: Add Service Form -->
		<div class="dsg-card">
			<h2><?php esc_html_e( 'Add / Update Service', 'dynamic-schema-generator' ); ?></h2>
			<form method="post" action="">
				<?php wp_nonce_field( 'dsg_service_action', 'dsg_service_nonce' ); ?>

				<p>
					<label for="dsg_srv_name"><strong><?php esc_html_e( 'Service Name', 'dynamic-schema-generator' ); ?> <span class="dsg-required">*</span></strong></label><br>
					<input type="text" name="dsg_service[name]" id="dsg_srv_name" class="widefat" required placeholder="e.g. Technical SEO Audit">
				</p>

				<p>
					<label for="dsg_srv_type"><strong><?php esc_html_e( 'Service Category / Type', 'dynamic-schema-generator' ); ?></strong></label><br>
					<input type="text" name="dsg_service[service_type]" id="dsg_srv_type" class="widefat" placeholder="e.g. Digital Marketing, Web Development">
				</p>

				<p>
					<label for="dsg_srv_desc"><strong><?php esc_html_e( 'Description', 'dynamic-schema-generator' ); ?></strong></label><br>
					<textarea name="dsg_service[description]" id="dsg_srv_desc" rows="3" class="widefat" placeholder="Detailed explanation of the deliverables."></textarea>
				</p>

				<p>
					<label for="dsg_srv_page"><strong><?php esc_html_e( 'Map to Landing Page', 'dynamic-schema-generator' ); ?></strong></label><br>
					<select name="dsg_service[page_id]" id="dsg_srv_page" class="widefat">
						<option value="0"><?php esc_html_e( '-- Select Target Landing Page --', 'dynamic-schema-generator' ); ?></option>
						<?php foreach ( $pages as $p ) : ?>
							<option value="<?php echo esc_attr( $p->ID ); ?>"><?php echo esc_html( $p->post_title ); ?> (ID: <?php echo esc_html( $p->ID ); ?>)</option>
						<?php endforeach; ?>
					</select>
					<span class="description"><?php esc_html_e( 'Service schema will be automatically attached to this URL.', 'dynamic-schema-generator' ); ?></span>
				</p>

				<p>
					<label for="dsg_srv_area"><strong><?php esc_html_e( 'Area Served', 'dynamic-schema-generator' ); ?></strong></label><br>
					<input type="text" name="dsg_service[area_served]" id="dsg_srv_area" class="widefat" placeholder="e.g. Nationwide or Local Service Radius">
				</p>

				<div style="display:flex;gap:10px;">
					<div style="flex:1;">
						<label for="dsg_srv_price"><strong><?php esc_html_e( 'Fixed Price (Optional)', 'dynamic-schema-generator' ); ?></strong></label><br>
						<input type="number" step="0.01" name="dsg_service[price]" id="dsg_srv_price" class="widefat" placeholder="499.00">
					</div>
					<div style="width:100px;">
						<label for="dsg_srv_curr"><strong><?php esc_html_e( 'Currency', 'dynamic-schema-generator' ); ?></strong></label><br>
						<input type="text" name="dsg_service[currency]" id="dsg_srv_curr" value="USD" class="widefat" maxlength="3">
					</div>
				</div>
				<p class="description"><?php esc_html_e( 'Leave price empty if pricing varies or is quote-based. Never invent fixed prices.', 'dynamic-schema-generator' ); ?></p>

				<p>
					<label for="dsg_srv_img"><strong><?php esc_html_e( 'Service Image URL', 'dynamic-schema-generator' ); ?></strong></label><br>
					<input type="url" name="dsg_service[image_url]" id="dsg_srv_img" class="widefat" placeholder="https://example.com/images/service.jpg">
				</p>

				<p class="submit">
					<button type="submit" name="dsg_save_service" class="button button-primary"><?php esc_html_e( 'Save Service', 'dynamic-schema-generator' ); ?></button>
				</p>
			</form>
		</div>
	</div>
</div>
