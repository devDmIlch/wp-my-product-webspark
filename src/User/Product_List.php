<?php
/**
 * Product List class file.
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

namespace WPMPW\User;

/**
 * Controls 'My Products' page in user account.
 */
class Product_List {

	// Initialization Methods.

	/**
	 * Class initialization method.
	 */
	public function init(): void {

		// Initialize hooks.
		$this->hooks();
	}

	/**
	 * Class hooks initialization method.
	 */
	protected function hooks(): void {
		// Register 'My Products' page.
		add_filter( 'woocommerce_account_menu_items', [ $this, 'register_customer_product_list_page' ], 30 );
		// Register endpoint for 'My Products' page.
		add_action( 'init', [ $this, 'register_customer_product_list_endpoints' ] );
		// Display content for the 'My Products' page.
		add_action( 'woocommerce_account_customer-products_endpoint', [ $this, 'display_customer_product_list' ] );
	}


	// Public Methods.

	/**
	 * Registers customer products page that lists their created products.
	 *
	 * @param array $pages Existing subpages for 'My Account'.
	 *
	 * @return array Modified array of subpages included with 'My Products' page.
	 */
	public function register_customer_product_list_page( array $pages ): array {
		return [ 'customer-products' => __( 'My Products', 'wpmpw' ) ] + $pages;
	}

	/**
	 * Register endpoint for the customer's 'My Products' page.
	 */
	public function register_customer_product_list_endpoints(): void {
		add_rewrite_endpoint( 'customer-products', EP_ROOT | EP_PAGES );
	}

	/**
	 * Displays customer products.
	 */
	public function display_customer_product_list(): void {
		echo 'test 123';
	}
}
