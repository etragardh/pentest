<?php

/**
 * @var array $propertiesData
 */

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('wc-cart-fragments');
});

$hidden = apply_filters('woocommerce_widget_cart_is_hidden', false);

if (WC()->cart === null) {
    return;
}

if ($hidden) {
    return;
}

$content = $propertiesData['content']['content'] ?? [];
$design = $propertiesData['design'] ?? [];

$style = $design['cart']['style'] ?? 'dropdown';
$position = $design['cart'][$style . '_position'] ?? 'left';

$primaryButton = $content['cart']['primary_button'] ?? 'cart';

// Continue Link
$continueLinkType = $content['cart']['continue_shopping_link'] ?? null;
$continueEnabled = $continueLinkType && $continueLinkType !== 'disabled';

$continueLinks = [
    'homepage' => get_home_url(),
    'shop' => get_permalink(wc_get_page_id('shop')),
    'custom' => $content['cart']['url'] ?? '',
];

$continueShoppingLink = $continueLinks[$continueLinkType] ?? null;
$continueShoppingText = __('Continue Shopping', 'woocommerce');

$cartText = __('Cart', 'woocommerce');

$headerBlockId = $content['after_title_bar'] ?? null;
$footerBlockId = $content['after_footer'] ?? null;

$subtotal = WC()->cart->get_cart_subtotal();
$count = WC()->cart->get_cart_contents_count();

// Link attributes
$attributes = $content['link']['attributes'] ?? [];
$formattedAttributes = implode(
    ' ',
    array_map(
        fn ($attr) => sprintf('%s="%s"', $attr['name'], htmlspecialchars($attr['value'])),
        $attributes
    )
);
?>

<a class="bde-mini-cart-toggle" href="<?php echo esc_url(wc_get_cart_url()); ?>" <?php echo $formattedAttributes; ?>>
    <span class="bde-mini-cart-toggle__subtotal" data-count='<?php echo $count; ?>'><?php echo $subtotal; ?></span>
    <span class="bde-mini-cart-toggle__icon"></span>
    <span class="bde-mini-cart-toggle__counter" data-count='<?php echo $count; ?>'><?php echo $count; ?></span>
</a>

<div class="bde-mini-cart-offcanvas bde-mini-cart-offcanvas--<?php echo $style; ?> bde-mini-cart-offcanvas--primary-<?php echo $primaryButton; ?> bde-mini-cart-offcanvas--<?php echo $position; ?>">
    <div class="bde-mini-cart-offcanvas-body">
        <div class="bde-mini-cart-offcanvas-topbar">
            <h6 class="bde-mini-cart-offcanvas-title"><?php echo $cartText; ?></h6>
            <button class="bde-mini-cart-offcanvas__close-button">×</button>
        </div>

        <?php
        if ($headerBlockId) {
            echo \Breakdance\Render\render($headerBlockId);
        }
        ?>

        <div class="widget_shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
        </div>

        <?php if ($continueEnabled && $continueShoppingLink) { ?>
            <a class="bde-mini-cart-continue-link" href="<?php echo $continueShoppingLink; ?>">
                <?php echo $continueShoppingText; ?>
            </a>
        <?php } ?>

        <?php
        if ($footerBlockId) {
            echo \Breakdance\Render\render($footerBlockId);
        }
        ?>
    </div>

    <span class="bde-mini-cart-offcanvas-overlay"></span>
</div>