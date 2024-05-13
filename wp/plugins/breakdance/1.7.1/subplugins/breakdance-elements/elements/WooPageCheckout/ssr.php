<?php
/**
 * @var array $propertiesData
 */

if (is_checkout()) {
    echo do_shortcode('[woocommerce_checkout no-hijack]');
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\Woopagecheckout::name(),
        "Checkout"
    );
}
