<?php

namespace Breakdance\WooCommerce;

use function Breakdance\WooCommerce\Settings\isWooIntegrationEnabled;

require_once __DIR__ . "/settings/base.php";
require_once __DIR__ . "/utils.php";
require_once __DIR__ . "/register-support.php";

add_action(
    'plugins_loaded',
    function() {
        if (isWooIntegrationEnabled()) {
            require_once __DIR__ . "/widgets/woo_filters.php";
            require_once __DIR__ . "/widgets/woo_general.php";
        }
    }
);
