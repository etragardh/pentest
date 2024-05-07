<?php

/**
 * Plugin Name: Breakdance WooCommerce CSS
 * Description: Temporary plugin for developing the CSS for Breakdance's WooCommerce integration
 * Author: Louis
 * Version: 0.1
 */

namespace Breakdance\WooCommerce;

use function Breakdance\isRequestFromBuilderIframe;
use function Breakdance\isRequestFromBuilderSsr;
use function Breakdance\WooCommerce\Settings\isWooIntegrationEnabled;

require_once __DIR__ . "/config.php";
require_once __DIR__ . "/util/base.php";

function initializePlugin()
{
    if (!isEnabled()) return;

    importFiles();
    enqueueCssFiles();
    disableWooStyles();
    declareWooSupport();
}

add_action('plugins_loaded', '\Breakdance\WooCommerce\initializePlugin');

function declareWooSupport()
{
    add_action('after_setup_theme', function () {
        foreach (BREAKDANCE_WOO_SUPPORTS as $feature) {
            add_theme_support($feature);
        }
    });

    add_action('wp_enqueue_scripts', '\Breakdance\WooCommerce\addBuilderSupport');
}

function importFiles()
{
    // Make sure these files are loaded after WC,
    // otherwise some overwrites won't work.
    require_once __DIR__ . "/mods/base.php";
    require_once __DIR__ . "/builders/cart.php";
    require_once __DIR__ . "/builders/checkout.php";

    if (isQuicklookEnabled()) {
        require_once __DIR__ . "/quicklook/base.php";
    }
}

function disableWooStyles()
{
    /**
     * Disable Woo Default CSS
     * Don't use "woocommerce_enqueue_styles" because that could disable other plugin's styles too
     *
     * These are registered in "class-wc-frontend-scripts/get_styles()"
     */
    add_filter( 'woocommerce_enqueue_styles', function ($enqueuedStyles){
        unset( $enqueuedStyles['woocommerce-general'] );
        unset( $enqueuedStyles['woocommerce-layout'] );
        unset( $enqueuedStyles['woocommerce-smallscreen'] );

        return $enqueuedStyles;
    } );

    add_filter('woocommerce_enqueue_styles', '__return_empty_array');

    // Disable Woo inline styles: https://github.com/woocommerce/woocommerce/issues/21674#issuecomment-459652584
    // Does this actually work lol?
    add_action('wp_print_styles', function () {
        wp_style_add_data('woocommerce-inline', 'after', '');
    });

    registerWpThemesAndDisableTheirWcStyles();
}

function enqueueCssFiles()
{
    $url = BREAKDANCE_WOO_URL . 'css/breakdance-woocommerce.css';

    \Breakdance\GlobalDefaultStylesheets\GlobalDefaultStylesheetsController::getInstance()->register($url);

    add_action('wp_enqueue_scripts', '\Breakdance\WooCommerce\addInlineStyles');
}


add_filter('woocommerce_checkout_redirect_empty_cart', function ($redirect) {
    if (isRequestFromBuilderIframe() || isRequestFromBuilderSsr()) {
        return false;
    }

    return $redirect;
});

add_filter('breakdance_checkout_force_show_login', function ($show) {
    return isRequestFromBuilderSsr() ? true : $show;
});

/**
 * Manually load some Woo scripts in the Builder.
 * @return void
 */
function addBuilderSupport()
{
    if (!\Breakdance\isRequestFromBuilderIframe()) return;
    wp_enqueue_script('wc-add-to-cart-variation');
    wp_enqueue_script('wc-price-slider');
    wp_enqueue_script('selectWoo');
    wp_enqueue_style('select2');
}

function addInlineStyles()
{
    // Copied from class-wc-frontend-scripts.php

    // Placeholder style.
    wp_register_style('breakdance-woo-inline', false);
    wp_enqueue_style('breakdance-woo-inline');

    $showAsterisk = wc_string_to_bool(get_option('woocommerce_checkout_highlight_required_fields', 'yes'));

    if (!$showAsterisk) {
        wp_add_inline_style( 'breakdance-woo-inline', '.woocommerce form .form-row .required { visibility: hidden; }' );
    }
}

function registerWpThemesAndDisableTheirWcStyles()
{
    /**
     * These don't offer WooCo styles support, but that means we support them so we register them
     */
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Ten',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Eleven',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Twelve',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Thirteen',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Fourteen',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Fifteen',
        fn() => false
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Sixteen',
        fn() => false
    );

    // Disable Woo Theme support styles
    // Included in each individual's theme class from "twenty seventeen" onwards
    // Included in "WooCommerce\theme_support_includes()"
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Seventeen',
        fn() => add_filter('woocommerce_twenty_seventeen_styles', '__return_false')
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Nineteen',
        fn() => add_filter('woocommerce_twenty_nineteen_styles', '__return_false')
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Twenty',
        fn() => add_filter('woocommerce_twenty_twenty_styles', '__return_false')
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Twenty-One',
        fn() => add_filter('woocommerce_twenty_twenty_one_styles', '__return_false')
    );
    registerThemeWooCommerceSupportAndDisableStyles(
        'Twenty Twenty-Two',
        fn() => add_filter('woocommerce_twenty_twenty_two_styles', '__return_false')
    );
}


/**
 * @return bool
 */
function isEnabled()
{
    if (!function_exists('Breakdance\WooCommerce\Settings\isWooIntegrationEnabled')) {
        return false;
    }

    return isWooIntegrationEnabled();
}
