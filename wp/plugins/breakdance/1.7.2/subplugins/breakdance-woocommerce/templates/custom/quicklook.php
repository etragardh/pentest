<?php
    global $product;
    $url = \Breakdance\WooCommerce\getQuicklookRedirectUrl($product);
?>
<div class="bde-woo-product-quicklook">
    <button class="bde-woo-product-quicklook-button button" data-id="<?php echo $product->get_id(); ?>" data-redirect-url="<?php echo $url; ?>">
        <?php echo \Breakdance\WooCommerce\getQuicklookLabel(); ?>
    </button>
</div>
