<?php

namespace Breakdance\Admin;

function gutenberg_launcher_scripts()
{
    if (!is_breakdance_available()) {
        return;
    }

    $url = BREAKDANCE_PLUGIN_URL . 'plugin/admin/launcher/js/gutenberg.js';
    wp_enqueue_script('breakdance-launcher-gutenberg', $url, ['breakdance-launcher-shared']);
}
add_action('enqueue_block_editor_assets', 'Breakdance\Admin\gutenberg_launcher_scripts');

function add_launcher_as_block_template()
{
    global $pagenow;

    if (!is_breakdance_available()) {
        return;
    }

    if (!in_array($pagenow, array( 'post.php', 'post-new.php' ))) {
        return;
    }

    $screen = get_current_screen();

    if (!$screen) {
        return;
    }

    $post_type = get_post_type_object($screen->post_type);

    if (!$post_type) {
        return;
    }

    /**
     * @psalm-suppress UndefinedPropertyAssignment
     */
    $post_type->template = [
        ['breakdance/block-breakdance-launcher'],
    ];
}
add_action('current_screen', '\Breakdance\Admin\add_launcher_as_block_template');
