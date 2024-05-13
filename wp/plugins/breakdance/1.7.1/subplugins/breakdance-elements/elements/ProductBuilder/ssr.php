<?php
/**
 * @var array $propertiesData
 */

$content = $propertiesData['content'] ?? [];
$productId = $content['content']['product'] ?? null;

\Breakdance\WooCommerce\renderProductPart($productId, function ($product) use ($content) {
    do_action("woocommerce_before_single_product");

    if (post_password_required()) {
        echo get_the_password_form();
        return;
    }
    $disableWooId = $content['content']['disable_woo_id'] ?? false;

    ?>
    <div <?php echo $disableWooId ? '' : 'id="product-'. get_the_ID() .'"'; ?> <?php wc_product_class( '', $product ); ?>>
        %%CHILDREN%%
    </div>
    <?php

    do_action("woocommerce_after_single_product");
});
