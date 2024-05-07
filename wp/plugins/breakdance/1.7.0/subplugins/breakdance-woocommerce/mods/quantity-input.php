<?php

namespace Breakdance\WooCommerce;

function registerQtyAssets()
{
    wp_enqueue_script('breakdance-woo-qty', BREAKDANCE_WOO_URL . 'js/quantity.js', [], false, true);
}
add_action('wp_enqueue_scripts', '\Breakdance\WooCommerce\registerQtyAssets');

function addQuantityInputToShopLoop()
{
    global $product;

    if (!$product->is_type('simple') || !$product->is_in_stock()) return;

    woocommerce_quantity_input([
        'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
        'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
        'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity()
    ]);
}

function addQuantityInputToMiniCart($html, $cart_item, $cart_item_key)
{
    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key);
    $product_subtotal = apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );

    $input = woocommerce_quantity_input(
        [
            'input_name'   => "cart[{$cart_item_key}][qty]",
            'input_value'  => $cart_item['quantity'],
            'max_value'    => $_product->get_max_purchase_quantity(),
            'min_value'    => '0',
            'product_name' => $_product->get_name(),
        ],
        $_product,
        false
    );

    $advanced = "<div class=\"bde-mini-cart-quantity\">{$input} {$product_subtotal}</div>";
    $simple = "<div class=\"bde-mini-cart-quantity-simple\">{$html}</div>";

    return $advanced . $simple;
}

add_action('woocommerce_after_shop_loop_item', function () {
    echo '<div class="bde-woo-product-footer">';
        do_action('breakdance_shop_loop_footer');
    echo '</div>';
});

// Move add to cart button inside footer div.
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
add_action('breakdance_shop_loop_footer', 'woocommerce_template_loop_add_to_cart');

// Add quantity input to shop loop
add_action('breakdance_shop_loop_footer', '\Breakdance\WooCommerce\addQuantityInputToShopLoop', 9);

// Add quantity input to mini cart
add_action('woocommerce_widget_cart_item_quantity', '\Breakdance\WooCommerce\addQuantityInputToMiniCart', 10, 3);
