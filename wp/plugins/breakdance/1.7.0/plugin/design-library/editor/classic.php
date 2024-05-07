<?php

namespace Breakdance\DesignLibrary;

use function Breakdance\Util\is_post_request;

function addClassicMetabox()
{
    $screen = get_current_screen();
    // If you add a new field here, don't forget to add it to gutenberg as well. (src/fields.jsx)
    if (!\Breakdance\DesignLibrary\isDesignLibraryEnabled()) return;
    if ($screen && $screen->is_block_editor()) return;

    $postTypes = \Breakdance\Settings\get_allowed_post_types();

    foreach($postTypes as $postType) {
        add_meta_box(
         'breakdance-design-library-fields',
            'Breakdance Design Library',
            /**
             * @param \WP_Post $post
             * @return void
             */
            function($post) {
                wp_nonce_field(__FILE__, '_bd_classic_nonce');
                $checked = (bool) get_post_meta($post->ID, '_breakdance_hide_in_design_set', true);
                /** @var string $tags */
                $tags = get_post_meta($post->ID, '_breakdance_tags', true);
                ?>
                <p>
                    <label><input type="checkbox" name="breakdance_hide_in_set" <?php echo $checked ? 'checked' : ''; ?>> Hide in Design Set?</label>
                </p>
                <div>
                    Tags
                    <input type="text" class="large-text" name="breakdance_tags" value="<?php echo esc_attr($tags); ?>">
                    <p class="howto">Separate tags with commas</p>
                </div>
                <?php
            },
            $postType,
            'side'
        );
    }
}
add_action('add_meta_boxes', '\Breakdance\DesignLibrary\addClassicMetabox');

/**
 * @param int $post_id
 * @return void
 */
function onClassicSave($post_id)
{
    /** @var string $nonce */
    $nonce = filter_input(INPUT_POST, '_bd_classic_nonce', FILTER_SANITIZE_SPECIAL_CHARS);

    if (!is_post_request()) return;
    if (!wp_verify_nonce($nonce, __FILE__)) return;

    $postTypes = \Breakdance\Settings\get_allowed_post_types();
    $postType = get_post_type($post_id);

    if (!in_array($postType, $postTypes)) return;

    $showInSet = filter_input(INPUT_POST, 'breakdance_hide_in_set', FILTER_UNSAFE_RAW) == 'on';
    /** @var string $tags */
    $tags = filter_input(INPUT_POST, 'breakdance_tags', FILTER_SANITIZE_SPECIAL_CHARS);

    update_post_meta($post_id, '_breakdance_hide_in_design_set', $showInSet);
    update_post_meta($post_id, '_breakdance_tags', $tags);
}
add_action('save_post', '\Breakdance\DesignLibrary\onClassicSave');
