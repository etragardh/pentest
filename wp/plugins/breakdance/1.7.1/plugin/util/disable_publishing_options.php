<?php

namespace Breakdance\Util;

/**
 * @param string $post_type_slug
 * @return void
 */
function disable_publishing_options_and_attributes_metabox_and_force_status_to_publish($post_type_slug)
{

    // disable misc publishing options (date, etc) and forces post status to publish
    // https://wordpress.stackexchange.com/a/147187

    add_action('admin_head',
        function () use ($post_type_slug) {
            $screen = get_current_screen();
            if ($screen && $screen->id === $post_type_slug) {
                echo '<style>#minor-publishing { display: none; }</style>';
            }
        }
    );

    add_filter('wp_insert_post_data',
        /**
         * @param array $post
         * @return array
         */
        function ($post) use ($post_type_slug) {
            /* We still want to use the trash */
            if (array_key_exists("post_status", $post) && $post['post_status'] !== 'trash' && $post['post_status'] !== 'auto-draft') {
                if (isset($post['post_type']) && $post['post_type'] === $post_type_slug) {
                    $post['post_status'] = 'publish';
                }
            }
            return $post;
        }
    );

    // also remove the 'page attributes' metabox (where the user can choose a WordPress 'Template')

    add_action('admin_menu', function () use ($post_type_slug) {
        remove_meta_box('pageparentdiv', $post_type_slug, 'side');
    });

}
