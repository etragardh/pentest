<?php
namespace Breakdance\AjaxEndpoints;

use function Breakdance\Util\WP\get_all_taxonomies;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_popups',
        'Breakdance\AjaxEndpoints\getPopups',
        'edit',
        true
    );
});

/**
 * @return array{value: string, text: string}[]
 */
function getPopups()
{
    $popups = get_posts(
        [
            'post_type' => 'breakdance_popup',
            'numberposts' => -1,
            'orderby'     => 'modified',
            'order'       => 'DESC',
        ]
    );

    return array_map(function ($popup) {
        if (is_object($popup)) {
            return [
                'value' => (string) $popup->ID,
                'text' => $popup->post_title,
            ];
        }
    }, $popups);
}
