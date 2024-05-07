<?php
/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;

$content = $propertiesData['content']['content'] ?? [];
$productId = $content['product'] ?? null;

WooActions::filterProduct($content)->then(function () use ($productId) {
    \Breakdance\WooCommerce\renderProductPart($productId, function () {
        wc_get_template_part('content', 'single-product');
    });
});


