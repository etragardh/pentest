<?php

namespace Breakdance\Themeless\Rules;

add_action('breakdance_register_template_types_and_conditions', '\Breakdance\Themeless\Rules\registerWoocommerceConditions');

function registerWoocommerceConditions() {
    if (!class_exists('woocommerce', false)) {
        return;
    }
    require_once __DIR__ . "/stock-status.php";
    require_once __DIR__ . "/stock-quantity.php";
    require_once __DIR__ . "/product-virtual.php";
    require_once __DIR__ . "/product-downloadable.php";
    require_once __DIR__ . "/product-cart.php";
    require_once __DIR__ . "/product-attributes.php";
    require_once __DIR__ . "/product-measurements.php";
    require_once __DIR__ . "/product-price.php";
    require_once __DIR__ . "/product-sale.php";
    require_once __DIR__ . "/product-tax.php";
    require_once __DIR__ . "/product-shipping.php";
    require_once __DIR__ . "/product-tags.php";
    require_once __DIR__ . "/product-categories.php";
    require_once __DIR__ . "/cart-quantity.php";
    require_once __DIR__ . "/cart-value.php";
    require_once __DIR__ . "/cart-weight.php";
    require_once __DIR__ . "/customer-orders.php";
}

/**
 * @return string[]
 */
function getProductConditionPostTypes() {
    return [
        'product',
        BREAKDANCE_HEADER_POST_TYPE,
        BREAKDANCE_BLOCK_POST_TYPE,
        BREAKDANCE_FOOTER_POST_TYPE,
        BREAKDANCE_POPUP_POST_TYPE,
        BREAKDANCE_ACF_BLOCK_POST_TYPE,
    ];
}
