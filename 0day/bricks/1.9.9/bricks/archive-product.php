<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Use get_queried_object_id() instead of get_the_ID()
 *
 * get_the_ID() gets the post ID of the 1st query result in archive or taxonomy pages (@see #862jbfkby)
 *
 * Same as in Database::set_page_data()
 *
 * @since 1.7.1
 */
$post_id       = function_exists( 'is_shop' ) && is_shop() ? wc_get_page_id( 'shop' ) : get_queried_object_id();
$template_type = function_exists( 'is_shop' ) && is_shop() ? 'content' : 'wc_archive';
$bricks_data   = Bricks\Helpers::get_bricks_data( $post_id, $template_type );

if ( $bricks_data ) {
	// Set products query args according to "Products" element settings of this template
	do_action( 'bricks/archive_product/before', $bricks_data, $post_id );

	Bricks\Frontend::render_content( $bricks_data );

	do_action( 'bricks/archive_product/after', $bricks_data, $post_id );
}

// Default Product Archive (Shop page, product category/tag, etc.)
else {
	ob_start();
	?>
	<div class="brxe-container">
		<div class="bricks-archive-title-wrapper">
			<?php
			/**
			 * This should be from loop/header.php template via do_action( 'woocommerce_shop_loop_header' );
			 */
			if ( apply_filters( 'woocommerce_show_page_title', true ) ) {
				echo '<h1 class="title">' . woocommerce_page_title( false ) . '</h1>';
			}

			do_action( 'woocommerce_archive_description' );
			?>
		</div>

		<div class="brxe-woocommerce-products">
			<?php
			if ( woocommerce_product_loop() ) {
				echo '<div class="bricks-before-shop-loop">';
				do_action( 'woocommerce_before_shop_loop' );
				echo '</div>';

				woocommerce_product_loop_start();

				if ( wc_get_loop_prop( 'total' ) ) {
					while ( have_posts() ) {
						the_post();
						do_action( 'woocommerce_shop_loop' );
						wc_get_template_part( 'content', 'product' );
					}
				}

				woocommerce_product_loop_end();

				do_action( 'woocommerce_after_shop_loop' );
			} else {
				do_action( 'woocommerce_no_products_found' );
			}
			?>
		</div>
	</div>
	<?php
	$attributes = [ 'class' => 'layout-default' ];

	$html = ob_get_clean();

	Bricks\Frontend::render_content( [], $attributes, $html );
}

get_footer();
