<?php

if (is_checkout()) {
    \Breakdance\WooCommerce\CheckoutBuilder\billing();
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'checkout',
        \EssentialElements\WooCheckoutBillingForm::name(),
        "Checkout"
    );
}
