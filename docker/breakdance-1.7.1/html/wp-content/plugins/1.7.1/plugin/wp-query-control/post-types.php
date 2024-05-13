<?php

namespace Breakdance\WpQueryControl;

use function Breakdance\Util\get_public_post_types_excluding_templates;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_post_types_for_wp_query',
        'Breakdance\WpQueryControl\getBreakdancePostTypes',
        'edit'
    );
});

/**
 * @return array{postTypes: list<array{name: mixed, slug: string}>}
 */
function getBreakdancePostTypes()
{
    $postTypes = get_public_post_types_excluding_templates();
    $postTypesForBuilder = [];

    foreach ($postTypes as $postType) {
        $postTypeObj = get_post_type_object($postType);

        if (!$postTypeObj) {
            continue;
        }

        $postTypesForBuilder[] = [
            'name' => $postTypeObj->labels->name,
            'slug' => $postType
        ];
    }

    return ['postTypes' => $postTypesForBuilder];
}
