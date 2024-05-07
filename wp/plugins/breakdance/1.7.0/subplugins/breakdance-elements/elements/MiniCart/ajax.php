<?php

namespace EssentialElements;

/**
 * Show cart contents / total Ajax
 */
add_filter('woocommerce_add_to_cart_fragments', '\EssentialElements\woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment($fragments)
{
    if (\Breakdance\isRequestFromBuilderIframe()) return $fragments;

    $subtotal = WC()->cart->get_cart_subtotal();
    $count = WC()->cart->get_cart_contents_count();
    $fragments['.bde-mini-cart-toggle__subtotal'] = "<span class='bde-mini-cart-toggle__subtotal' data-count='{$count}'>{$subtotal}</span>";
    $fragments['.bde-mini-cart-toggle__counter'] = "<span class='bde-mini-cart-toggle__counter' data-count='{$count}'>{$count}</span>";
    return $fragments;
}
