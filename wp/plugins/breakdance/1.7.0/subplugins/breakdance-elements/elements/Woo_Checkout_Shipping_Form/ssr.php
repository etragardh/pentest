<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\shipping();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutShippingForm::name(),
        "Checkout"
    );
}
