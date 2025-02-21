<?php
/**
 * Email template for customer product submission in plain.
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

echo esc_html( $email_heading ) . "\n\n";

echo esc_html( __( 'Submitted product details:', 'wpmpw' ) ) . "\n\n";

// translators: Placeholder here is replaced with <name of a product> when email is submitted.
echo esc_html( sprintf( __( 'Product Name: %s', 'wpmpw' ), $product->post_title ) ) . "\n\n";

// translators: Placeholder here is replaced with <email of a user> when email is submitted.
echo esc_html( sprintf( __( 'Submitted by: %s', 'wpmpw' ), esc_html( $author->user_email ) ) ) . "\n\n";

// translators: Placeholder here is replaced with <a link to the product> when email is submitted.
echo esc_html( sprintf( __( 'Link to the product: %s', 'wpmpw' ), esc_url( get_edit_post_link( $product->ID ) ) ) ) . "\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );
