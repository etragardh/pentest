<?php

namespace Breakdance\Admin;

use Breakdance\Themeless\ThemelessController;

use function Breakdance\Themeless\_getTemplateForRequest;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;

add_action('breakdance_loaded', function () {
    add_action('init', function () {
        if (\Breakdance\Permissions\hasPermission('full')) {
            add_action('admin_bar_menu', '\Breakdance\Admin\breakdance_admin_bar_menu', 1000);
        }
    });
});

function breakdance_admin_bar_menu()
{

    /* TODO: dont show menu to client mode users */

    global $wp_admin_bar, $post;

    /** @var \WP_Post|null */
    $post = $post;

    if (!$post) {
        return;
    }

    $postId = (int) $post->ID;
    $postTitle = (string) $post->post_title;

    $menu_items_to_add = [];

    $enabled = false;

    if (is_singular()) {
        // TODO: memoize for performance
        // i cant believe wordpress APIs dont have a per-post-type action for these quick action links... is that for real
        // id add it myself and submit a PR if the project was on GitHub. lol. :/ :(
        $postTypes = \Breakdance\Settings\get_allowed_post_types();

        if (in_array($post->post_type, $postTypes)) {
            $editUrl = get_edit_post_link($postId);
            if (get_mode($postId) === 'breakdance') {
                $editUrl = \Breakdance\Admin\get_builder_loader_url($postId);
            }
            $menu_items_to_add[] = [
                'id' => 'edit_with_breakdance',
                'parent' => 'breakdance_admin_bar_menu',
                'title' => 'Open \'' . $postTitle . '\'',
                'href' => $editUrl,
            ];
        }
    }

    /**
     * @var null|int[]
     */
    $hierarchy = \Breakdance\Themeless\ThemelessController::getInstance()->originalTemplateHierarchyForRequest;

    if (is_array($hierarchy) && count($hierarchy) > 0) {
        $enabled = true; // If a page has a template, show the status as green.

        foreach ($hierarchy as $post_id) {

            /** @var \WP_Post */
            $dont_call_this_post_because_that_would_mutate_global_state = get_post($post_id);

            $templateSettings = getTemplateSettingsFromDatabase($post_id);

            if ($templateSettings['fallback'] ?? false) {
                $enabled = false;
                break;
            }

            $menu_items_to_add[] = [
                'id' => 'edit_template_with_breakdance_' . (string) $post_id,
                'parent' => 'breakdance_admin_bar_menu',
                'title' => 'Open \'' . $dont_call_this_post_because_that_would_mutate_global_state->post_title . '\' Template',
                'href' => \Breakdance\Admin\get_builder_loader_url((string) $dont_call_this_post_because_that_would_mutate_global_state->ID),
            ];
        }
    }

    $header = _getTemplateForRequest(ThemelessController::getInstance()->headers);

    if ($header) {
        /** @var \WP_Post */
        $dont_call_this_post_because_that_would_mutate_global_state = get_post($header['id']);
        $menu_items_to_add[] = [
            'id' => 'edit_header_with_breakdance_' . (string) $header['id'],
            'parent' => 'breakdance_admin_bar_menu',
            'title' => 'Open \'' . $dont_call_this_post_because_that_would_mutate_global_state->post_title . '\' Header',
            'href' => \Breakdance\Admin\get_builder_loader_url((string) $dont_call_this_post_because_that_would_mutate_global_state->ID),
        ];
    }

    $footer = _getTemplateForRequest(ThemelessController::getInstance()->footers);

    if ($footer) {
        /** @var \WP_Post */
        $dont_call_this_post_because_that_would_mutate_global_state = get_post($footer['id']);

        $menu_items_to_add[] = [
            'id' => 'edit_footer_with_breakdance_' . (string) $footer['id'],
            'parent' => 'breakdance_admin_bar_menu',
            'title' => 'Open \'' . $dont_call_this_post_because_that_would_mutate_global_state->post_title . '\' Footer',
            'href' => \Breakdance\Admin\get_builder_loader_url((string) $dont_call_this_post_because_that_would_mutate_global_state->ID),
        ];
    }

    $enabled = $enabled ? true : \Breakdance\Data\get_tree($postId) !== false;

    $status_color = ($enabled ? 'lightgreen' : 'grey');
    $status_dot = "<span style='line-height: 16px; font-size:16px; color: " . $status_color . ";'>&#8226;</span>";

    if (count($menu_items_to_add) > 0) {
        /** @psalm-suppress MixedMethodCall */
        $wp_admin_bar->add_menu([
            'id' => 'breakdance_admin_bar_menu',
            'title' => 'Breakdance ' . $status_dot,
            'href' => false,
        ]);

        $menu_items_to_add[] = [
            'id' => 'breakdance_browse_mode',
            'parent' => 'breakdance_admin_bar_menu',
            'title' => 'Edit Global Styles',
            'href' => get_browse_mode_url_with_return_back_to_current_page(get_current_page_url())
        ];
    }

    foreach ($menu_items_to_add as $menu_item_to_add) {
        /** @psalm-suppress MixedMethodCall */
        $wp_admin_bar->add_menu(
            $menu_item_to_add
        );
    }

    /* maybe always show the admin menu, and if there's nothing ot put in it,
just say "this page isnt using breakdance? i mean, a page could use a block and shit tho. lol.
maybe when ever shit is rendered add it to a render log or something so we can display it in the admin menu, i.e. used blocks:
 */
}
