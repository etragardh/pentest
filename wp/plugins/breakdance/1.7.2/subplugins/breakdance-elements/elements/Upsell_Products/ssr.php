<?php
/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;

$content = $propertiesData['content']['content'];
$productId = $content['product'] ?? null;

global $post;
$post = get_post($productId);
setup_postdata($post);

$limit = $content['product_count'] ?? 4;
$orderBy = $content['order_by'] ?? 'rand';
$order = $content['order'] ?? 'desc';

WooActions::filterCatalog($propertiesData['design']['elements'] ?? [])
    ->then(function() use ($order, $orderBy, $limit) {
        woocommerce_upsell_display($limit, 4, $orderBy, $order);
        wp_reset_postdata();
    });


