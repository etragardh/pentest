<?php

namespace Breakdance\Admin\SettingsPage;

/*
this is the public API developers can use to add a tab to our settings page
hook breakdance_register_admin_settings_page_register_tabs, then call addTab
 */

/**
 * @param string $name
 * @param string $slug
 * @param callable $callback
 * @param int $order
 * @return void
 */
function addTab($name, $slug, $callback, $order)
{
    SettingsPageController::getInstance()->tabs[] = [
        'name' => $name,
        'slug' => $slug,
        'order' => $order
    ];

    add_action('breakdance_admin_settings_page_tabs_' . $slug . '_tab', $callback);
}

add_action('breakdance_loaded', function () {
    do_action('breakdance_register_admin_settings_page_register_tabs');
});
