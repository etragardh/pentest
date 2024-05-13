<?php
/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;
use function Breakdance\WooCommerce\getActionsForCart;

if (is_cart()) {
    $crossSell = $propertiesData['design']['cross_sells'] ?? ['disable' => false];
    $actions = getActionsForCart($crossSell);

    WooActions::remove($actions['remove'])
        ->add($actions['add'])
        ->then(function () {
            echo do_shortcode('[woocommerce_cart no-hijack]');
        });
} else {
    \Breakdance\WooCommerce\getErrorMessageForWooElementPageInWrongPage(
        'cart',
        \EssentialElements\Woopageshoppingcart::name(),
        "Cart"
    );
}
