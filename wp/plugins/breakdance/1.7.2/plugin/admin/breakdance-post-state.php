<?php

namespace Breakdance\Admin;

add_filter('display_post_states',
    /**
     * @param array $states
     * @param \WP_Post $post
     */
    function ($states, $post) {
        $hasPermissions = \Breakdance\Permissions\hasMinimumPermission('edit');

        if ($hasPermissions && \Breakdance\Data\get_tree($post->ID) !== false) {
            $states['breakdance'] = 'Breakdance';
        }

        return $states;
    },
    10,
    2
);
