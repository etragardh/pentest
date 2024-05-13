<?php

namespace Breakdance\HeaderFooterTrackingCode;

use function Breakdance\Data\get_global_option;

add_action('wp_head', function () {

    $tracking_code_header = (string) get_global_option('breakdance_settings_tracking_code_header');
    echo $tracking_code_header;
}, 100000000);

add_action('wp_footer', function () {

    $tracking_code_footer = (string) get_global_option('breakdance_settings_tracking_code_footer');
    echo $tracking_code_footer;
}, 100000000);
