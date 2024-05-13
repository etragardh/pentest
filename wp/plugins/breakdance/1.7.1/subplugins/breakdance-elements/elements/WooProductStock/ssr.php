<?php
/**
 * @var array $propertiesData
 */

global $product;
$product = wc_get_product();

if ($product) {
    echo wc_get_stock_html($product);
}
