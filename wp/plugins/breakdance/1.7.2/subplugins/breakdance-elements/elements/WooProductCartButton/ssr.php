<?php
/**
 * @var array $propertiesData
 * @var array $parentPropertiesData
 */

$productId = $parentPropertiesData['content']['content']['product'] ?? null;

\Breakdance\WooCommerce\renderProductPart($productId, function () {
    woocommerce_template_single_add_to_cart();
});
