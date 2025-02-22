<?php
/**
 * Customer Product class file.
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

namespace WPMPW\User;

use WP_Error;

/**
 * Controls functionality related to the customer created products.
 */
class Product_New {

	// Private Fields.

	/**
	 * Name of the form action.
	 *
	 * @var string
	 */
	private string $action_name;

	/**
	 * Page endpoint name.
	 *
	 * @var string
	 */
	private string $endpoint_name;


	// Initialization Methods.

	/**
	 * Class initialization method.
	 */
	public function init(): void {
		$this->action_name   = 'customer-product-submit';
		$this->endpoint_name = 'customer-product-new';

		// Initialize hooks.
		$this->hooks();
	}

	/**
	 * Class hooks initialization method.
	 */
	protected function hooks(): void {
		// Register 'My Products' and 'Add Products' pages.
		add_filter( 'woocommerce_account_menu_items', [ $this, 'register_customer_product_pages' ], 20 );
		// Register endpoints for 'My Products' and ' Add Products' pages.
		add_action( 'init', [ $this, 'register_customer_product_page_endpoints' ] );
		// Display content for the 'My Products' page.
		add_action( "woocommerce_account_{$this->endpoint_name}_endpoint", [ $this, 'display_customer_product_form' ] );

		// Filter the title of the page to display correct one.
		add_filter( "woocommerce_endpoint_{$this->endpoint_name}_title", [ $this, 'set_customer_product_page_title' ] );
		// Add query variable to recognise when the page is loaded.
		add_filter( 'woocommerce_get_query_vars', [ $this, 'set_query_var' ] );

		// Add post submission listener.
		add_action( 'init', [ $this, 'handle_customer_product_submission' ] );
	}


	// Public Methods.

	/**
	 * Registers customer 'Add product' page that allows them to create new product.
	 *
	 * @param array $pages Existing subpages for 'My Account'.
	 *
	 * @return array Modified array of subpages included with 'Add Product' page.
	 */
	public function register_customer_product_pages( array $pages ): array {
		return [ $this->endpoint_name => __( 'Add Product', 'wpmpw' ) ] + $pages;
	}

	/**
	 * Register endpoints for the customer's 'Add Product' page.
	 */
	public function register_customer_product_page_endpoints(): void {
		add_rewrite_endpoint( $this->endpoint_name, EP_ROOT | EP_PAGES );
	}

	/**
	 * Set's query variable with endpoint name.
	 *
	 * @param array $query_vars Query parameters.
	 *
	 * @return array Modified array of query parameters.
	 */
	public function set_query_var( array $query_vars ): array {
		$query_vars[ $this->endpoint_name ] = $this->endpoint_name;

		return $query_vars;
	}

	/**
	 * Sets page title to the correct value, if on the endpoint page.
	 *
	 * @param string $title Default page title.
	 *
	 * @return string Modified title.
	 */
	public function set_customer_product_page_title( string $title ): string {
		return __( 'Add Product', 'wpmpw' );
	}

	/**
	 * Displays customer products.
	 */
	public function display_customer_product_form(): void {
		$args = [
			'action' => $this->action_name,
		];

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['id'] ) ) {
			$post_id = sanitize_text_field( wp_unslash( $_GET['id'] ?? '0' ) );

			// Check if the post exist and the author of the post matches current logged in user.
			if ( (int) get_post_field( 'post_author', $post_id ) === get_current_user_id() ) {
				$product_obj = new \WC_Product_Simple( $post_id );

				$args = [
					'action'      => $this->action_name,
					'is-edit'     => true,
					'post-id'     => $post_id,
					'title'       => $product_obj->get_title(),
					'description' => $product_obj->get_description(),
					'price'       => $product_obj->get_price(),
					'quantity'    => $product_obj->get_sku(),
				];
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		// Add a check whether the user is high enough level to allow them to upload media via wp.media.
		$args['can-upload-media'] = current_user_can( 'upload_files' );

		load_template( WPMPW_PLUGIN_TEMPLATES . 'user/product-new.php', true, $args );
	}

	/**
	 * Handles customer product submission.
	 */
	public function handle_customer_product_submission(): void {
		// Check whether the form was submitted.
		if ( ! isset( $_POST['action'] ) || sanitize_text_field( wp_unslash( $_POST['action'] ) ) !== $this->action_name ) { // phpcs:ignore
			return;
		}

		// Check the nonce.
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), $this->action_name ) ) {
			$errors = new WP_Error();
			$errors->add( 'error_nonce', 'Invalid nonce.' );
			return;
		}

		$user_id = get_current_user_id();

		// Check whether the post was edited.
		$is_edit = sanitize_text_field( wp_unslash( $_POST['status'] ?? '0' ) ) !== '0';

		$post_id = 0;
		// Check whether the post id exists and whether it is edited by its author.
		if ( $is_edit ) {
			$post_id = sanitize_text_field( wp_unslash( $_POST['post-id'] ?? '0' ) );

			if ( (int) get_post_field( 'post_author', $post_id ) !== $user_id ) {
				return;
			}
		}

		// Bail Is the required fields are not filled.
		if ( ! isset( $_POST['product-name'], $_POST['product-quantity'], $_POST['product-price'] ) ) {
			return;
		}

		// Create and populate new product.
		$product_obj = new \WC_Product_Simple( $post_id );
		$product_obj->set_name( sanitize_text_field( wp_unslash( $_POST['product-name'] ) ) );
		$product_obj->set_status( 'pending' );
		$product_obj->set_description( wp_kses_post( wp_unslash( $_POST['product-description'] ?? '' ) ) );

		if ( isset( $_POST['thumbnail'] ) ) {
			$product_obj->set_image_id( sanitize_text_field( wp_unslash( $_POST['thumbnail'] ) ) );
		}

		$product_price = sanitize_text_field( wp_unslash( $_POST['product-price'] ) );
		$product_obj->set_price( $product_price );
		$product_obj->set_regular_price( $product_price );

		try {
			$product_obj->set_sku( sanitize_text_field( wp_unslash( $_POST['product-quantity'] ) ) );
		} catch ( \WC_Data_Exception $exception ) {
			return;
		}

		// Save the product.
		$product_obj->save();

		// Set post author.
		wp_update_post(
			[
				'ID'          => $product_obj->get_id(),
				'post_author' => $user_id,
			]
		);

		// Email the website administrator about submission.
		$this->send_admin_product_submission_notification_email( $product_obj->get_id(), $user_id );
	}


	// Private Methods.

	/**
	 * Sends a notification about submitted product form to website admin.
	 *
	 * @param int $post_id ID of the product submitted.
	 * @param int $user_id ID of the submission author.
	 */
	private function send_admin_product_submission_notification_email( int $post_id, int $user_id ): void {
		WC()->mailer()->emails['wc_customer_product_email']->trigger( $post_id, $user_id );
	}
}
