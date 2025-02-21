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

	// Initialization Methods.

	/**
	 * Class initialization method.
	 */
	public function init(): void {
		$this->action_name = 'customer-product-submit';

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
		add_action( 'woocommerce_account_customer-product-new_endpoint', [ $this, 'display_customer_product_form' ] );

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
		return [ 'customer-product-new' => __( 'Add Product', 'wpmpw' ) ] + $pages;
	}

	/**
	 * Register endpoints for the customer's 'Add Product' page.
	 */
	public function register_customer_product_page_endpoints(): void {
		add_rewrite_endpoint( 'customer-product-new', EP_ROOT | EP_PAGES );
	}

	/**
	 * Displays customer products.
	 */
	public function display_customer_product_form(): void {
		$args = [
			'action' => $this->action_name,
		];

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

		// Prepare an array for input.
		$post_data = [
			'post_author'  => $user_id,
			'post_type'    => 'product',
			'post_status'  => 'pending',
			'post_title'   => sanitize_text_field( wp_unslash( $_POST['product-name'] ?? '0' ) ),
			'post_content' => wp_kses_post( wp_unslash( $_POST['product-description'] ?? '' ) ),
			'meta_input'   => [
				'_sku'           => sanitize_text_field( wp_unslash( $_POST['product-price'] ?? '0' ) ),
				'_price'         => sanitize_text_field( wp_unslash( $_POST['product-quantity'] ?? '0' ) ),
				'_regular_price' => sanitize_text_field( wp_unslash( $_POST['product-quantity'] ?? '0' ) ),
			],
		];

		// Check whether the post was edited.
		$is_edit = sanitize_text_field( wp_unslash( $_POST['status'] ?? '0' ) ) !== '0';

		// Check whether the post id exists and whether it is edited by its author.
		if ( $is_edit ) {
			$post_id = sanitize_text_field( wp_unslash( $_POST['post_id'] ?? '0' ) );

			if ( ! get_post_status( $post_id ) ) {
				return;
			}

			if ( get_post_field( 'post_author', $post_id ) !== $user_id ) {
				return;
			}

			$post_data['ID'] = $post_id;
		}

		// Submit new post.
		$post_id = wp_insert_post( $post_data );
		// Email the website administrator about submission.
		$this->send_admin_product_submission_notification_email( $post_id, $user_id );
	}

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
