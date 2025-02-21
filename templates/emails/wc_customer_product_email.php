<?php
/**
 * Email template for customer product submission.
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<h3>
	<?php // translators: Placeholder stands for the <name of the product>. ?>
	<?php echo esc_html( sprintf( __( 'Product Name: %s', 'wpmpw' ), $product->post_title ) ); ?>
</h3>
<p>
	<?php esc_html_e( 'Submitted by: ' ); ?>
	<a href="<?php echo esc_url( get_edit_user_link( $author->ID ) ); ?>"><?php echo esc_html( $author->user_email ); ?></a>
</p>
<p>
	<a href="<?php echo esc_url( get_edit_post_link( $product->ID ) ); ?>">
		<?php esc_html_e( 'Link to the product', 'wpmpw' ); ?>
	</a>
</p>
<?php
do_action( 'woocommerce_email_footer' );
