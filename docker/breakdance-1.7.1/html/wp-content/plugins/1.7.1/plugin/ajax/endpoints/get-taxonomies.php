<?php
namespace Breakdance\AjaxEndpoints;

use function Breakdance\Util\WP\get_all_taxonomies;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_taxonomies',
        'Breakdance\AjaxEndpoints\getTaxonomies',
        'edit',
        true
    );
});

/**
 * @return array{value: string, text: string}[]
 */
function getTaxonomies()
{
    $taxonomies = get_all_taxonomies();
    return array_map(function ($taxonomy) {
        if (is_object($taxonomy)) {
            return [
                'value' => $taxonomy->name,
                'text' => $taxonomy->label,
            ];
        }

        return [
            'value' => $taxonomy,
            'text' => $taxonomy,
        ];
    }, $taxonomies);
}
