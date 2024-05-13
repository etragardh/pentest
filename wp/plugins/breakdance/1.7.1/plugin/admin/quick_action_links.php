<?php

namespace Breakdance\Admin;

add_action('breakdance_loaded', function () {
    add_action('init', function () {
        if (\Breakdance\Permissions\hasMinimumPermission('edit')) {
            add_filter('page_row_actions', '\Breakdance\Admin\breakdance_add_quick_action_links', 10, 2); /* page is not the post type here... it is something different. wordpress apis lol */
            add_filter('post_row_actions', '\Breakdance\Admin\breakdance_add_quick_action_links', 10, 2); /* post is not the post type here... it is something different. wordpress apis lol */
        }
    });
});

/**
 * @param string[] $actions An array of row action links.
 * @param \WP_Post $post    The post object.
 *
 * @return mixed
 */
function breakdance_add_quick_action_links($actions, $post)
{

    // TODO: memoize for performance
    // i cant believe wordpress APIs dont have a per-post-type action for these quick action links... is that for real
    // id add it myself and submit a PR if the project was on GitHub. lol. :/ :(
    $postTypes = \Breakdance\Settings\get_allowed_post_types();

    /* TODO: dont show links to client mode users */
    if (!in_array($post->post_type, $postTypes)) {
        return $actions;
    }

    if ($post->post_status === 'trash') {
        return $actions;
    }

    $mode = get_mode($post->ID);
    if ($mode !== 'breakdance') {
        return $actions;
    }

    $builder_loader_url = \Breakdance\Admin\get_builder_loader_url((string) $post->ID);

    $actions['breakdance_quick_action_link'] = <<<HTML
        <a href='{$builder_loader_url}'>Edit in Breakdance</a>
    HTML;

    return $actions;

}
