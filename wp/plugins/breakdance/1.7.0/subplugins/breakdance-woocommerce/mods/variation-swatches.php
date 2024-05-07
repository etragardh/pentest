<?php

add_action('wp_enqueue_scripts', function () {
    wp_dequeue_style('woo-variation-swatches'); // woo-variation-swatches
    wp_dequeue_style('cfvsw_swatches_product'); // variation-swatches-woo
}, 100);

// variation-swatches-woo
add_filter('woocommerce_dropdown_variation_attribute_options_html', function ($select_html, $args) {
    // Remove the style= attributes from all variations.
    return preg_replace('/style=([^"]*);>/', '>', $select_html);
}, 9999, 2);
