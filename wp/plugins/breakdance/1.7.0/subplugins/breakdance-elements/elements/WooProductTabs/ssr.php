<?php
/**
 * @var array $propertiesData
 */

global $post;
setup_postdata($post);

global $product;
$product = wc_get_product();

if ($product) {

    \Breakdance\WooCommerce\tabs($propertiesData);

}
