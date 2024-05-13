<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\Themeless\get_posts_as_template_previewable_items;
use function Breakdance\Themeless\get_posts_with_search_args;
use function Breakdance\Util\get_public_post_types_excluding_templates;
use function Breakdance\Util\WP\get_supported_post_statuses;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsSingularRules'
);

function registerConditionsSingularRules()
{

    $breakdanceHeadersAndStuff = [
        BREAKDANCE_HEADER_POST_TYPE,
        BREAKDANCE_FOOTER_POST_TYPE,
        BREAKDANCE_BLOCK_POST_TYPE,
        BREAKDANCE_ACF_BLOCK_POST_TYPE,
        BREAKDANCE_POPUP_POST_TYPE
    ];

    $allSinglesPostType = 'all-singles';
    $postTypes = \Breakdance\Util\get_public_post_types_excluding_templates();
    $postTypesAndAllSingles = array_merge($postTypes, [$allSinglesPostType], $breakdanceHeadersAndStuff);

    // Create a template type and condition IS for each post type
    foreach ($postTypes as $postType) {
        $postTypeObj = get_post_type_object($postType);

        if (! $postTypeObj) {
            continue;
        }

        /**
         * @var string
         */
        $postName = $postTypeObj->labels->singular_name;

        // POST IS
        \Breakdance\Themeless\registerCondition(
            [
                'proOnly' => false,
                'supports' => ['element_display', 'templating', 'query_builder'],
                'availableForType' => array_merge([$postType, 'all-singles'], $breakdanceHeadersAndStuff),
                'slug' => 'post-dropdown-' . $postType,
                'label' => $postName,
                'category' => 'Singular',
                'operands' => [OPERAND_IS, OPERAND_IS_NOT],
                'values' => function () use ($postName, $postType) {
                    return [
                        [
                            'label' => $postName,
                            'items' => getPostsForMultiselect(['post_type' => $postType]),
                        ],
                    ];
                },
                'callback' =>
                /**
                 * @param string $operand
                 * @param string[] $value
                 * @return bool
                 */
                    function ($operand, $value) {
                        global $post;

                        if (!$post) return false;

                        /** @var \WP_Post */
                        $post = $post;


                        switch ($operand){
                            case OPERAND_IS:
                                return in_array($post->ID, $value, false);
                            case OPERAND_IS_NOT:
                                return !in_array($post->ID, $value, false);
                            default:
                                return false;
                        }
                    },
                'templatePreviewableItems' =>
                /**
                 * @param string $operand
                 * @param string[] $value
                 * @return TemplatePreviewableItem[]
                 */
                    function ($operand, $value) use ($postType) {
                            $postInOrNotIn = 'post__in';

                        if($operand === OPERAND_IS_NOT){
                            $postInOrNotIn = 'post__not_in';
                        }

                        return get_posts_as_template_previewable_items([
                            'post_type' => $postType,
                            $postInOrNotIn => array_map(function ($postIdString) {
                                return (int)$postIdString;
                            }, $value),
                        ]);
                    },
                'queryCallback' => /**
                 * @param WordPressQueryVars $query
                 * @param string $operand
                 * @param array $value
                 * @return WordPressQueryVars
                 */
                    function ($query, $operand, $value) {
                        $postIds = array_map(
                            /**
                             * @param array{value: string, text: string} $postData
                             */
                            function($postData){
                                return (int)$postData['value'];
                            },
                            $value
                        );

                        if (count($postIds) === 0) return $query;

                        $postInOrNotIn = '';

                        if ($operand === OPERAND_IS) {
                            $postInOrNotIn = 'post__in';
                        } else if ($operand === OPERAND_IS_NOT) {
                            $postInOrNotIn = 'post__not_in';
                        }

                        if (isset($query[$postInOrNotIn])) {
                            /** @var array $postsInQuery */
                            $postsInQuery = $query[$postInOrNotIn];

                            $query[$postInOrNotIn] = array_merge($postsInQuery, $postIds);
                        } else {
                            $query[$postInOrNotIn] = $postIds;
                        }

                        /** @var WordPressQueryVars */
                        return $query;
                    }
            ]
        );


        // HAS PARENT
        \Breakdance\Themeless\registerCondition(
            [
                'proOnly' => false,
                'supports' => ['element_display', 'templating'],
                'availableForType' => array_merge([$postType, 'all-singles'], $breakdanceHeadersAndStuff),
                'slug' => 'has-parent-' . $postType,
                'label' => "{$postName} Parent",
                'category' => 'Singular',
                'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
                'values' => function () use ($postName, $postType) {
                    return [
                        [
                            'label' => $postName,
                            'items' => getPostsForMultiselect([
                                'post_type' => $postType,
                                // give me all parents. This returns even posts with no children
                                // but the proper solution would need to check if each post has children
                                // and is probably not very performant
                                'post_parent' => 0,
                            ]),
                        ],
                    ];
                },
                'callback' =>
                /**
                 * @param string $operand
                 * @param string[] $value
                 * @return bool
                 */
                    function ($operand, $value) {
                        global $post;

                        if (!$post) return false;

                        /** @var \WP_Post */
                        $post = $post;

                        switch ($operand){
                            case OPERAND_ONE_OF:
                                return in_array($post->post_parent, $value);
                            case OPERAND_NONE_OF:
                                return !in_array($post->post_parent, $value);
                            default:
                                return false;
                        }
                    },
                'templatePreviewableItems' =>
                /**
                 * @param string $operand
                 * @param string[] $value
                 * @return TemplatePreviewableItem[]
                 */
                    function ($operand, $value) use ($postType) {
                        $postParentInOrNotIn = 'post_parent__in';

                        if ($operand === OPERAND_NONE_OF) {
                            $postParentInOrNotIn = 'post_parent__not_in';
                        }

                        return get_posts_as_template_previewable_items([
                            'post_type' => $postType,
                            $postParentInOrNotIn => $value,
                        ]);
                    },
            ]
        );
    }


    // HAS TAXONOMY
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'has-taxonomy',
            'label' => 'Has Taxonomy',
            'category' => 'Singular',
            'operands' => [OPERAND_ONE_OF, OPERAND_ALL_OF],
            'values' => function () use ($postTypes, $allSinglesPostType){
                return getTaxonomiesForMultiselect(
                /**
                 * @param \WP_Term $term
                 * @param \WP_Taxonomy $taxonomy
                 */
                    function (
                        $term,
                        $taxonomy
                    ) {

                        return [
                            'text' => $term->name ?: $term->slug,
                            'value' => json_encode([
                                'name' => $taxonomy->name,
                                'slug' => $term->slug,
                            ]),
                        ];
                    },
                    $postTypes,
                    [$allSinglesPostType]
                );
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value) {
                    $results = array_map(
                        function ($jsonValue) {
                            /**
                             * @var array{slug: string, name: string}
                             */
                            $termAndTax = json_decode($jsonValue, true);


                            if (isset($termAndTax['allInTax'])) {
                                return has_term(
                                    '',
                                    (string)$termAndTax['allInTax']
                                );
                            } elseif (isset($termAndTax['slug'], $termAndTax['name'])) {
                                return has_term(
                                    $termAndTax['slug'],
                                    $termAndTax['name']
                                );
                            }

                            return false;
                        },
                        $value
                    );

                    if ($operand === OPERAND_ONE_OF) {
                        return in_array(true, $results);
                    } elseif ($operand === OPERAND_ALL_OF) {
                        return ! in_array(false, $results);
                    } else {
                        // If something unexpected happens, say it doesn't apply.
                        // Probably less unexpected than saying it does since we have no clue what happened
                        return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @param string $postType
             */
                function ($operand, $value, $postType) {
                    /** @var string[] $allInTax */
                    $allInTax        = [];
                    $terms_and_taxes = [];

                    foreach ($value as $encodedTax) {
                        /**
                         * @var array
                         */
                        $values = json_decode($encodedTax, true);

                        if (isset($values['allInTax'])) {
                            $allInTax[] = (string)$values['allInTax'];
                        } elseif (isset($values['slug'], $values['name'])) {
                            $terms_and_taxes[] = [
                                'field' => 'slug',
                                'terms' => [$values['slug']],
                                'taxonomy' => $values['name'],
                            ];
                        } else {
                            $terms_and_taxes[] = false;
                        }
                    }

                    // the user selected "all in category/tag/etc"
                    // so let's get posts that have term in that taxonomy

                    // TODO can't we use `get_terms` to simplify this a lot? Look at the code in wpQueryFromArgs
                    // I think it's doing the same as this, but better and more performant
                    if (count($allInTax) > 0) {
                        $posts = get_posts_with_search_args([
                            'post_type' => $postType,
                        ]);

                        $previewableItems = [];
                        foreach ($posts as $post) {
                            // how performant is this? We're in a double loop querying each post for its terms, or is that cached somewhere already?
                            /** @var string $taxonomy */
                            foreach ($allInTax as $taxonomy) {
                                if (
                                    is_object_in_term($post->ID, $taxonomy)
                                    === true
                                ) {
                                    $permalink = get_permalink($post->ID);

                                    if (is_string($permalink)) {
                                        $previewableItems[] = [
                                            'label' => $post->post_title,
                                            'type' => $post->post_type,
                                            'url' => $permalink,
                                        ];
                                    }
                                }
                            }
                        }

                        return $previewableItems;
                    }

                    if ($operand === OPERAND_ONE_OF) {
                        $relation = "OR";
                    } elseif ($operand === OPERAND_ALL_OF) {
                        $relation = "AND";
                    } else {
                        return [];
                    }

                    return get_posts_as_template_previewable_items([
                        'post_type' => $postType,
                        'tax_query' => array_merge(
                            ['relation' => $relation],
                            $terms_and_taxes
                        ),
                    ]);
                },
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param array $value
             * @return WordPressQueryVars
             */
            function ($query, $operand, $value) {
                $taxQuery = $query['tax_query'] ?? [];
                /**
                 * @psalm-suppress MixedAssignment
                 * @psalm-suppress PossiblyInvalidIterator
                 */
                foreach ($value as $taxonomyData) {
                    /** @var array{value: string} */
                    $taxonomyData = $taxonomyData;
                    $taxonomyValue = $taxonomyData['value'];
                    /** @var array{allInTax: string, slug: string, name: string} */
                    $values = json_decode($taxonomyValue, true);

                    if (isset($values['allInTax'])) {
                        // the user selected "all in category/tag/etc"
                        /**
                         * @var string
                         */
                        $taxonomy = $values['allInTax'];
                        // get all terms for that taxonomy and add them to the query
                        /** @var \WP_Term[] $allTermsInTax */
                        $allTermsInTax = get_terms($taxonomy);

                        if (!is_wp_error($allTermsInTax)) {
                            $allSlugsInTerms = wp_list_pluck($allTermsInTax, 'slug');

                            $taxQuery[] = [
                                'field' => 'slug',
                                'terms' => $allSlugsInTerms,
                                'taxonomy' => $taxonomy,
                            ];
                        }
                    } elseif (isset($values['slug'], $values['name'])) {
                        $taxQuery[] = [
                            'field' => 'slug',
                            'terms' => [$values['slug']],
                            'taxonomy' => $values['name'],
                        ];
                    }
                }

                if (!empty($taxQuery)) {
                    if ($operand === OPERAND_ONE_OF) {
                        $taxQuery['relation'] = 'OR';
                    } elseif ($operand === OPERAND_ALL_OF) {
                        $taxQuery['relation'] = 'AND';
                    }
                    /** @var array<array-key, WordPressTaxQuery|string> $taxQuery */
                    $query['tax_query'] = $taxQuery;
                }

                return $query;
            }
        ]
    );


    // POST ID IS
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'post-id',
            'label' => 'Post ID',
            'category' => 'Singular',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return false;
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             * @return bool
             */
                function ($operand, $value) {
                    global $post;

                    if (!$post) return false;

                    /** @var \WP_Post */
                    $post = $post;

                    switch ($operand){
                        case OPERAND_IS:
                            return $post->ID === (int)$value;
                        case OPERAND_IS_NOT:
                            return $post->ID !== (int)$value;
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string $value
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value) {
                    switch ($operand){
                        case OPERAND_IS:
                            return get_posts_as_template_previewable_items([
                                'p' => (int)$value,
                            ]);
                        case OPERAND_IS_NOT:
                           return get_posts_as_template_previewable_items([
                                // without post_type we'd only get 'posts'
                                'post_type' => get_public_post_types_excluding_templates(),
                                'post__not_in' => [(int)$value]
                            ]);
                        default:
                            return [];
                    }
                },
        ]
    );

    // POST STATUS
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'post-status',
            'label' => 'Post Status',
            'category' => 'Singular',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return [
                    [
                        'label' => 'Statuses',
                        'items' => getPostStatusesForMultiselect()
                    ]
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function (string $operand, $value): bool {
                    $postStatusIsInArray = in_array(get_post_status(), $value);

                    if ($operand === OPERAND_IS) {
                        return $postStatusIsInArray;
                    } elseif ($operand === OPERAND_IS_NOT) {
                        return !$postStatusIsInArray;
                    }

                    return false;
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @param string $postType
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value, $postType) {
                    $potStatus = '';
                    if ($operand === OPERAND_IS) {
                        $potStatus = $value;
                    } elseif ($operand === OPERAND_IS_NOT) {
                        $potStatus = array_filter(
                            get_supported_post_statuses(),
                            function ($status) use ($value) {
                                return ! in_array($status, $value);
                            }
                        );
                    }

                    return get_posts_as_template_previewable_items(
                        [
                            'post_type' => $postType,
                            'post_status' => $potStatus,
                        ]
                    );
                },
        ]
    );


    // COMMENTS NUMBER
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'comments-number',
            'label' => 'Comments Number',
            'category' => 'Singular',
            'operands' => [
                OPERAND_GREATER_THAN,
                OPERAND_LESS_THAN,
                OPERAND_IS,
                OPERAND_IS_NOT,
            ],
            'valueInputType' => 'number',
            'values' => function () {
                return false;
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             * @return bool
             */
                function (string $operand, $value) {
                    if ($operand === OPERAND_IS) {
                        return $value == get_comments_number();
                    } elseif ($operand === OPERAND_IS_NOT) {
                        return $value != get_comments_number();
                    } elseif ($operand === OPERAND_GREATER_THAN) {
                        return get_comments_number() > $value;
                    } elseif ($operand === OPERAND_LESS_THAN) {
                        return get_comments_number() < $value;
                    } else {
                        // should never reach here
                        return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string $value
             * @param string $postType
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value, $postType) {
                    if ($operand === OPERAND_IS) {
                        $comparison = '=';
                    } elseif ($operand === OPERAND_IS_NOT) {
                        $comparison = '!=';
                    } elseif ($operand === OPERAND_GREATER_THAN) {
                        $comparison = '>';
                    } elseif ($operand === OPERAND_LESS_THAN) {
                        $comparison = '<';
                    } else {
                        return [];
                    }

                    return get_posts_as_template_previewable_items(
                        [
                            'post_type' => $postType,
                            'comment_count' => [
                                'value' => (int)$value,
                                'compare' => $comparison,
                            ],
                        ]
                    );
                },
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param string $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    $comparison = '';

                    if ($operand === OPERAND_IS) {
                        $comparison = '=';
                    } elseif ($operand === OPERAND_IS_NOT) {
                        $comparison = '!=';
                    } elseif ($operand === OPERAND_GREATER_THAN) {
                        $comparison = '>';
                    } elseif ($operand === OPERAND_LESS_THAN) {
                        $comparison = '<';
                    }

                    $query['comment_count'] = [
                        'value' => (int) $value,
                        'compare' => $comparison,
                    ];

                    return $query;
                }
        ]
    );

    // POST AUTHOR
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'post-author',
            'label' => 'Author',
            'category' => 'Singular',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return [
                    [
                        'label' => 'Authors',
                        'items' => getAuthorItemsForDropdown(),
                    ],
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value) {
                    global $post;

                    if (!$post) return false;

                    /** @var \WP_Post */
                    $post = $post;

                    $isCurrentAuthorInValue
                        = in_array((string)$post->post_author, $value);
                    if ($operand === OPERAND_IS) {
                        return $isCurrentAuthorInValue;
                    } elseif ($operand === OPERAND_IS_NOT) {
                        return ! $isCurrentAuthorInValue;
                    } else {
                        return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @param string $postType
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value, $postType) {
                    if ($operand === OPERAND_IS) {
                        $authorCondition = 'author__in';
                    } elseif ($operand === OPERAND_IS_NOT) {
                        $authorCondition = 'author__not_in';
                    } else {
                        return [];
                    }

                    return get_posts_as_template_previewable_items([
                        'post_type' => $postType,
                        $authorCondition => $value,
                    ]);
                },
                'queryCallback' => /**
                 * @param WordPressQueryVars $query
                 * @param string $operand
                 * @param DropdownData[] $value
                 * @return WordPressQueryVars
                 */
                function ($query, $operand, $value) {
                    $authorOperand = $operand;
                    $authors = $value;

                    $authorIds = array_map(function ($author) {
                        return (int)$author['value'];
                    }, $authors);

                    if ($authorOperand === OPERAND_IS) {
                        $query['author__in'] = $authorIds;
                    } elseif ($authorOperand === OPERAND_IS_NOT) {
                        $query['author__not_in'] = $authorIds;
                    }

                    return $query;
                }
        ]
    );

    // FEATURED IMAGE
    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating', 'query_builder'],
            'availableForType' => $postTypesAndAllSingles,
            'slug' => 'featured-image',
            'label' => 'Featured Image',
            'category' => 'Singular',
            'operands' => [OPERAND_IS],
            'valueInputType' => 'dropdown',
            'values' => function () {
                return [
                    [
                        'label' => 'Status',
                        'items' => [
                            ['text' => 'set', 'value' => 'set'],
                            ['text' => 'not set', 'value' => 'not set']
                        ]
                    ]
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             * @return bool
             */
                function ($operand, $value) {
                    if ($value === 'set') {
                        return has_post_thumbnail();
                    } elseif ($value === 'not set') {
                        return ! has_post_thumbnail();
                    } else {
                        return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string $value
             * @param string $postType
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value, $postType) {
                        return get_posts_as_template_previewable_items(
                            [
                                'post_type' => $postType,
                                'meta_query' => [
                                    [
                                        'key' => '_thumbnail_id',
                                        'compare' => $value === 'set'
                                            ? 'EXISTS'
                                            : 'NOT EXISTS',
                                    ],
                                ]
                            ]
                        );
                },
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param mixed $operand
             * @param string $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    $query['meta_query'][] = [
                        'key' => '_thumbnail_id',
                        'compare' => $value === 'set' ? 'EXISTS' : 'NOT EXISTS'
                    ];

                    return $query;
                },
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'proOnly' => false,
            'supports' => ['element_display', 'templating'],
            'availableForType' => array_merge(['all-singles'], $breakdanceHeadersAndStuff),
            'slug' => 'post-type',
            'label' => "Post Type",
            'category' => 'Singular',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () use ($postTypes) {
                return [
                    [
                        'label' => 'Post Type',
                        'items' => array_map(static function ($postType) {
                            $postTypeObject = get_post_type_object($postType);
                            return [
                                'text' => $postTypeObject ? $postTypeObject->label : $postType,
                                'value' => $postType
                            ];
                        }, $postTypes),
                    ],
                ];
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value) {
                    global $post;

                    if (!$post) return false;

                    /** @var \WP_Post */
                    $post = $post;

                    switch ($operand) {
                        case OPERAND_ONE_OF:
                            return in_array($post->post_type, $value);
                        case OPERAND_NONE_OF:
                            return !in_array($post->post_type, $value);
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return TemplatePreviewableItem[]
             */
                function ($operand, $value) use ($postTypes) {
                    $postTypesToIncludeForPreview = $value;

                    if ($operand === OPERAND_NONE_OF) {
                        $postTypesToIncludeForPreview = array_diff($postTypes, $value);
                    }

                    return get_posts_as_template_previewable_items([
                        'post_type' => $postTypesToIncludeForPreview,
                    ]);
                },
        ]
    );
}

// ---- Helper functions ---- ///

/**
 * @return TemplateConditionValue[]
 */
function getPostStatusesForMultiselect()
{
    /** @var object{label: string, name: string}[] $stati */
    $stati = array_values(get_post_stati(['_builtin' => true, 'internal' => false], 'objects'));

    return array_map(
        function ($status) {
            return [
                // using the name because the label can be misleading
                // like acf using "disabled" as a label, a user wouldn't know what it is.
                'text' => $status->name,
                'value' => $status->name,
            ];
        },
        $stati
    );
}

/**
 * @param array $args
 * @return TemplateConditionValue[]
 */
function getPostsForMultiselect($args = [])
{
    return array_map(
    /**
     * @param \WP_Post $post
     */
        function ($post) {
            return [
                'text' => $post->post_title,
                'value' => (string)$post->ID,
            ];
        },
        get_posts_with_search_args($args)
    );
}
