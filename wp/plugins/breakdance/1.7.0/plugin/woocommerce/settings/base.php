<?php

namespace Breakdance\WooCommerce\Settings;

const BREAKDANCE_WOOCO_STYLES_FIELD = 'breakdance_settings_woocommerce_styles';

require_once __DIR__ . "/settings-tab.php";

/**
 * @return boolean
 */
function isWooIntegrationEnabled() {
    if (!class_exists('woocommerce')) {
        return false;
    }

    // defaults to enabled
    return \Breakdance\Data\get_global_option(BREAKDANCE_WOOCO_STYLES_FIELD) !== 'disabled' ;
}
