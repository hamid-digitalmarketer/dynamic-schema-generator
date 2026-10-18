<?php
/**
 * Admin interface and settings pages controller.
 *
 * @package Dynamic_Schema_Generator
 */

namespace DSG;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Admin
 */
class Admin {

	/**
	 * Register hooks.
	 */
	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'register_admin_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_action( 'admin_init', array( $this, 'handle_form_submissions' ) );

		// AJAX handlers
		add_action( 'wp_ajax_dsg_validate_schema', array( $this, 'ajax_validate_schema' ) );
		add_action( 'wp_ajax_dsg_get_preview', array( $this, 'ajax_get_preview' ) );
		add_action( 'wp_ajax_dsg_clear_logs', array( $this, 'ajax_clear_logs' ) );

		// Post edit screen meta boxes
		add_action( 'add_meta_boxes', array( $this, 'add_post_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_meta_box_data' ) );
	}

	/**
	 * Register menu pages.
	 */
	public function register_admin_menus() {
		$cap = 'manage_options';

		add_menu_page(
			__( 'Dynamic Schema', 'dynamic-schema-generator' ),
			__( 'Dynamic Schema', 'dynamic-schema-generator' ),
			$cap,
			'dsg-dashboard',
			array( $this, 'render_dashboard_page' ),
			'dashicons-code-standards',
			85
		);

		add_submenu_page( 'dsg-dashboard', __( 'Dashboard', 'dynamic-schema-generator' ), __( 'Dashboard', 'dynamic-schema-generator' ), $cap, 'dsg-dashboard', array( $this, 'render_dashboard_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Business Info', 'dynamic-schema-generator' ), __( 'Business Info', 'dynamic-schema-generator' ), $cap, 'dsg-business', array( $this, 'render_business_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Schema Settings', 'dynamic-schema-generator' ), __( 'Schema Settings', 'dynamic-schema-generator' ), $cap, 'dsg-settings', array( $this, 'render_settings_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Services', 'dynamic-schema-generator' ), __( 'Services', 'dynamic-schema-generator' ), $cap, 'dsg-services', array( $this, 'render_services_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'FAQs', 'dynamic-schema-generator' ), __( 'FAQs', 'dynamic-schema-generator' ), $cap, 'dsg-faqs', array( $this, 'render_faqs_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Elementor', 'dynamic-schema-generator' ), __( 'Elementor', 'dynamic-schema-generator' ), $cap, 'dsg-elementor', array( $this, 'render_elementor_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Schema Preview', 'dynamic-schema-generator' ), __( 'Schema Preview', 'dynamic-schema-generator' ), $cap, 'dsg-preview', array( $this, 'render_preview_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Diagnostics', 'dynamic-schema-generator' ), __( 'Diagnostics', 'dynamic-schema-generator' ), $cap, 'dsg-diagnostics', array( $this, 'render_diagnostics_page' ) );
		add_submenu_page( 'dsg-dashboard', __( 'Documentation', 'dynamic-schema-generator' ), __( 'Documentation', 'dynamic-schema-generator' ), $cap, 'dsg-docs', array( $this, 'render_docs_page' ) );
	}

	/**
	 * Enqueue assets only on plugin admin screens.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		if ( strpos( $hook_suffix, 'dsg-' ) === false && 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'dsg-admin-style',
			DSG_PLUGIN_URL . 'admin/css/admin-style.css',
			array(),
			DSG_VERSION
		);

		wp_enqueue_script(
			'dsg-admin-script',
			DSG_PLUGIN_URL . 'admin/js/admin-script.js',
			array( 'jquery' ),
			DSG_VERSION,
			true
		);

		wp_localize_script(
			'dsg-admin-script',
			'dsgAdmin',
			array(
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'dsg_admin_nonce' ),
				'copiedMsg' => __( 'Copied to clipboard!', 'dynamic-schema-generator' ),
				'clearedMsg'=> __( 'Diagnostics log cleared.', 'dynamic-schema-generator' ),
			)
		);
	}

	/**
	 * Handle admin form POST submissions with capability and nonce checks.
	 */
	public function handle_form_submissions() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Save Settings
		if ( isset( $_POST['dsg_save_settings'] ) && check_admin_referer( 'dsg_settings_action', 'dsg_settings_nonce' ) ) {
			$raw = isset( $_POST['dsg_settings'] ) && is_array( $_POST['dsg_settings'] ) ? $_POST['dsg_settings'] : array();
			Settings::update( $raw );
			Diagnostics::log( 'Plugin settings updated successfully.' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-settings', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// Save Business Info
		if ( isset( $_POST['dsg_save_business'] ) && check_admin_referer( 'dsg_business_action', 'dsg_business_nonce' ) ) {
			$raw = isset( $_POST['dsg_business'] ) && is_array( $_POST['dsg_business'] ) ? $_POST['dsg_business'] : array();
			Business_Manager::save_info( $raw );
			Diagnostics::log( 'Business information updated successfully.' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-business', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// Save Service
		if ( isset( $_POST['dsg_save_service'] ) && check_admin_referer( 'dsg_service_action', 'dsg_service_nonce' ) ) {
			$raw = isset( $_POST['dsg_service'] ) && is_array( $_POST['dsg_service'] ) ? $_POST['dsg_service'] : array();
			Service_Manager::save( $raw );
			Diagnostics::log( 'Service entry saved: ' . ( isset( $raw['name'] ) ? sanitize_text_field( $raw['name'] ) : '' ) );
			wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-services', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// Delete Service
		if ( isset( $_GET['dsg_action'] ) && 'delete_service' === $_GET['dsg_action'] && isset( $_GET['id'] ) ) {
			if ( check_admin_referer( 'dsg_delete_service_' . $_GET['id'] ) ) {
				Service_Manager::delete( sanitize_key( $_GET['id'] ) );
				wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-services', 'deleted' => '1' ), admin_url( 'admin.php' ) ) );
				exit;
			}
		}

		// Save FAQ
		if ( isset( $_POST['dsg_save_faq'] ) && check_admin_referer( 'dsg_faq_action', 'dsg_faq_nonce' ) ) {
			$raw = isset( $_POST['dsg_faq'] ) && is_array( $_POST['dsg_faq'] ) ? $_POST['dsg_faq'] : array();
			FAQ_Manager::save( $raw );
			Diagnostics::log( 'FAQ entry saved.' );
			wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-faqs', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// Delete FAQ
		if ( isset( $_GET['dsg_action'] ) && 'delete_faq' === $_GET['dsg_action'] && isset( $_GET['id'] ) ) {
			if ( check_admin_referer( 'dsg_delete_faq_' . $_GET['id'] ) ) {
				FAQ_Manager::delete( sanitize_key( $_GET['id'] ) );
				wp_safe_redirect( add_query_arg( array( 'page' => 'dsg-faqs', 'deleted' => '1' ), admin_url( 'admin.php' ) ) );
				exit;
			}
		}
	}

	/**
	 * AJAX: Validate user-supplied JSON-LD payload.
	 */
	public function ajax_validate_schema() {
		check_ajax_referer( 'dsg_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'dynamic-schema-generator' ) ) );
		}

		$payload = isset( $_POST['schema_json'] ) ? wp_unslash( $_POST['schema_json'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$report  = Schema_Validator::validate( $payload );

		wp_send_json_success( $report );
	}

	/**
	 * AJAX: Get generated schema preview for a post or site.
	 */
	public function ajax_get_preview() {
		check_ajax_referer( 'dsg_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'dynamic-schema-generator' ) ) );
		}

		$post_id   = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
		$generator = new Schema_Generator();
		$graph     = $generator->generate( $post_id ? $post_id : null );

		$json   = wp_json_encode( $graph, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		$report = Schema_Validator::validate( $graph );

		wp_send_json_success( array(
			'json'       => $json,
			'validation' => $report,
		) );
	}

	/**
	 * AJAX: Clear diagnostics log.
	 */
	public function ajax_clear_logs() {
		check_ajax_referer( 'dsg_admin_nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized access.', 'dynamic-schema-generator' ) ) );
		}

		Diagnostics::clear_logs();
		wp_send_json_success();
	}

	/**
	 * Add post meta box for schema exclusion / custom FAQs.
	 */
	public function add_post_meta_boxes() {
		$settings = Settings::get_all();
		$types    = isset( $settings['post_types'] ) ? (array) $settings['post_types'] : array( 'post', 'page' );

		foreach ( $types as $post_type ) {
			add_meta_box(
				'dsg_schema_meta',
				__( 'Dynamic Schema Generator', 'dynamic-schema-generator' ),
				array( $this, 'render_post_meta_box' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * Render post meta box contents.
	 *
	 * @param \WP_Post $post Current post.
	 */
	public function render_post_meta_box( $post ) {
		wp_nonce_field( 'dsg_meta_box_action', 'dsg_meta_box_nonce' );

		$excluded = get_post_meta( $post->ID, '_dsg_exclude_schema', true );
		$faqs     = FAQ_Manager::get_by_post_id( $post->ID );
		?>
		<p>
			<label>
				<input type="checkbox" name="dsg_exclude_schema" value="1" <?php checked( $excluded, '1' ); ?>>
				<strong><?php esc_html_e( 'Disable Schema Output for this page', 'dynamic-schema-generator' ); ?></strong>
			</label>
			<br>
			<span class="description"><?php esc_html_e( 'Check this box if you want to suppress all JSON-LD output from this plugin on this specific page.', 'dynamic-schema-generator' ); ?></span>
		</p>
		<hr>
		<h4><?php esc_html_e( 'Page-Specific FAQs', 'dynamic-schema-generator' ); ?></h4>
		<p class="description"><?php esc_html_e( 'Add FAQ pairs below to emit an FAQPage schema for this page. If you use Elementor Accordion/Toggle widgets, they will be extracted automatically if this section is empty.', 'dynamic-schema-generator' ); ?></p>
		<div id="dsg-post-faqs-wrap">
			<?php
			if ( ! empty( $faqs ) ) {
				foreach ( $faqs as $i => $item ) {
					?>
					<div class="dsg-faq-item-row" style="margin-bottom:10px;padding:10px;background:#f9f9f9;border:1px solid #ddd;">
						<p><label><strong><?php esc_html_e( 'Question:', 'dynamic-schema-generator' ); ?></strong></label><br>
						<input type="text" name="dsg_post_faqs[<?php echo absint( $i ); ?>][question]" value="<?php echo esc_attr( $item['question'] ); ?>" class="widefat"></p>
						<p><label><strong><?php esc_html_e( 'Answer:', 'dynamic-schema-generator' ); ?></strong></label><br>
						<textarea name="dsg_post_faqs[<?php echo absint( $i ); ?>][answer]" rows="3" class="widefat"><?php echo esc_textarea( $item['answer'] ); ?></textarea></p>
					</div>
					<?php
				}
			}
			?>
		</div>
		<?php
	}

	/**
	 * Save post meta box values.
	 *
	 * @param int $post_id Post ID.
	 */
	public function save_post_meta_box_data( $post_id ) {
		if ( ! isset( $_POST['dsg_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['dsg_meta_box_nonce'], 'dsg_meta_box_action' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$excluded = ! empty( $_POST['dsg_exclude_schema'] ) ? '1' : '0';
		update_post_meta( $post_id, '_dsg_exclude_schema', $excluded );

		if ( isset( $_POST['dsg_post_faqs'] ) && is_array( $_POST['dsg_post_faqs'] ) ) {
			FAQ_Manager::save_post_faqs( $post_id, $_POST['dsg_post_faqs'] );
		}
	}

	// View renderers
	public function render_dashboard_page()   { $this->load_view( 'dashboard' ); }
	public function render_business_page()    { $this->load_view( 'business-info' ); }
	public function render_settings_page()    { $this->load_view( 'schema-settings' ); }
	public function render_services_page()    { $this->load_view( 'services' ); }
	public function render_faqs_page()        { $this->load_view( 'faqs' ); }
	public function render_elementor_page()   { $this->load_view( 'elementor' ); }
	public function render_preview_page()     { $this->load_view( 'preview' ); }
	public function render_diagnostics_page() { $this->load_view( 'diagnostics' ); }
	public function render_docs_page()        { $this->load_view( 'docs' ); }

	protected function load_view( $view ) {
		$file = DSG_PLUGIN_DIR . 'admin/views/' . $view . '.php';
		if ( file_exists( $file ) ) {
			include $file;
		}
	}
}
