<?php

add_action('init', function() {
    $postTypes = \Breakdance\Settings\get_allowed_post_types();

    foreach($postTypes as $postType) {
        register_post_meta($postType, '_breakdance_hide_in_design_set', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);

        register_post_meta($postType, '_breakdance_tags', [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            }
        ]);
    }
});

add_action('enqueue_block_editor_assets', function() {
    if (!\Breakdance\DesignLibrary\isDesignLibraryEnabled()) return;

    wp_enqueue_script(
        'breakdance-design-library-editor',
        BREAKDANCE_PLUGIN_URL . '/plugin/design-library/editor/dist/editor.js',
        ['wp-edit-post']
    );
});
