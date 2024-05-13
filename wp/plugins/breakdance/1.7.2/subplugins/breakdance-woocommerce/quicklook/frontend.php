<?php

namespace Breakdance\WooCommerce\Quicklook;

function enqueueScripts()
{
    // Required in order to make the gallery and variations work.
    wp_enqueue_script('wc-add-to-cart-variation');
    wp_enqueue_script('wc-single-product');

    if (current_theme_supports('wc-product-gallery-zoom')) {
        wp_enqueue_script('zoom');
    }

    if (current_theme_supports('wc-product-gallery-lightbox')) {
        wp_enqueue_script('photoswipe-ui-default');
        wp_enqueue_style('photoswipe-default-skin');

        if (!has_action('wp_footer', 'woocommerce_photoswipe')) {
            // Prevent duplicate photoswipe template
            add_action('wp_footer', 'woocommerce_photoswipe', 20);
        }
    }

    wp_enqueue_script('breakdance-quicklook', BREAKDANCE_WOO_URL . 'js/quicklook.js', [], null, true);
}
add_action('wp_enqueue_scripts', '\Breakdance\WooCommerce\Quicklook\enqueueScripts');

function modalTemplate()
{
    wc_get_template_part('custom/quicklook-modal');
}

add_action('wp_footer', '\Breakdance\WooCommerce\Quicklook\modalTemplate');

/**
 * Add the "quicklook" button to the shop loop item.
 */
function quicklookButton()
{
    wc_get_template_part('custom/quicklook');
}
add_action('breakdance_before_shop_loop_after_image', '\Breakdance\WooCommerce\Quicklook\quicklookButton');
