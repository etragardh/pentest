<?php
/**
 * @var array $propertiesData
 * @var array $parentPropertiesData
 */

$productId = $parentPropertiesData['content']['content']['product'] ?? null;

\Breakdance\WooCommerce\renderProductPart($productId, function () {
    woocommerce_product_additional_information_tab();
});
