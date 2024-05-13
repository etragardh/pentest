<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\login();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutLoginForm::name(),
        "Checkout"
    );
}

