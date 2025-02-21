<?php
/**
 * Email class for customer product submission notifications.
 *
 * @package wpmpw/theme
 * @since 0.0.1
 */

namespace WPMPW\Emails;

/**
 * Email class for customer product submission notifications.
 */
class WC_Customer_Product_Email extends \WC_Email {

	// Public Fields.

	/**
	 * ID of an author submitting the product.
	 *
	 * @var \WP_User
	 */
	public \WP_User $author;


	// Initialization Methods.

	/**
	 * Class initialization method.
	 */
	public function init(): void {
		$this->id          = 'wc_customer_product_email';
		$this->title       = __( 'New Product Submission', 'wpmpw' );
		$this->description = __( 'A customer has just submitted a product', 'wpmpw' );
		$this->heading     = __( 'New Product has been submitted', 'wpmpw' );
		$this->subject     = __( 'New product has been submitted', 'wpmpw' );
		$this->recipient   = get_bloginfo( 'admin_email' );

		$this->customer_email = false;

		// Template paths.
		$this->template_html  = 'emails/wc_customer_product_email.php';
		$this->template_plain = 'emails/plain/wc_customer_product_email.php';
		$this->template_base  = WPMPW_PLUGIN_TEMPLATES;

		// Initialize class hooks.
		$this->hooks();
	}

	/**
	 * Class hooks initialization method.
	 */
	protected function hooks(): void {
		// Use custom action to trigger email sending.
		add_action( 'wpmpw_customer_submitted_product', [ $this, 'trigger' ], 10, 2 );
	}


	// Public Methods.

	/**
	 * Sends data about submitted customer product.
	 *
	 * @param int $product_id ID of the submitted customer product.
	 * @param int $author_id  ID of the submission's author.
	 */
	public function trigger( int $product_id, int $author_id ): void {
		// Set object to submitted product id.
		$this->object = get_post( $product_id );

		// Abort sending if the email is disabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		// Save an author of the submission.
		$this->author = get_user_by( 'id', $author_id );

		$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}


	/**
	 * Retrieves content of an email.
	 *
	 * @return string
	 */
	public function get_content_html(): string {
		return wc_get_template_html(
			$this->template_html,
			[
				'product'       => $this->object,
				'author'        => $this->author,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			],
			'',
			$this->template_base
		);
	}

	/**
	 * Retrieves content of an email in plain.
	 *
	 * @return string
	 */
	public function get_content_plain(): string {
		return wc_get_template_html(
			$this->template_plain,
			[
				'product'       => $this->object,
				'author'        => $this->author,
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			],
			'',
			$this->template_base
		);
	}
}
