<?php

use function Breakdance\Filesystem\check_all_required_directories;

add_action('breakdance_loaded', function () {
    add_action('init', function () {
        if (is_admin()) {
            $required_directories_status = check_all_required_directories();
            $not_ok_directories = array_filter($required_directories_status, function ($item) {
                return $item !== null;
            });

            if (sizeof($not_ok_directories) > 0) {
                add_action('admin_notices', 'Breakdance\Filesystem\HelperFunctions\show_unavailable_directories_admin_notice');
            }
        }
    });
});
