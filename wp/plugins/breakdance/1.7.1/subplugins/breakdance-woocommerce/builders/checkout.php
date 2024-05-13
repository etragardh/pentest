<?php

namespace Breakdance\WooCommerce\CheckoutBuilder;

// Copied from class-wc-shortcode-checkout.php

function checkout()
{
    remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form' );
    remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_login_form' );

    // Show non-cart errors.
    do_action( 'woocommerce_before_checkout_form_cart_notices' );

    // Check cart has contents.
    if ( WC()->cart->is_empty() && ! is_customize_preview() && apply_filters( 'woocommerce_checkout_redirect_empty_cart', true ) ) {
        return;
    }

    // Check cart contents for errors.
    do_action( 'woocommerce_check_cart_items' );

    // Calc totals.
    WC()->cart->calculate_totals();

    // Get checkout object.
    $checkout = WC()->checkout();

    if ( empty( $_POST ) && wc_notice_count( 'error' ) > 0 ) { // WPCS: input var ok, CSRF ok.

        wc_get_template( 'checkout/cart-errors.php', array( 'checkout' => $checkout ) );
        wc_clear_notices();

    } else {

        $non_js_checkout = ! empty( $_POST['woocommerce_checkout_update_totals'] ); // WPCS: input var ok, CSRF ok.

        if ( wc_notice_count( 'error' ) === 0 && $non_js_checkout ) {
            wc_add_notice( __( 'The order totals have been updated. Please confirm your order by pressing the "Place order" button at the bottom of the page.', 'woocommerce' ) );
        }

        do_action( 'woocommerce_before_checkout_form', $checkout );
        ?>
        <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
            %%CHILDREN%%
        </form>
        <?php
        do_action('woocommerce_after_checkout_form', $checkout);
    }
}

function shipping()
{
    $checkout = WC()->checkout();
    ?>
    <div class="col-2">
        <?php do_action( 'woocommerce_checkout_shipping' ); ?>
    </div>
    <?php
}

function billing()
{
    add_filter('breakdance_checkout_billing_show_title', '__return_false');

    $checkout = WC()->checkout();
    do_action('woocommerce_checkout_before_customer_details');

    if ($checkout->get_checkout_fields()) {
        do_action('woocommerce_checkout_billing');
    }

    do_action('woocommerce_checkout_after_customer_details');
}

function orderReview()
{
    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );

    do_action('woocommerce_checkout_before_order_review_heading');
    do_action( 'woocommerce_checkout_before_order_review' );
    ?>

    <div id="order_review" class="woocommerce-checkout-review-order">
        <?php do_action( 'woocommerce_checkout_order_review' ); ?>
    </div>

    <?php do_action( 'woocommerce_checkout_after_order_review' );
}

function payment()
{
    woocommerce_checkout_payment();
}

function login()
{
    woocommerce_checkout_login_form();
}

function coupon()
{
    woocommerce_checkout_coupon_form();
}

function thankYou()
{
    global $wp;
    $order_id = $wp->query_vars['order-received'];
    $order = false;

    // Get the order.
    $order_id  = apply_filters( 'woocommerce_thankyou_order_id', absint( $order_id ) );
    $order_key = apply_filters( 'woocommerce_thankyou_order_key', empty( $_GET['key'] ) ? '' : wc_clean( wp_unslash( $_GET['key'] ) ) ); // WPCS: input var ok, CSRF ok.

    if ( $order_id > 0 ) {
        $order = wc_get_order( $order_id );
        if ( ! $order || ! hash_equals( $order->get_order_key(), $order_key ) ) {
            $order = false;
        }
    }

    // Empty awaiting payment session.
    unset( WC()->session->order_awaiting_payment );

    // In case order is created from admin, but paid by the actual customer, store the ip address of the payer
    // when they visit the payment confirmation page.
    if ( $order && $order->is_created_via( 'admin' ) ) {
        $order->set_customer_ip_address( WC_Geolocation::get_ip_address() );
        $order->save();
    }

    // Empty current cart.
    wc_empty_cart();

    wc_get_template( 'checkout/thankyou.php', array( 'order' => $order ) );
}
