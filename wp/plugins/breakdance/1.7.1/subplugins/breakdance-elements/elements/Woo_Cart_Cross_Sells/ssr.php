<?php

/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;

$elements = $propertiesData['design']['cross_sells']['elements'] ?? [];

WooActions::filterCatalog($elements)
    ->then(function() {
        \Breakdance\WooCommerce\CartBuilder\crossSell();
    });
