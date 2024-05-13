<?php

namespace Breakdance\Util\WP;

use function Breakdance\Themeless\Rules\isSearch;

/**
 * @return bool
 */
function is_home_when_home_is_the_posts_archive()
{
    return is_home() && !is_front_page();
}

/**
 * @return bool
 */
function isAnyArchive(){
    return is_archive() || is_post_type_archive() || is_home() || isSearch();
}

