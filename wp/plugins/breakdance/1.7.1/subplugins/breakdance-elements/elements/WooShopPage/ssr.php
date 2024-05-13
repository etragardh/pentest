<?php
/**
 * @var array $propertiesData
 */

use Breakdance\WooCommerce\WooActions;

if (is_shop() || is_product_taxonomy()) {
    WooActions::filterCatalog($propertiesData['design']['products_list']['elements'] ?? [])
        ->then(function () {
            /* Copied from WooCo archive-product.php template */
            if (woocommerce_product_loop()) {

                /**
                 * Hook: woocommerce_before_shop_loop.
                 *
                 * @hooked woocommerce_output_all_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();

                if (wc_get_loop_prop('total')) {
                    while (have_posts()) {
                        the_post();

                        /**
                         * Hook: woocommerce_shop_loop.
                         */
                        do_action('woocommerce_shop_loop');

                        wc_get_template_part('content', 'product');
                    }
                }

                woocommerce_product_loop_end();

                /**
                 * Hook: woocommerce_after_shop_loop.
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');
            } else {
                /**
                 * Hook: woocommerce_no_products_found.
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }
        });
} else {
    echo <<<HTML
     <div class="breakdance-empty-ssr-message breakdance-empty-ssr-message-error">
        <div>
            The <b>"Shop Page"</b> element can only be added to a shop archive template or a template for product taxonomies.<br /><br />
            Create one in the WP admin at <b>Breakdance &gt; Templates</b>.
        </div>
    </div>
HTML;

}