<?php
/**
 * Business Information Settings View.
 *
 * @package Dynamic_Schema_Generator
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$business = \DSG\Business_Manager::get_info();
$types    = \DSG\Business_Manager::get_supported_types();
?>
<div class="wrap dsg-admin-wrap">
	<div class="dsg-header">
		<div class="dsg-header-title">
			<h1><?php esc_html_e( 'Business Information', 'dynamic-schema-generator' ); ?></h1>
			<p class="dsg-subtitle"><?php esc_html_e( 'Configure core entity attributes for Organization and LocalBusiness schema markup.', 'dynamic-schema-generator' ); ?></p>
		</div>
	</div>

	<?php if ( isset( $_GET['updated'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Business information saved successfully.', 'dynamic-schema-generator' ); ?></p></div>
	<?php endif; ?>

	<form method="post" action="">
		<?php wp_nonce_field( 'dsg_business_action', 'dsg_business_nonce' ); ?>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'General Entity Attributes', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="dsg_business_type"><?php esc_html_e( 'Schema Entity Type', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<select name="dsg_business[business_type]" id="dsg_business_type" class="regular-text">
							<?php foreach ( $types as $val => $label ) : ?>
								<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $business['business_type'], $val ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php esc_html_e( 'Choose LocalBusiness (or specialized subtype) only if you serve local customers with a physical location or defined service territory.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_name"><?php esc_html_e( 'Business / Brand Name', 'dynamic-schema-generator' ); ?> <span class="dsg-required">*</span></label></th>
					<td>
						<input name="dsg_business[name]" type="text" id="dsg_name" value="<?php echo esc_attr( $business['name'] ); ?>" class="regular-text" required>
						<p class="description"><?php esc_html_e( 'Your public brand or operational company name.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_legal_name"><?php esc_html_e( 'Legal Name', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[legal_name]" type="text" id="dsg_legal_name" value="<?php echo esc_attr( $business['legal_name'] ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'Official registered corporate entity name (if different from brand name).', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_description"><?php esc_html_e( 'Entity Description', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<textarea name="dsg_business[description]" id="dsg_description" rows="3" class="large-text"><?php echo esc_textarea( $business['description'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Concise, authoritative description of your business and industry focus.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_url"><?php esc_html_e( 'Canonical Website URL', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[url]" type="url" id="dsg_url" value="<?php echo esc_url( $business['url'] ); ?>" class="regular-text">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_logo_url"><?php esc_html_e( 'Brand Logo URL', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[logo_url]" type="url" id="dsg_logo_url" value="<?php echo esc_url( $business['logo_url'] ); ?>" class="regular-text">
						<p class="description"><?php esc_html_e( 'Publicly accessible URL to a square or rectangular brand logo image.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_telephone"><?php esc_html_e( 'Public Phone Number', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[telephone]" type="text" id="dsg_telephone" value="<?php echo esc_attr( $business['telephone'] ); ?>" class="regular-text" placeholder="+1-555-123-4567">
					</td>
				</tr>

				<tr>
					<th scope="row"><label for="dsg_email"><?php esc_html_e( 'Public Support Email', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[email]" type="email" id="dsg_email" value="<?php echo esc_attr( $business['email'] ); ?>" class="regular-text">
					</td>
				</tr>
			</table>
		</div>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Physical Location & Address', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="dsg_street_address"><?php esc_html_e( 'Street Address', 'dynamic-schema-generator' ); ?></label></th>
					<td><input name="dsg_business[street_address]" type="text" id="dsg_street_address" value="<?php echo esc_attr( $business['street_address'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_address_locality"><?php esc_html_e( 'City / Locality', 'dynamic-schema-generator' ); ?></label></th>
					<td><input name="dsg_business[address_locality]" type="text" id="dsg_address_locality" value="<?php echo esc_attr( $business['address_locality'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_address_region"><?php esc_html_e( 'State / Province / Region', 'dynamic-schema-generator' ); ?></label></th>
					<td><input name="dsg_business[address_region]" type="text" id="dsg_address_region" value="<?php echo esc_attr( $business['address_region'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_postal_code"><?php esc_html_e( 'Postal / ZIP Code', 'dynamic-schema-generator' ); ?></label></th>
					<td><input name="dsg_business[postal_code]" type="text" id="dsg_postal_code" value="<?php echo esc_attr( $business['postal_code'] ); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_address_country"><?php esc_html_e( 'Country Code (ISO 2-letter)', 'dynamic-schema-generator' ); ?></label></th>
					<td><input name="dsg_business[address_country]" type="text" id="dsg_address_country" value="<?php echo esc_attr( $business['address_country'] ); ?>" class="small-text" maxlength="2" placeholder="US"></td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_latitude"><?php esc_html_e( 'Geographic Coordinates', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[latitude]" type="text" id="dsg_latitude" value="<?php echo esc_attr( $business['latitude'] ); ?>" placeholder="Latitude (e.g. 31.5204)" class="regular-text" style="width:180px;">
						<input name="dsg_business[longitude]" type="text" id="dsg_longitude" value="<?php echo esc_attr( $business['longitude'] ); ?>" placeholder="Longitude (e.g. 74.3587)" class="regular-text" style="width:180px;">
						<p class="description"><?php esc_html_e( 'Exact latitude and longitude for Google Maps / local 3-pack proximity accuracy.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<div class="dsg-card">
			<h2><?php esc_html_e( 'Social Profiles & Authority Links (sameAs)', 'dynamic-schema-generator' ); ?></h2>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="dsg_same_as"><?php esc_html_e( 'Profile URLs', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<textarea name="dsg_business[same_as]" id="dsg_same_as" rows="4" class="large-text" placeholder="https://www.linkedin.com/company/example&#10;https://x.com/example&#10;https://github.com/example"><?php echo esc_textarea( implode( "\n", (array) $business['same_as'] ) ); ?></textarea>
						<p class="description"><?php esc_html_e( 'Enter one official social media or directory URL per line. These feed the Schema.org "sameAs" property for knowledge graph establishment.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_area_served"><?php esc_html_e( 'Primary Service Area', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[area_served]" type="text" id="dsg_area_served" value="<?php echo esc_attr( $business['area_served'] ); ?>" class="regular-text" placeholder="e.g. United States, Texas, or Greater London">
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="dsg_price_range"><?php esc_html_e( 'Price Range', 'dynamic-schema-generator' ); ?></label></th>
					<td>
						<input name="dsg_business[price_range]" type="text" id="dsg_price_range" value="<?php echo esc_attr( $business['price_range'] ); ?>" class="small-text" placeholder="$$">
						<p class="description"><?php esc_html_e( 'General price level indicator ($, $$, $$$, or $$$$) for LocalBusiness.', 'dynamic-schema-generator' ); ?></p>
					</td>
				</tr>
			</table>
		</div>

		<p class="submit">
			<button type="submit" name="dsg_save_business" class="button button-primary button-large"><?php esc_html_e( 'Save Business Information', 'dynamic-schema-generator' ); ?></button>
		</p>
	</form>
</div>
