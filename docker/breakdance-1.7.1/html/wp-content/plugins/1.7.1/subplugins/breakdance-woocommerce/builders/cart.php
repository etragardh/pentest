<?php

namespace Breakdance\WooCommerce\CartBuilder;

use function Breakdance\isRequestFromBuilderSsr;

function renderCartPart($element, $callback, $hideWhenEmpty = true) {
    if (!is_cart()) {
        \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
            'cart',
            $element::name(),
            "Cart"
        );

        return;
    }

    if ($hideWhenEmpty && WC()->cart->is_empty()) {
        return;
    }

    if (!defined('WOOCOMMERCE_CART')) {
        BreakdanceCartShortcode::output([]);
    }

    if (is_callable($callback)) {
        $callback();
    }
}

function totals()
{
    renderCartPart(
        \EssentialElements\WooCartTotals::class,
        function() {
            require_once __DIR__ . "/cart/totals.php";
        }
    );
}

function table()
{
    renderCartPart(
        \EssentialElements\WooCartContents::class,
        function() {
            require_once __DIR__ . "/cart/table.php";
        }
    );
}

function crossSell()
{
    renderCartPart(
        \EssentialElements\WooCartCrossSells::class,
        function() {
            woocommerce_cross_sell_display();
        }
    );
}

function emptyMessage()
{
    if (!WC()->cart->is_empty() && !isRequestFromBuilderSsr()) {
        return;
    }

    renderCartPart(
        \EssentialElements\WooCartEmptyMessage::class,
        function() {
            wc_get_template( 'cart/cart-empty.php' );
        },
        false
    );

}

class BreakdanceCartShortcode extends \WC_Shortcode_Cart {
    /**
     * Output the cart shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @throws \Exception
     */
    public static function output( $atts ) {
        // Constants.
        wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );

        $atts        = shortcode_atts( array(), $atts, 'woocommerce_cart' );
        $nonce_value = wc_get_var( $_REQUEST['woocommerce-shipping-calculator-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.

        // Update Shipping. Nonce check uses new value and old value (woocommerce-cart). @todo remove in 4.0.
        if ( ! empty( $_POST['calc_shipping'] ) && ( wp_verify_nonce( $nonce_value, 'woocommerce-shipping-calculator' ) || wp_verify_nonce( $nonce_value, 'woocommerce-cart' ) ) ) { // WPCS: input var ok.
            self::calculate_shipping();

            // Also calc totals before we check items so subtotals etc are up to date.
            WC()->cart->calculate_totals();
        }

        // Check cart items are valid.
        do_action( 'woocommerce_check_cart_items' );

        // Calc totals.
        WC()->cart->calculate_totals();

//        if ( WC()->cart->is_empty() ) {
//            wc_get_template( 'cart/cart-empty.php' );
//        } else {
//            wc_get_template( 'cart/cart.php' );
//        }
    }
}

