<?php
/**
 * Plugin Name: WebSpark WooCommerce Plugin
 * Plugin URI: https://github.com/devDmIlch/wp-my-product-webspark
 * Description: test plugin
 * Author: Dmitrii Ilchenko
 * Version: 0.0.1
 * Author URI: TG: @chSnake
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

// Disallow direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin Constants.
if ( ! defined( 'WPMPW_PLUGIN_FILE' ) ) {
	define( 'WPMPW_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'WPMPW_PLUGIN_TEMPLATES' ) ) {
	define( 'WPMPW_PLUGIN_TEMPLATES', __DIR__ . '/templates/' );
}

require __DIR__ . '/vendor/autoload.php';

if ( ! class_exists( 'WP_My_Product_Webspark' ) ) {

	/**
	 * Plugin's main class.
	 */
	final class WP_My_Product_Webspark {

		// Initialization Methods.

		/**
		 * Class initialization method.
		 */
		public function init(): void {

			// Check if the woocommerce is installed.
			if ( ! $this->verify_woocommerce_activation() ) {
				// Add a notification message about missing dependency.
				add_action( 'admin_notices', [ $this, 'display_woocommerce_inactive_notice' ] );
				// Abort further plugin job.
				return;
			}

			$customer_products = new \WPMPW\User\Product_New();
			$customer_products->init();

			$customer_product_list = new \WPMPW\User\Product_List();
			$customer_product_list->init();

			// Initialize plugin hooks.
			$this->hooks();
		}

		/**
		 * Class hooks initialization method.
		 */
		protected function hooks(): void {
			// Check whether the WooCommerce is active on plugin activation.
			register_activation_hook( WPMPW_PLUGIN_FILE, [ $this, 'verify_woocommerce_activation' ] );

			// Register custom plugin emails.
			add_filter( 'woocommerce_email_classes', [ $this, 'initialize_plugin_emails' ] );

			// Register script file.
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_script_files' ] );
		}


		// Private Methods.

		/**
		 * Checks whether the WooCommerce is active and deactivates plugin if it isn't.
		 */
		private function verify_woocommerce_activation(): bool {
			return in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins', [] ), true );
		}


		// Public Methods.

		/**
		 * Displays notification to wp-admin with a notice about missing WooCommerce dependency.
		 */
		public function display_woocommerce_inactive_notice(): void {
			$message = __( 'WPMPW requires WooCommerce installed and activated.', 'wpmpw' );
			printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error' ), esc_html( $message ) );
		}

		/**
		 * Registers WC emails.
		 *
		 * @param array $emails List of existing WC emails.
		 *
		 * @return array List of existing WC emails appended with plugin emails.
		 */
		public function initialize_plugin_emails( array $emails ): array {
			$emails['wc_customer_product_email'] = new \WPMPW\Emails\WC_Customer_Product_Email();
			$emails['wc_customer_product_email']->init();

			return $emails;
		}

		/**
		 * Enqueues script file with gallery selector.
		 */
		public function enqueue_script_files(): void {
			// Enqueue media script.
			wp_enqueue_media();
			// Enqueue JavaScript.
			wp_enqueue_script(
				'wpmpw_scripts',
				plugins_url( '/js/index.js', WPMPW_PLUGIN_FILE ),
				[ 'wp-i18n', 'media-upload' ],
				'0.0.1',
				true
			);
		}
	}

	$wp_my_product_webspark = new WP_My_Product_Webspark();
	$wp_my_product_webspark->init();
}
