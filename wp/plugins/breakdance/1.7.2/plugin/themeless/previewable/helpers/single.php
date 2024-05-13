<?php

namespace Breakdance\Themeless;

use function Breakdance\Util\WP\performant_get_posts;

/**
 * @param array $args
 * @return TemplatePreviewableItem[]
 */
function get_posts_as_template_previewable_items($args = [])
{
    /**
     * @var TemplatePreviewableItem[]
     */
    $previewableItems = [];
    foreach (get_posts_with_search_args($args) as $post) {
        $permalink = get_permalink($post->ID);

        if (is_string($permalink)) {
            $previewableItems[] = [
                'label' => $post->post_title,
                'type' => $post->post_type,
                'url' => $permalink,
            ];
        }
    }

    return $previewableItems;
}

/**
 * Note: the "relation" in args' "meta_query" will always be "AND". Use a nested meta query if you need "OR".
 * @param array $args
 * @return \WP_Post[]
 */
function get_posts_with_search_args($args = [])
{
    $search = SearchContext::getInstance()->search;

    $searchArg = $search
        ? ['breakdance_search_post_title' => $search]
        : ['posts_per_page' => TEMPLATE_POSTS_LIMIT];

    /** @var array $metaQuery */
    $metaQuery = $args['meta_query'] ?? [];
    $allPostsExcludingOnesWithBlankTemplates = [
        'meta_query' => array_merge(
            $metaQuery,
            [
                'relation' => 'AND',
                [
                    'relation' => 'OR',
                    [
                        'key' => '_wp_page_template',
                        'compare' => 'NOT EXISTS'
                    ],
                    [
                        'key' => '_wp_page_template',
                        'value' => 'breakdance_blank_canvas',
                        'compare' => '!='
                    ]
                ],
            ],
        )
    ];

    return performant_get_posts(
        array_merge(
            $args,
            $searchArg,
            $allPostsExcludingOnesWithBlankTemplates
        )
    );
}
