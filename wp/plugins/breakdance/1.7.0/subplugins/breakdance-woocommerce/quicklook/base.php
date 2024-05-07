<?php

namespace Breakdance\WooCommerce\Quicklook;

require_once __DIR__ . "/frontend.php";

/**
 * Product Image
 *
 * @see woocommerce_show_product_sale_flash()
 * @see woocommerce_show_product_images()
 */
add_action( 'breakdance_quicklook_image', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'breakdance_quicklook_image', 'woocommerce_show_product_images', 20 );

/**
 * Product Summary
 *
 * @see woocommerce_template_single_title()
 * @see woocommerce_template_single_rating()
 * @see woocommerce_template_single_price()
 * @see woocommerce_template_single_excerpt()
 * @see woocommerce_template_single_add_to_cart()
 * @see woocommerce_template_single_meta()
 */
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_title', 5 );
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_rating', 10 );
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_price', 15 );
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_excerpt', 20 );
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_add_to_cart', 25 );
add_action( 'breakdance_quicklook_summary', 'woocommerce_template_single_meta', 30 );

function ajaxTemplate()
{
    ob_start();
    remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);
    wc_get_template_part('custom/quicklook-ajax');
    return ob_get_clean();
}

/**
 * @return array{html: string}
 */
function ajaxEndpoint()
{
    $productId = (int) $_REQUEST['productId'];

    if (!$productId) {
        return ['message' => 'Product not found'];
    }

    // Set the main query to be this product
    wp("p=$productId&post_type=product"); // TODO: Is this vulnerable to an attack/injection?

    // Set the global $product variable
    wc_setup_product_data($productId);

    return ['html' => ajaxTemplate()];
}
\Breakdance\AJAX\register_handler(
    'breakdance_quicklook',
    '\Breakdance\WooCommerce\Quicklook\ajaxEndpoint',
    'none',
    false,
    [],
    true
);

/**
 * AJAX add to cart.
 */
function addToCart()
{
    ob_start();

    // phpcs:disable WordPress.Security.NonceVerification.Missing
    if ( ! isset( $_POST['product_id'] ) ) {
        return;
    }

    $product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
    $product           = wc_get_product( $product_id );
    $quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
    $product_status    = get_post_status( $product_id );
    $variation_id      = 0;
    $variation         = [];

    foreach($_POST as $key => $value) {
        if (strpos($key, 'attribute_') === 0) {
            $variation[$key] = $value;
        }
    }

    if ( $product && 'variation' === $product->get_type() ) {
        $variation_id = $product_id;
        $product_id   = $product->get_parent_id();

        if ( empty( $variation ) ) {
            $variation = $product->get_variation_attributes();
        }
    }

    if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

        do_action( 'woocommerce_ajax_added_to_cart', $product_id );

        if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
            wc_add_to_cart_message( array( $product_id => $quantity ), true );
        }

        \WC_AJAX::get_refreshed_fragments();

    } else {

        // If there was an error adding to the cart, redirect to the product page to show any errors.
        $data = array(
            'error'       => true,
            'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
        );

        wp_send_json( $data );
    }
}
\Breakdance\AJAX\register_handler(
    'breakdance_add_to_cart',
    '\Breakdance\WooCommerce\Quicklook\addToCart',
    'none',
    [],
    true
);
