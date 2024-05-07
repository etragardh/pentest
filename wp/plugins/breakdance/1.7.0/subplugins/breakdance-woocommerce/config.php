<?php

define('BREAKDANCE_WOO_URL', plugin_dir_url( __FILE__ ));
define('BREAKDANCE_WOO_DIR', plugin_dir_path( __FILE__ ));
define('BREAKDANCE_WOO_TEMPLATES_DIR', BREAKDANCE_WOO_DIR . 'templates' . DIRECTORY_SEPARATOR);
define('BREAKDANCE_WOO_SUPPORTS', [
    'woocommerce',
    'wc-product-gallery-zoom',
    'wc-product-gallery-slider',
    'wc-product-gallery-lightbox'
]);
// define('DEBUG_WOO_TEMPLATES', true);
// define('DEBUG_WOO_ACTIONS', true);
