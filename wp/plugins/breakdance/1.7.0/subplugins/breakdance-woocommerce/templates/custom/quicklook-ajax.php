<?php global $product; ?>
<div id="product-<?php echo $product->get_id(); ?>" <?php post_class( 'product' ); ?>>
    <?php do_action( 'breakdance_quicklook_image' ); ?>

    <div class="summary entry-summary">
        <?php do_action( 'breakdance_quicklook_summary' ); ?>
    </div>
</div>
