<?php

namespace Breakdance\AjaxEndpoints;

use function Breakdance\Util\get_post_types_with_archives;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_post_types',
        'Breakdance\AjaxEndpoints\getPostTypes',
        'edit',
        true
    );
});

/**
 * @return array{value: string, text: string}[]
 */
function getPostTypes()
{
    $postTypeSlugs = get_post_types_with_archives();
    return array_map(function ($postTypeSlug) {

        return [
            'value' => $postTypeSlug,
            'text' => $postTypeSlug,
        ];
    }, $postTypeSlugs);
}
