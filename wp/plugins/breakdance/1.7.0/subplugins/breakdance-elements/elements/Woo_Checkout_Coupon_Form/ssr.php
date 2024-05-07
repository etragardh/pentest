<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\coupon();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutCouponForm::name(),
        "Checkout"
    );
}

