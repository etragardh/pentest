<?php

namespace Breakdance\Settings;

use function Breakdance\Util\get_public_and_allowed_post_types;

/**
 * @param boolean $includeBreakdancePostTypes
 * @return string[]
 */
function get_allowed_post_types($includeBreakdancePostTypes = true)
{

    /** @var string[]|false */
    $enabledPostTypes = \Breakdance\Data\get_global_option('breakdance_settings_enabled_post_types');

    /** @var string[] */
    $post_types = is_array($enabledPostTypes) ? $enabledPostTypes : get_public_and_allowed_post_types();
    /** @var string[] $allEditablePostTypes */
    $allEditablePostTypes = BREAKDANCE_ALL_EDITABLE_POST_TYPES;
    /** @var string[] $bannedPostTypes */
    $bannedPostTypes = BREAKDANCE_BANNED_POST_TYPES;

    if ($includeBreakdancePostTypes) {
        $post_types = array_merge($post_types, $allEditablePostTypes);
    }

    // fail-safe in case a post type was added to 'enabled_post_types' before we banned it (via a plugin or whatnot)
    /** @var string[] $allowedPostTypes */
    $allowedPostTypes = array_filter(
        $post_types,
        function ($postType) use ($bannedPostTypes){
            return !in_array($postType, $bannedPostTypes);
        }
    );

    return $allowedPostTypes;
}
