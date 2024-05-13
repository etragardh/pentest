<?php

namespace Breakdance\Util\WP;

/**
 * @param string|false $searchTerm
 * @return \WP_User[]
 * @psalm-suppress MixedReturnTypeCoercion
 */
function get_authors($searchTerm)
{
    return get_users($searchTerm
        ? [
            'search' => '*'.$searchTerm.'*',
            'search_columns' => ['user_nicename', 'display_name'],
            // @see https://make.wordpress.org/core/2022/01/05/new-capability-queries-in-wordpress-5-9/
            'capabilities' => ['edit_posts'],
        ]
        : ['number' => TEMPLATE_POSTS_LIMIT, 'capabilities' => ['edit_posts']]
    );
}

/**
 * @param int $authorId
 * @return string
 */
function get_author_permalink($authorId)
{
    return get_author_posts_url($authorId);
}
