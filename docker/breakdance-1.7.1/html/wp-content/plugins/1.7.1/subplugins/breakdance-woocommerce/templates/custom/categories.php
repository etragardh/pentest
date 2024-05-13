<?php
global $product;

$terms = get_the_terms($product->get_id(), 'product_cat');

if (!$terms) return;

$names = array_map(function ($term) {
    return $term->name;
}, $terms);

?>
<p class="bde-woo-categories-list"><?php echo join( ', ', $names ); ?></p>
