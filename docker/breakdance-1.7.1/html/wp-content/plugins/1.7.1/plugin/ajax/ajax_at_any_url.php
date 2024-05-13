<?php

namespace Breakdance\AJAX;

/*

fired by template_include

enable AJAX actions to be fired from any URL
as long as the breakdance_ajax_at_any_url parameter is set.
to end an ajax action fired this way, wp_die doesn't work... only die does (why?)
 */

use function Breakdance\Util\is_post_request;

/**
 * Don't use add_action('breakdance_ajax' directly)... use \Breakdance\AJAX\register_handler instead
 * to register an a handler and get automatic security, permissions, etc.
 * @return boolean
 */
function see_if_this_is_an_ajax_at_any_url_request_and_if_so_fire_it()
{
    /** @var string|null|false $ajax_action */
    $ajax_action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_SPECIAL_CHARS);

    if (is_post_request() && isset($_POST['breakdance_ajax_at_any_url']) && $ajax_action) {
        $custom_action = "breakdance_ajax_{$ajax_action}";
        do_action($custom_action);
        return true;
    }

    return false;
}
