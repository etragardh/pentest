<?php

namespace Breakdance\AjaxEndpoints;

use function Breakdance\Util\WP\performant_get_posts;

/**
 * @psalm-type PostMeta = array{id: int, title: string, thumbnail: string|false}
 */

add_action('breakdance_loaded', function () {
    \Breakdance\AJAX\register_handler(
        'breakdance_get_posts',
        'Breakdance\AjaxEndpoints\getPosts',
        'edit',
        true,
        [
            'args' => [
                'search' => FILTER_UNSAFE_RAW,
                'postType' => FILTER_SANITIZE_SPECIAL_CHARS
            ],
            'optional_args' => ['postType'],
        ]
    );
});

/**
 * @param string $searchString
 * @param string|null $postType
 * @return array{data: PostMeta[]}
 */
function getPosts($searchString, $postType)
{
    $postType = $postType ?? 'post';

    $posts = performant_get_posts([
        's' => $searchString,
        'post_type' => $postType,
        'posts_per_page' => TEMPLATE_POSTS_LIMIT,
    ]);

    $formattedPosts = array_map(
        '\Breakdance\AjaxEndpoints\formatPost',
        $posts
    );

    return ['data' => $formattedPosts];
}

/**
 * @param \WP_Post $post
 * @return PostMeta
 */
function formatPost($post)
{
    $thumbnail = get_the_post_thumbnail_url($post->ID, 'thumbnail');

    return [
        'id' => $post->ID,
        'title' => $post->post_title,
        'thumbnail' => $thumbnail,
    ];
}
