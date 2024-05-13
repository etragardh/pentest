<?php

namespace Breakdance\WooCommerce;

function addClassToPrice($classes)
{
    global $product;

    if ($product->get_type() === 'variable') {
        return $classes;
    }

    $classes .= ' price--stackable';
    return $classes;
}
add_filter('woocommerce_product_price_class', '\Breakdance\WooCommerce\addClassToPrice');
