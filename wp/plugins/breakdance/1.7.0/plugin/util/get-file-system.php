<?php

/**
 * @return \WP_Filesystem_Base
 */
function get_filesystem()
{
    global $wp_filesystem;

    // If the filesystem has not been instantiated yet, do it here.
    if (!$wp_filesystem) {
        if (!function_exists('WP_Filesystem')) {
            /**
             * @psalm-suppress UnresolvableInclude
             * @psalm-suppress UndefinedConstant
             */
            require_once wp_normalize_path(ABSPATH
                . '/wp-admin/includes/file.php');
        }
        WP_Filesystem();
    }

    /**
     * @var \WP_Filesystem_Base
     */
    $wp_filesystem = $wp_filesystem;
    return $wp_filesystem;
}
