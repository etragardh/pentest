<?php
/**
 * @var array $propertiesData
 */

$hideTitle = $propertiesData['design']['totals']['hide_title'] ?? false;

if ($hideTitle) {
    add_filter('breakdance_cart_totals_show_title', '__return_false');
}

\Breakdance\WooCommerce\CartBuilder\totals();
