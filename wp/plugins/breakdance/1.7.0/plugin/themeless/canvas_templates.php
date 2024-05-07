<?php

namespace Breakdance\Themeless;

add_action('init', '\Breakdance\Themeless\setup_filter_for_canvas_templates');

function setup_filter_for_canvas_templates()
{
    // array of strings
    $allowed_post_types = \Breakdance\Settings\get_allowed_post_types();

    foreach ($allowed_post_types as $post_type) {
        add_filter("theme_{$post_type}_templates", '\Breakdance\Themeless\add_canvas_templates');
    }
}

/**
 * @param array{string,string} $templates
 * @return array{string,string}
 */
function add_canvas_templates($templates)
{

    return array_merge($templates, [
        'breakdance_blank_canvas' => '[Breakdance] No Header / Footer',
    ]);
}
