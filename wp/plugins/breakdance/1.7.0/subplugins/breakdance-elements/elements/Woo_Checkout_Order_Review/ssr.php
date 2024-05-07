<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\orderReview();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutOrderReview::name(),
        "Checkout"
    );
}
