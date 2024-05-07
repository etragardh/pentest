<?php

namespace Breakdance\Util;

use Breakdance\Themeless\SearchContext;

use function Breakdance\Themeless\filterBySearchOrReturnOriginal;

/**
 * @return string[]
 */
function get_public_and_allowed_post_types()
{
    /**
     * @var string[]
     */
    $postTypes = array_values(
        get_post_types([
            'public' => true,
        ])
    );

    /** @var string[] $allEditablePostTypes */
    $allEditablePostTypes = BREAKDANCE_ALL_EDITABLE_POST_TYPES;
    /** @var string[] $bannedPostTypes */
    $bannedPostTypes = BREAKDANCE_BANNED_POST_TYPES;

    return array_filter(
        $postTypes,
        function ($postType) use($allEditablePostTypes, $bannedPostTypes){
            return !in_array($postType, $allEditablePostTypes)
                && !in_array($postType, $bannedPostTypes);
        }
    );
}

/**
 * @return string[]
 */
function get_public_post_types_excluding_templates()
{
    /**
     * @var string[]
     */
    $postTypes = array_values(
        get_post_types([
            'public' => true,
        ])
    );

    /**
     * @var string[]
     */
    $editablePostTypes = BREAKDANCE_ALL_EDITABLE_POST_TYPES;

    return array_filter(
        $postTypes,
        function ($postType) use ($editablePostTypes) {
            return !in_array($postType, $editablePostTypes);
        }
    );
}

/**
 * @param string|false $searchTerm
 * @return  string[]
 */
function get_post_types_with_archives($searchTerm = false)
{
    $post_type_slugs = \Breakdance\Util\get_public_post_types_excluding_templates();
    $post_types_with_archive = [];

    foreach ($post_type_slugs as $post_type_slug) {
        $archive_link = get_post_type_archive_link($post_type_slug);

        // Add only post types with archive pages. There can be post-types without.
        if ($archive_link) {
            $post_types_with_archive[] = $post_type_slug;
        }
    }

    return filterBySearchOrReturnOriginal($post_types_with_archive, $searchTerm);
}
