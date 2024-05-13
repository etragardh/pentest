<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\payment();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutPayment::name(),
        "Checkout"
    );
}


