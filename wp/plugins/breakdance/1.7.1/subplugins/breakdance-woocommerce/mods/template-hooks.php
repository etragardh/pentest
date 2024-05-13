<?php

namespace Breakdance\WooCommerce;

function showProductCategories()
{
    wc_get_template_part('custom/categories');
}

function wrapThumbnailInADiv()
{
    echo '<div class="bde-woo-product-image">';
        do_action( 'breakdance_before_shop_loop_item_image' );
        echo woocommerce_get_product_thumbnail();
        do_action( 'breakdance_before_shop_loop_after_image' );
    echo '</div>';
}

function renderCustomArea($blockId, $index) {
    $postId = get_the_ID() ?: null;
    echo '<div class="custom-area custom-area-' . $index . '">';
    echo \Breakdance\Render\render($blockId, $postId);
    echo '</div>';
}

/*
 * Products List
 */

// Product Image

// Wrap product image in a div
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail');
add_action('woocommerce_before_shop_loop_item_title', '\Breakdance\WooCommerce\wrapThumbnailInADiv', 10);

// Sale Badge

// Remove sale badge from product title
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash');
// Then add it back inside the image div
add_action('breakdance_before_shop_loop_item_image', 'woocommerce_show_product_loop_sale_flash', 10);
