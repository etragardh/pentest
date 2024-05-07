<?php

/*
 * Plugin: woocommerce-paypal-payments
 * Move the PayPal buttons on Single Product page from `woocommerce_single_product_summary` to `woocommerce_after_add_to_cart_form` hook.
 * The hook `woocommerce_single_product_summary` is not available inside the Product Builder element.
 */
add_filter( 'woocommerce_paypal_payments_single_product_renderer_hook', function () {
    global $product;
    if ( ! is_object( $product ) ) {
        $product = wc_get_product( get_the_ID() );
    }

    if ($product->is_type('variable')) {
        return 'woocommerce_after_add_to_cart_button';
    }
    return 'woocommerce_after_add_to_cart_form';
});
