<?php
/**
 * Product List template
 *
 * @package wpmpw/plugin
 * @since 0.0.1
 */

if ( empty( $args ) ) {
	return;
}

?>
<table>
	<thead>
		<tr>
			<th>
				<?php esc_html_e( 'Name', 'wpmpw' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Quantity', 'wpmpw' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Price', 'wpmpw' ); ?>
			</th>
			<th>
				<?php esc_html_e( 'Status', 'wpmpw' ); ?>
			</th>
			<th></th><th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $args['query']->products as $product ) : ?>
			<tr>
				<td>
					<?php echo esc_html( $product->get_title() ); ?>
				</td>
				<td>
					<?php echo esc_html( $product->get_sku() ); ?>
				</td>
				<td>
					<?php echo wp_kses_post( $product->get_price_html() ); ?>
				</td>
				<td>
					<?php echo esc_html( ucfirst( $product->get_status() ) ); ?>
				</td>
				<td>
					<a href="<?php echo esc_url( $args['url_edit'] . '?id=' . $product->get_id() ); ?>" class="edit-product">
						<?php esc_html_e( 'Edit', 'wpmpw' ); ?>
					</a>
				</td>
				<td>
					<a href="<?php echo esc_url( get_delete_post_link( $product->get_id() ) ); ?>" class="edit-product">
						<?php esc_html_e( 'Delete', 'wpmpw' ); ?>
					</a>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<br>
<?php if ( $args['query']->max_num_pages > 1 ) : ?>
	<p class="pagination">
		<?php if ( $args['page'] > 1 ) : ?>
			<a class="prev" href="<?php echo esc_url( $args['url'] . '?index=' . ( $args['page'] - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'wpmpw' ); ?></a>
		<?php endif; ?>
		<span class="curr">
			<?php echo esc_html( $args['page'] ); ?>
		</span>
		<?php if ( $args['page'] < $args['query']->max_num_pages ) : ?>
			<a class="next" href="<?php echo esc_url( $args['url'] . '?index=' . ( $args['page'] + 1 ) ); ?>"><?php esc_html_e( 'Next', 'wpmpw' ); ?></a>
		<?php endif; ?>
	</p>
<?php endif; ?>
