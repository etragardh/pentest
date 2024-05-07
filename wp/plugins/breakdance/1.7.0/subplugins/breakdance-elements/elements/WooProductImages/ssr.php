<?php
/**
 * @var array $propertiesData
 * @var array $parentPropertiesData
 */

$productId = $parentPropertiesData['content']['content']['product'] ?? null;

\Breakdance\WooCommerce\renderProductPart($productId, function () {
    woocommerce_show_product_sale_flash();
    woocommerce_show_product_images();
});
