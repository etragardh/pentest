<?php

namespace Breakdance\Themeless;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler('breakdance_save_template_last_previewed_item', 'Breakdance\Themeless\save_template_last_previewed_item', 'edit');
});

/**
 * @return array{success: true} | array{error: string}
 */
function save_template_last_previewed_item()
{
    $postId = (int) filter_input(INPUT_POST, 'postId');
    $previewedItem = (string) filter_input(INPUT_POST,'item');

    if ($previewedItem && $postId) {
        \Breakdance\Data\set_meta($postId, 'template_last_previewed_item', json_decode($previewedItem));

        return ['success' => true];
    }

    return ['error' => 'No ID or previewed item provided'];
}
