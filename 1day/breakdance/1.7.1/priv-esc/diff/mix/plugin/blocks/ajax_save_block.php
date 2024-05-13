<?php

namespace Breakdance\Blocks;

use function Breakdance\Admin\get_builder_loader_url;
use function Breakdance\Data\set_meta;
use function Breakdance\Themeless\getTemplateSettingsFromDatabase;

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_save_global_block',
        '\Breakdance\Blocks\saveGlobalBlock',
        'edit',
        true,
        [
            'args' => [
                'label' => FILTER_UNSAFE_RAW,
                'id' => FILTER_VALIDATE_INT,
                'tree' => FILTER_UNSAFE_RAW
            ]
        ]
    );
    \Breakdance\AJAX\register_handler(
        'breakdance_save_acf_block',
        '\Breakdance\Blocks\createAcfBlock',
        'edit',
        true,
        [
            'args' => [
                'field' => FILTER_SANITIZE_SPECIAL_CHARS,
                'postId' => FILTER_VALIDATE_INT,
            ]
        ]
    );
    \Breakdance\AJAX\register_handler(
        'breakdance_delete_acf_block',
        '\Breakdance\Blocks\deleteAcfBlock',
        'edit',
        true,
        [
            'args' => [
                'field' => FILTER_SANITIZE_SPECIAL_CHARS,
                'postId' => FILTER_VALIDATE_INT,
            ]
        ]
    );
});

/**
 * @param string $label
 * @param int $id
 * @param string $tree
 * @param string $postType
 * @return array[]|string[]
 */
function saveGlobalBlock($label, $id, $tree, $postType = BREAKDANCE_BLOCK_POST_TYPE)
{
    $label = sanitize_text_field($label);

    /* ID needs to be -1 to create a post, because if it's something
    falsy like 0, it will return the current post in the loop. */
    $post = get_post($id);
    if ($post) {
        $idOrError = wp_update_post([
            'ID' => $id,
            'post_title' => $label
        ], true);
    } else{
        $idOrError = wp_insert_post(
            [
                'post_title' => $label,
                'post_type' => $postType,
            ],
            true
        );
    }

    if (isset($idOrError) && !$idOrError || is_wp_error($idOrError)) {
        return ['error' => "Failed to save Global Block \"{$label}\""];
    }

    /** @var int $id */
    $id = $idOrError;

    set_meta(
        $id,
        'breakdance_data',
        [
            'tree_json_string' => $tree,
        ]
    );

    \Breakdance\Render\generateCacheForPost($id);

    $post = get_post($id);

    if (!$post) {
        return ['error' => "Couldn't get template with ID \"{$id}\""];
    }

    /** @var \WP_Post */
    $post = $post;

    return [
        'block' => [
            'id' => $post->ID,
            'label' => $post->post_title,
            'tree' => json_decode($tree),
            'editInBreakdanceLink' => get_builder_loader_url($post->ID),
            'status' => 'publish'
        ]
    ];
}


/**
 * @param string $field
 * @param int $postId
 * @return array[]|string[]
 */
function createAcfBlock($field, $postId) {
    if (!function_exists('get_field') || !function_exists('acf_get_field')) {
        return [];
    }
    /** @var ACFField $fieldData */
    $fieldData = acf_get_field($field);
    $existingBlockId = (int) get_field($field, $postId, false);
    $label = $fieldData['label'] . ' - ' . get_the_title($postId);
    if (!$existingBlockId) {
        $newAcfBlock = saveGlobalBlock($label, -1, '', BREAKDANCE_ACF_BLOCK_POST_TYPE);
        /** @var int|false $newBlockId */
        $newBlockId = $newAcfBlock['block']['id'] ?? false;
        if ($newBlockId) {
            update_field($field, $newBlockId, $postId);
            add_post_meta($newBlockId, 'breakdance_acf_content_parent', $postId);
        }
        return $newAcfBlock;
    }

    $builderUrl = add_query_arg(['postId' => $postId], get_builder_loader_url($existingBlockId));

    return [
        'block' => [
            'id' => $existingBlockId,
            'label' => $label,
            'tree' => false,
            'editInBreakdanceLink' => $builderUrl
        ]
    ];
}

/**
 * @param string $field
 * @param int|string $postId
 * @return array{deleted: \WP_Post|false|null}
 */
function deleteAcfBlock($field, $postId) {
    if (!function_exists('get_field') || !function_exists('delete_field')) {
        return ['deleted' => false];
    }
    $blockId = (int) get_field($field, $postId, false);
    $acfBlock = get_post($blockId);
    $deleted = false;
    if ($acfBlock instanceof \WP_Post && $acfBlock->post_type === BREAKDANCE_ACF_BLOCK_POST_TYPE) {
        $deleted = wp_delete_post($acfBlock->ID, true);
        delete_field($field, $postId);
    }

    return ['deleted' => $deleted];
}
