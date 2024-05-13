<?php
/**
 * @var array $propertiesData
 */

if (is_account_page()) {
    echo do_shortcode('[woocommerce_my_account no-hijack]');
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'myaccount',
        \EssentialElements\Woopageaccount::name(),
        "Account"
    );
}
