<?php

namespace Breakdance\WooCommerce;

/**
 * @param $fields
 * @return array
 */
function reorderCheckoutFields($fields)
{
    $reorder = apply_filters('breakdance_reorder_checkout_fields', true);

    if (!$reorder) return $fields;

    $fields['billing_email']['priority'] = 5; // Before everything

    if (isset($fields['billing_country'])) {
        $fields['billing_country']['priority'] = 75; // After city
    }

    return $fields;
}

add_filter('woocommerce_billing_fields', '\Breakdance\WooCommerce\reorderCheckoutFields');


/**
 * Wrap checkout order review with a div.
 */
function beforeOrderReview() {
    echo '<div class="bde-order-review-column">';
}

/**
 * Close the div wrapper.
 */
function afterOrderReview() {
    echo '</div>';
}

add_action( 'woocommerce_checkout_before_order_review_heading', '\Breakdance\WooCommerce\beforeOrderReview' );
add_action( 'woocommerce_checkout_after_order_review', '\Breakdance\WooCommerce\afterOrderReview' );
