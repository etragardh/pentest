<?php

namespace Breakdance\Util\WP;

add_filter('posts_where', 'Breakdance\Util\WP\filter_posts_by_title', 10, 2);

/**
 * @param string $where
 * @param \WP_Query $wp_query
 * @return string
 */
function filter_posts_by_title($where, $wp_query)
{
    global $wpdb;

    // Add 'breakdance_search_post_title' to the 'args' of a get_posts for this to run
    if ($postTitle = (string)$wp_query->get('breakdance_search_post_title')) {
        /**
         * @var string $escapedPostTitle
         * @psalm-suppress MixedMethodCall
         */
        $escapedPostTitle = esc_sql((string)$wpdb->esc_like($postTitle));

        /**
         * @psalm-suppress MixedPropertyFetch
         */
        $where .= " AND {$wpdb->posts}.post_title LIKE '%{$escapedPostTitle}%'";
    }

    return $where;
}

/**
 * @return string[]
**/
function get_supported_post_statuses(){
    return [
        'publish',
        'pending',
        'draft',
        'future',
        'private',
        'inherit',
    ];
}

/**
 * @param array $args
 * @return \WP_Post[]
 */
function performant_get_posts($args)
{
    /**
     * @var \WP_Post[]
     */
    $posts = get_posts(
        array_merge(
            [
                // needed to run custom filters
                'suppress_filters' => false,
                'post_status' => get_supported_post_statuses(),
                // You should use a limit when calling this function.
                'posts_per_page' => -1,
            ],
            $args,
        )
    );

    return $posts;
}
