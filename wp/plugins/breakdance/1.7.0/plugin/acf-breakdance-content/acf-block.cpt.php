<?php

namespace Breakdance\Blocks;

use function Breakdance\Themeless\getTemplateCptsSharedArgs;

/**
 * @return void
 */
function register_acf_post_type()
{
    \register_post_type(
        BREAKDANCE_ACF_BLOCK_POST_TYPE,
        array_merge(
            [
                'labels' => [
                    'name' => 'ACF Block',
                    'singular_name' => 'ACF Block',
                ],
            ],
            getTemplateCptsSharedArgs()
        )
    );

    \Breakdance\Util\disable_publishing_options_and_attributes_metabox_and_force_status_to_publish(BREAKDANCE_ACF_BLOCK_POST_TYPE);

}

/**
 * @param int $postId
 * @return void
 */
function deleteAcfContentBlocks($postId) {
    if (!function_exists('get_field_objects')) {
        return;
    }
    /** @var ACFField[]|false $fields */
    $fields = get_field_objects($postId);
    if (!$fields) {
        return;
    }
    $breakdanceFields = array_filter($fields, static function($field) {
        return $field['type'] === 'BREAKDANCE_CONTENT';
    });
    if (!empty($breakdanceFields)) {
        foreach ($breakdanceFields as $breakdanceField) {
            deleteAcfBlock($breakdanceField['name'], $postId);
        }
    }
}
add_action('before_delete_post', 'Breakdance\Blocks\deleteAcfContentBlocks');
