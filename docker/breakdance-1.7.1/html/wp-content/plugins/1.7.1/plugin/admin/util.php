<?php

namespace Breakdance\Admin;

/**
 * @param string|int $post_id
 * @return string
 */
function get_builder_loader_url($post_id)
{
    $url = home_url();

    $query_args = [
        'breakdance' => 'builder',
        'id' => $post_id,
    ];

    return add_query_arg($query_args, $url);
}

/**
 * @param string|null $page_to_open_url
 * @param string|null $return_back_to_url
 * @return string
 */
function get_browse_mode_url($page_to_open_url = null, $return_back_to_url = null)
{
    $url = home_url();

    $query_args = [
        'breakdance' => 'builder',
        'mode' => 'browse',
    ];

    if ($page_to_open_url) {
        $query_args['browseModeOpenUrl'] = urlencode($page_to_open_url);
    }

    if ($return_back_to_url) {
        $query_args['returnUrl'] = urlencode($return_back_to_url);
    }

    return add_query_arg($query_args, $url);
}

/**
 * @return string
 */
function get_current_page_url()
{
    /**
     * @psalm-suppress PossiblyInvalidArgument
     * @psalm-suppress MixedArgument
     */
    return home_url(remove_query_arg(wp_removable_query_args(), wp_unslash($_SERVER['REQUEST_URI'] ?? '')));
}

/**
 * @param string|null $page_to_open_url
 * @return string
 */
function get_browse_mode_url_with_return_back_to_current_page($page_to_open_url = null)
{
    return get_browse_mode_url($page_to_open_url, get_current_page_url());
}

/**
 * @return bool
 */
function is_breakdance_development_environment()
{
    return defined('BREAKDANCE_DEVELOPMENT_ENVIRONMENT') && BREAKDANCE_DEVELOPMENT_ENVIRONMENT === true;
}

/**
 * @return string
 */
function get_env()
{
    return is_breakdance_development_environment() ? 'local' : 'production';
}

/**
 * @return bool
 */
function current_post_is_breakdance_because_its_post_type_is_prefixed_with_breakdance_()
{
    global $post;

    /**
     * @psalm-suppress MixedPropertyFetch
     */
    return $post ?
        strpos((string) $post->post_type, 'breakdance') !== false :
        false;
}
