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
    ->then(function() use ($content, $order, $orderBy, $limit) {
        $args = [
            'posts_per_page' => $limit,
            'limit' => $limit,
            'orderby' => $orderBy,
            'order' => $order
        ];

        woocommerce_related_products(
            apply_filters(
                'woocommerce_output_related_products_args',
                $args
            )
        );
        wp_reset_postdata();
    });









