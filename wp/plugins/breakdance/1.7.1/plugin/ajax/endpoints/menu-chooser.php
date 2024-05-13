<?php

namespace Breakdance\AjaxEndpoints;

/**
 * @psalm-type MenuMeta = array{value: string, text: string}
 */

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_menus',
        'Breakdance\AjaxEndpoints\getMenus',
        'edit',
        true
    );
});

/**
 * @return MenuMeta[]
 */
function getMenus()
{
    $wpMenus = wp_get_nav_menus();
    $menus = array_map(function ($menu) {
        return [
            'value' => $menu->slug,
            'text' => $menu->name,
        ];
    }, $wpMenus);

    return $menus;
}
