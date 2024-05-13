<?php

namespace Breakdance\Blocks;

use function Breakdance\Admin\get_builder_loader_url;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_load_blocks', '\Breakdance\Blocks\load', 'edit', true);
});

/**
 * @return array{blocks: GlobalBlock[]}
 */
function load()
{
    /**
     * @var \WP_Post[]
     */
    $posts = get_posts([
        'post_type' => BREAKDANCE_BLOCK_POST_TYPE,
        'post_status' => 'publish',
        'numberposts' => -1,
    ]);

    $blocks = array_map(function ($post) {

        $tree = \Breakdance\Data\get_tree($post->ID);

        return [
            'label' => $post->post_title,
            'id' => intval($post->ID),
            'tree' => $tree,
            'editInBreakdanceLink' => get_builder_loader_url($post->ID)
        ];
    }, $posts);

    return [
        'blocks' => $blocks,
    ];

}
