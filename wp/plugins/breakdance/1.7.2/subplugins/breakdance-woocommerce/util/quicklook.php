<?php

namespace Breakdance\WooCommerce;

/**
 * @return array|mixed
 */
function getQuicklookSettings()
{
    $settings = \Breakdance\Data\get_global_settings_array();
    return $settings['settings']['woocommerce']['other']['quicklook'] ?? [];
}

/**
 * @return boolean
 */
function isQuicklookEnabled()
{
    return false;
    // return getQuicklookSettings()['enable'] ?? false;
}

/**
 * @return boolean
 */
function shouldShowQuicklookArrows()
{
    return getQuicklookSettings()['show_arrows'] ?? false;
}

/**
 * @return string
 */
function getQuicklookLabel()
{
    return getQuicklookSettings()['label'] ?? 'Quicklook';
}

/**
 * @param \WC_Product $product
 * @return string|null
 */
function getQuicklookRedirectUrl($product)
{
    $page = getQuicklookSettings()['redirect_on_add'] ?? 'product';

    $urls = [
        'product' => get_permalink($product->get_id()),
        'cart' => wc_get_cart_url(),
        'disabled'=> null
    ];

    return $urls[$page];
}
