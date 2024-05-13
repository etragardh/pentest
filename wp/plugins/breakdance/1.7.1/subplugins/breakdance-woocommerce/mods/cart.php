<?php

namespace Breakdance\WooCommerce;

// Ajax - Cart Qty Input
add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_cart_update_qty',
        '\Breakdance\WooCommerce\ajaxQtyCart',
        'none',
        true,
        [],
        true
    );
});

/**
 * @return void
 */
function ajaxQtyCart()
{
    // Set item key as the hash found in input.qty's name
    $itemKey = filter_input(INPUT_POST, 'hash');
    $newQty = filter_input(INPUT_POST, 'quantity');

    // Get the array of values owned by the product we're updating
    $item = WC()->cart->get_cart_item($itemKey);

    // Get the quantity of the item in the cart
    $qty = apply_filters('woocommerce_stock_amount_cart_item', wc_stock_amount(preg_replace('/[^0-9\.]/', '', $newQty)), $itemKey);

    // Validation check
    $passes = apply_filters('woocommerce_update_cart_validation', true, $itemKey, $item, $newQty);

    // Update the quantity of the item in the cart
    if ($passes) {
        WC()->cart->set_quantity( $itemKey, $qty, true );
    }
}

function getActionsForCart($crossSell)
{
    $remove = [];

    // Remove cross sell from cart collaterals
    if ($crossSell['disable']) {
        $remove[] = ['woocommerce_after_cart', 'woocommerce_cross_sell_display'];
    }

    $catalogActions = getActionsForCatalog($crossSell['elements'] ?? []);

    return array_merge_recursive([
        'remove' => $remove
    ], $catalogActions);
}

/*
 * Template Hooks
 */

// Remove variations from product title
add_filter('woocommerce_product_variation_title_include_attributes', '__return_false');

// Move cross sell below cart
remove_action('woocommerce_cart_collaterals', 'woocommerce_cross_sell_display');
add_action('woocommerce_after_cart', 'woocommerce_cross_sell_display');
