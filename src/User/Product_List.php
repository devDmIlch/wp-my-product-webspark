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

	// Private Fields.

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
		$this->endpoint_name = 'customer-products';

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
		add_action( "woocommerce_account_{$this->endpoint_name}_endpoint", [ $this, 'display_customer_product_list' ] );

		// Filter the title of the page to display correct one.
		add_filter( "woocommerce_endpoint_{$this->endpoint_name}_title", [ $this, 'set_customer_product_page_title' ] );
		// Add query variable to recognise when the page is loaded.
		add_filter( 'woocommerce_get_query_vars', [ $this, 'set_query_var' ] );
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
		return [ $this->endpoint_name => __( 'My Products', 'wpmpw' ) ] + $pages;
	}

	/**
	 * Register endpoint for the customer's 'My Products' page.
	 */
	public function register_customer_product_list_endpoints(): void {
		add_rewrite_endpoint( $this->endpoint_name, EP_ROOT | EP_PAGES );
	}

	/**
	 * Displays customer products.
	 */
	public function display_customer_product_list(): void {
		// Get the current pagination value.
		$page = sanitize_text_field( wp_unslash( $_GET['index'] ?? '1' ) ); // phpcs:ignore

		// Create query for the customer products.
		$query = new \WC_Product_Query(
			[
				'page'           => $page,
				'posts_per_page' => 10,
				'post_status'    => [ 'pending', 'publish' ],
				'author'         => get_current_user_id(),
				'paginate'       => true,
			]
		);

		$args = [
			'query'    => $query->get_products(),
			'page'     => $page,
			'url'      => wc_get_endpoint_url( $this->endpoint_name ),
			'url_edit' => wc_get_endpoint_url( 'customer-product-new' ),
		];

		load_template( WPMPW_PLUGIN_TEMPLATES . 'user/product-list.php', true, $args );

		// Clear query parameters.
		wp_reset_postdata();
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
		return __( 'My products', 'wpmpw' );
	}
}
