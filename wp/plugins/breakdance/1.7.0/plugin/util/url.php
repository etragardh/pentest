<?php

namespace Breakdance\Util;

/**
 * @param string $url
 * @return bool
 */
function validateUrl($url)
{
    return strtolower(esc_url_raw($url)) === strtolower($url);
}

/**
 * @param string $postType
 * @return string
 */
function get_menu_page_url($postType)
{
    // can't use menu_page_url because the menus don't registering for ajax request, so it'd return nothing
    return get_admin_url() . "admin.php?page=$postType";
}
