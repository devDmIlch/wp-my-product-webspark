<?php
/**
 * New customer product submit form
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

// This template requires arguments.
if ( empty( $args ) ) {
	return;
}

$button_class = 'button' . wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';

?>
<form id="customer-product-submit" method="post">
	<?php wp_nonce_field( $args['action'] ); ?>
	<input type="hidden" name="action" value="<?php echo esc_attr( $args['action'] ); ?>">
	<input type="hidden" name="status" value="<?php echo esc_attr( $args['is-edit'] ?? false ? '1' : '0' ); ?>">
	<input type="hidden" name="post-id" value="<?php echo esc_attr( $args['post-id'] ?? 0 ); ?>">
	<?php

	woocommerce_form_field(
		'product-name',
		[
			'label'    => __( 'Name', 'wpmpw' ),
			'default'  => $args['title'] ?? null,
			'class'    => 'form-row',
			'required' => true,
		]
	);

	woocommerce_form_field(
		'product-price',
		[
			'label'    => __( 'Price', 'wpmpw' ),
			'type'     => 'number',
			'default'  => $args['price'] ?? null,
			'class'    => 'form-row form-row-first',
			'required' => true,
		]
	);

	woocommerce_form_field(
		'product-quantity',
		[
			'label'    => __( 'Quantity', 'wpmpw' ),
			'type'     => 'number',
			'default'  => $args['quantity'] ?? null,
			'class'    => 'form-row form-row-last',
			'required' => true,
		]
	);

	?>
	<p class="form-row">
		<label for="product-description">
			<?php esc_html_e( 'Product Description', 'wpmpw' ); ?>
		</label>
		<?php
		wp_editor(
			$args['description'] ?? '',
			'product-description',
			[
				'media_buttons'    => false,
				'drag_drop_upload' => false,
				'quicktags'        => false,
			]
		);
		?>
	</p>
	<p class="form-row">
		<a class="<?php echo esc_attr( $button_class ); ?>">
			<?php esc_html_e( 'Select Thumbnail', 'wpmpw' ); ?>
		</a>
	</p>
	<br>
	<p>
		<button type="submit" class="<?php echo esc_attr( $button_class ); ?>" name="save_address" value="<?php esc_attr_e( 'Save address', 'woocommerce' ); ?>">
			<?php echo esc_html( ( $args['is-edit'] ?? false ) ? __( 'Update Product', 'wpmpw' ) : __( 'Submit Product', 'wpmpw' ) ); ?>
		</button>
	</p>
</form>
