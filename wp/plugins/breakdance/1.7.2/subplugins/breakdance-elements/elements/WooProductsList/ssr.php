<?php

/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;
use function Breakdance\WpQueryControl\setupIsotopeFilterBar;

$args = $propertiesData['content']['content'];
$layout = $propertiesData['design']['layout']['layout'];
$attributes = ['wrapperClass' => $layout === 'slider' ? 'swiper' : null];
$products = \Breakdance\WooCommerce\getProducts($args);

// Filter Bar
$filterbar = setupIsotopeFilterBar([
    'settings' => $propertiesData['content']['filter_bar'] ?? [],
    'design' => $propertiesData['design']['filter_bar'] ?? [],
    'query' => $products,
    'defaultType' => 'product_cat',
    'defaultAll' => __('All Products', 'breakdance')
]);

WooActions::filterCatalog($propertiesData['design']['elements'] ?? [], $filterbar)
    ->then(function () use ($attributes, $products, $filterbar, $propertiesData, $layout) {
        \Breakdance\WpQueryControl\renderIsotoperFilterBar($filterbar);

        if ($layout === 'slider') {
            echo '<div class="breakdance-swiper-wrapper" data-swiper-id="%%ID%%">';
        }

        \Breakdance\WooCommerce\renderProducts($products, $attributes);

        if ($layout === 'slider') {
            \Breakdance\WpQueryControl\renderSwiperPagination($propertiesData['design']['layout']['slider']);
            echo "</div>"; // close .breakdance-swiper-wrapper
        }
    });
