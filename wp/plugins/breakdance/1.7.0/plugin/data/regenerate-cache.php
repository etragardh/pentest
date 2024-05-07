<?php

namespace Breakdance\Data;

use function Breakdance\Util\WP\performant_get_posts;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_regenerate_post_cache',
        '\Breakdance\Data\regenerate_post_cache',
        'edit',
        true
    );
});

function regenerate_post_cache()
{
    $id = intval($_POST['id'] ?? null);
    \Breakdance\Render\generateCacheForPost($id);
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_regenerate_global_settings_cache',
        '\Breakdance\Data\regenerate_global_settings_cache',
        'edit',
        true
    );
});

function regenerate_global_settings_cache()
{
    \Breakdance\Render\generateCacheForGlobalSettings();
}

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_paginated_breakdance_post_ids',
        '\Breakdance\Data\get_paginated_breakdance_post_ids',
        'edit',
        true,
        [
            'args' => [
                'page' => FILTER_VALIDATE_INT
            ]
        ]
    );
});

/**
 * @param int $currentPage
 * @return array{postIds: array<array-key, \WP_Post>}
 */
function get_paginated_breakdance_post_ids($currentPage)
{

    /** @var string[] $BREAKDANCE_ALL_EDITABLE_POST_TYPES */
    $BREAKDANCE_ALL_EDITABLE_POST_TYPES = BREAKDANCE_ALL_EDITABLE_POST_TYPES;

    $postTypesWithoutBreakdancePostTypes = \Breakdance\Settings\get_allowed_post_types(false);
    $postTypes = array_merge($postTypesWithoutBreakdancePostTypes, $BREAKDANCE_ALL_EDITABLE_POST_TYPES);

    $postsPerPage = 50;

    $allBreakdancePostIds = performant_get_posts(
            [
                'post_type' => $postTypes,
                'fields' => 'ids',
                'posts_per_page' => $postsPerPage,
                'offset' => $postsPerPage * $currentPage,
                'meta_query' => [
                    [
                        'key' => 'breakdance_data',
                        'compare' => 'EXISTS'
                    ],
                ],
            ],
    );

    return ['postIds' => $allBreakdancePostIds];
}
