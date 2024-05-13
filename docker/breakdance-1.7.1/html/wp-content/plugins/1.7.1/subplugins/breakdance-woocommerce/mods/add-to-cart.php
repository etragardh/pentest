<?php

namespace Breakdance\WooCommerce;

function addToCart()
{
    add_action("wp_head", "\Breakdance\WooCommerce\addInlineScript");
}
add_action('woocommerce_add_to_cart', '\Breakdance\WooCommerce\addToCart');

// Automatically open menu cart dropdown for non-ajax add-to-cart requests.
function addInlineScript()
{
    ?>
    <script>
      let BREAKDANCE_ADDED_TO_CART = true;
    </script>
    <?php
}
