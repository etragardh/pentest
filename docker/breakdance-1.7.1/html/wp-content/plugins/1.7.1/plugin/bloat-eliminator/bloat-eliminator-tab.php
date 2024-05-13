<?php

namespace Breakdance\BloatEliminator;

use function Breakdance\Data\get_global_option;
use function Breakdance\Data\set_global_option;
use function Breakdance\Util\is_post_request;

add_action('breakdance_register_admin_settings_page_register_tabs', 'Breakdance\BloatEliminator\register');

function register()
{
    \Breakdance\Admin\SettingsPage\addTab(
        'Performance',
        'bloat_eliminator',
        'Breakdance\BloatEliminator\tab',
        1050
    );
}

function saveBloatEliminatorSettings()
{
    $options = [];
    $whitelist = [
        'gutenberg-blocks-css',
        'rsd-links',
        'wlw-link',
        'rest-api',
        'shortlink',
        'rel-links',
        'wp-generator',
        'feed-links',
        'xml-rpc',
        'wp-emoji',
        'wp-oembed',
        'wp-dashicons',
    ];

    foreach ($whitelist as $feature) {
        if (array_key_exists($feature, $_POST)) {
            $options[] = $feature;
        }
    }

    set_global_option('breakdance_settings_bloat_eliminator', $options);
}

function tab()
{
    $nonce_action = 'breakdance_admin_bloat-eliminator_tab';
    if (is_post_request() && check_admin_referer($nonce_action)) {
        saveBloatEliminatorSettings();
    }

    $bloatOptions = (array) get_global_option('breakdance_settings_bloat_eliminator');
    $bloatOptions = $bloatOptions ? $bloatOptions : [];

    require_once __DIR__ . '/bloat-eliminator-template.php';
}
