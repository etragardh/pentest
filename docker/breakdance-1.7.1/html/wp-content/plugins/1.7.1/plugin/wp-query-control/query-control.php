<?php

namespace Breakdance\WpQueryControl;

use function Breakdance\Util\WP\isAnyArchive;

/**
 * @param QueryControlParams|null $wpQueryControlProperties
 * @param array $extraArgsToMerge
 * @psalm-suppress MixedInferredReturnType
 * @return array|string
 */
function getWpQueryArgumentsFromWpQueryControlProperties($wpQueryControlProperties, $extraArgsToMerge = [])
{
    if (isAnyArchive() && !$wpQueryControlProperties) {
        /**
         * @psalm-suppress InvalidGlobal
         */
        global $wp_query;

        /**
         * Use the default query when there's no "Custom Query" selected
         *
         * @psalm-suppress MixedReturnStatement
         * @psalm-suppress MixedPropertyFetch
         */
        return $wp_query->query_vars;
    }

    if (!$wpQueryControlProperties) {
        $defaultQuery = array_merge(
        // these defaults are the same as in "WpQuery.vue", keep them synced
            [
                'posts_per_page' => 8,
            ],
            $extraArgsToMerge
        );

        /** @var array $defaultQuery */
        $defaultQuery = apply_filters('breakdance_query_control_query', $defaultQuery);

        return $defaultQuery;
    }

    $wpCustomQuery = $wpQueryControlProperties['custom'];
    $wpTextQuery = $wpQueryControlProperties['text'];
    $phpCodeForQuery = $wpQueryControlProperties['php'];
    $activeQuery = $wpQueryControlProperties['active'];

    if ($activeQuery === "text"){
        // We ignore $extraArgsToMerge here
        /** @var string $wpTextQuery */
        $wpTextQuery = apply_filters('breakdance_query_control_query', $wpTextQuery);

        return $wpTextQuery;
    }

    if ($activeQuery === "php"){
        // If the "eval" fails the SSR will fail
        /** @psalm-suppress MixedReturnStatement */
        $manualPhpQuery = array_merge($extraArgsToMerge, eval($phpCodeForQuery));

        /** @var array $manualPhpQuery */
        $manualPhpQuery = apply_filters('breakdance_query_control_query', $manualPhpQuery);

        return $manualPhpQuery;
    }

    /** @psalm-suppress RedundantConditionGivenDocblockType
     *I know it's redundant but I like the explicitness, Psalm
     */
    if (
        $activeQuery === "custom" &&
        $wpCustomQuery['source'] === 'post_types' &&
        isset($wpCustomQuery['conditions']) &&
        sizeof($wpCustomQuery['conditions'])
    ) {
        $metaQuery = getValidWpMetaQuery($wpCustomQuery['metaQuery'] ?? null);

        $queryArguments = [
            'post_type' => $wpCustomQuery['postTypes'],
            'post_status' => 'publish',
            'ignore_sticky_posts' => $wpCustomQuery['ignoreStickyPosts'],
            'date_query' => getDateQuery(
                $wpCustomQuery['date'],
                $wpCustomQuery['beforeDate'],
                $wpCustomQuery['afterDate']
            ),
            'meta_query' => $metaQuery ?: [],
            'post__not_in' => $wpCustomQuery['ignoreCurrentPost'] === true && get_the_ID() ? [get_the_ID()] : null,
            'posts_per_page' => getPostsPerPage($wpCustomQuery['postsPerPage'] ?? 8, $wpCustomQuery['totalPosts'] ?? null),
            // offset makes "paged" invalid and destroys pagination, so ignore it if its set
            'offset' => isset($extraArgsToMerge['paged']) && $extraArgsToMerge['paged'] ? null : $wpCustomQuery['offset'] ?? null,
        ];

        /** @var WordPressQueryVars $conditionArguments */
        $conditionArguments = [];
        // conditions is always an array of conditions, but only the first will be populated by the QueryControl
        [$queryConditions] = $wpCustomQuery['conditions'];
        foreach ($queryConditions as $queryCondition) {
            if (!array_key_exists('ruleSlug', $queryCondition) || !array_key_exists('value', $queryCondition)) {
                continue;
            }
            $condition = \Breakdance\Themeless\ThemelessController::getInstance()->getConditionBySlug($queryCondition['ruleSlug']);
            if ($condition && array_key_exists('queryCallback', $condition) && is_callable($condition['queryCallback'])) {
                $conditionArguments = $condition['queryCallback']($conditionArguments, $queryCondition['operand'], $queryCondition['value']);
            }
        }

        $order = getOrder($wpCustomQuery);

        $customQuery = array_merge($extraArgsToMerge, $queryArguments, $conditionArguments, $order);
        /** @var array  $customQuery */
        $customQuery = apply_filters('breakdance_query_control_query', $customQuery);

        return $customQuery;

    }

    /** @psalm-suppress RedundantConditionGivenDocblockType
     *I know it's redundant but I like the explicitness, Psalm
     */
    if ($activeQuery === "custom" && $wpCustomQuery['source'] === 'related'){
        $post = get_post();

        if (!$post){
            /** @var array $noPostArgs */
            $noPostArgs =  apply_filters('breakdance_query_control_query', $extraArgsToMerge);

            return $noPostArgs;
        }

        /** @var \WP_Post $post */
        $post = $post;

        $id = $post->ID;

        $queryArguments = [
            'post_type' => get_post_type($id),
            'post_status' => 'publish',
            // always ignore for related posts
            'ignore_sticky_posts' => true,
            'date_query' => getDateQuery(
                $wpCustomQuery['date'],
                $wpCustomQuery['beforeDate'],
                $wpCustomQuery['afterDate']
            ),
            'tax_query' => getTermsQuery($wpCustomQuery['includeByTaxonomies'], $id),
            'author' => $wpCustomQuery['includeByAuthor'] ? $post->post_author : null,
            'post__not_in' => [$id],
            'posts_per_page' => $wpCustomQuery['postsPerPage'] ?? 3,
            // ignore paged even if it's sent by $extraArgsToMerge, it makes no sense for "related" posts
            'paged' => null,
        ];

        $order = getOrder($wpCustomQuery);

        $relatedQuery = array_merge($extraArgsToMerge, $queryArguments, $order);
        /** @var array $relatedQuery */
        $relatedQuery = apply_filters('breakdance_query_control_query', $relatedQuery);

        return $relatedQuery;
    }

    /** @psalm-suppress RedundantConditionGivenDocblockType
     *I know it's redundant but I like the explicitness, Psalm
     */
    if ($activeQuery === "custom" && $wpCustomQuery['source'] === 'acf_relationship' && function_exists('get_field')){
        $fieldName = $wpCustomQuery['acfField'];
        /** @var int[]|\WP_Post[] $field */
        $field = get_field($fieldName);
        if (is_array($field)) {
            $relatedIds = array_map(static function($related) {
                if ($related instanceof \WP_Post) {
                    return $related->ID;
                }
                return $related;
            }, $field);
        } else {
            // WordPress will return all posts if post__in is an empty array,
            // which would be unexpected here, so pass it an id of 0 to
            // ensure it will return an empty result set
            $relatedIds = [0];
        }

        $queryArguments = [
            'post__in' => $relatedIds,
            'post_type' => 'any',
            'post_status' => 'publish',
            'orderby' => $wpCustomQuery['orderBy'],
            'order' => $wpCustomQuery['order'],
            // always ignore for related posts
            'ignore_sticky_posts' => true,
            'date_query' => getDateQuery(
                $wpCustomQuery['date'],
                $wpCustomQuery['beforeDate'],
                $wpCustomQuery['afterDate']
            ),
            'posts_per_page' => $wpCustomQuery['postsPerPage'] ?? 3,
            // ignore paged even if it's sent by $extraArgsToMerge, it makes no sense for "related" posts
            'paged' => null,
        ];

        $acfRelationshipQuery = array_merge($extraArgsToMerge, $queryArguments);
        /** @var array $acfRelationshipQuery */
        $acfRelationshipQuery = apply_filters('breakdance_query_control_query', $acfRelationshipQuery);

        return $acfRelationshipQuery;
    }

    /** @var array $defaultQuery */
    $defaultQuery = apply_filters('breakdance_query_control_query', $extraArgsToMerge);

    return $defaultQuery;
}
/**
 * @param CustomQuery $wpCustomQuery
 * @return array{orderby: string, order: string, meta_key?: string}
 */
function getOrder($wpCustomQuery)
{
    $order = [
        'orderby' => $wpCustomQuery['orderBy'],
        'order' => $wpCustomQuery['order'],
    ];

    if ($wpCustomQuery['orderBy'] === 'acf_field' && $wpCustomQuery['acfField']) {
        $order['orderby'] = 'meta_value';
        $order['meta_key'] = $wpCustomQuery['acfField'];
    }

    if ($wpCustomQuery['orderBy'] === 'metabox_field' && $wpCustomQuery['metaboxField']) {
        $order['orderby'] = 'meta_value';
        $order['meta_key'] = $wpCustomQuery['metaboxField'];
    }

    return $order;
}

/**
 * @param string $date
 * @param string|null $beforeDate
 * @param string|null $afterDate
 * @return array|null
 */
function getDateQuery($date, $beforeDate, $afterDate)
{
    if ($date === 'custom') {
        return [
            'before' => $beforeDate,
            'after' => $afterDate,
            'inclusive' => true
        ];
    } else if ($date !== 'all' && $date) {
        return [
            'after' => $date
        ];
    }

    return null;
}

/**
 * @param string[]|null $taxonomies
 * @param int $postId
 *
 * @return array
 */
function getTermsQuery($taxonomies, $postId){
    if (!$taxonomies){
        return [];
    }

    $termsQuery = [];
    foreach ($taxonomies as $taxonomy){
        $taxonomyTermsInPost = get_the_terms($postId, $taxonomy);

        if (is_wp_error($taxonomyTermsInPost) || !$taxonomyTermsInPost){
            continue;
        }

        /** @var \WP_Term[] $taxonomyTermsInPost */
        $taxonomyTermsInPost = $taxonomyTermsInPost;

        $termsQuery[] = [
            'terms' => array_map(fn($term) => $term->term_id, $taxonomyTermsInPost),
            'taxonomy' => $taxonomy
        ];
    }

    if (count($termsQuery) > 0) {
        return array_merge(
            ['relation' => 'OR'],
            $termsQuery
        );
    }


    return $termsQuery;
}

/**
 * The data structure we send from JS doesn't match the one WP expects, because it makes TS type much better
 *
 * Recursively flatten the "metaQueries" array of objects, and turn it into an numerical keyed object
 *
 * Using JS notation to make it simpler to see the difference
 * From: {relation: 'OR', metaQueries: [{...}, {relation: 'AND', metaQueries: [{...},{...}]}]}
 * To: {relation: 'OR', 0 => {...}, 1 => {relation: 'AND', 0 => {...}, 1 => {...}}}
 *
 * @param array{relation: 'AND'|'OR', metaQueries: mixed[]} | null $invalidWpMetaQuery
 * @return array|null
 */
function getValidWpMetaQuery($invalidWpMetaQuery){
    /**
     * @psalm-suppress RedundantConditionGivenDocblockType
     */
    if (isset($invalidWpMetaQuery) && isset($invalidWpMetaQuery["relation"]) && $invalidWpMetaQuery["relation"] && isset($invalidWpMetaQuery["metaQueries"]) && $invalidWpMetaQuery["metaQueries"]) {
        return [
            "relation" => $invalidWpMetaQuery["relation"],
            ...array_map(
                function ($query) {
                    /**
                     * @psalm-suppress MixedArgument
                     */
                    return getValidWpMetaQuery($query);
                },
                $invalidWpMetaQuery["metaQueries"]
            )
        ];
    } else {
        return $invalidWpMetaQuery;
    }
}

/**
 * Returns the posts per page, or 0 if we've reached totalPosts
 * By returning 0 postsPerPage, we artificially limit the total posts available, since WP_Query doesn't have a better way
 * @param int $postsPerPage
 * @param int|null $totalPosts
 * @return int
 */
function getPostsPerPage($postsPerPage, $totalPosts){
    $paged = getPage();

    if ($totalPosts && $totalPosts > 0 && $paged){
        /** @var int $paged */
        $paged = $paged;
        $currentTotalPosts = $postsPerPage * $paged;

        if ($currentTotalPosts >= $totalPosts){
            // we can have reached the end but still need to show some posts
            // e.g: totalPosts = 20. postsPerPage = 6. On the 4th page we'll have currentTotal = 24.
            // We still need to show 2 posts to reach totalPosts (we've shown 18 so far)
            $postsShownUntilNow = ($postsPerPage * ($paged - 1));
            $postsStillToShow = $totalPosts - $postsShownUntilNow;

            return $postsStillToShow > 0 ? $postsStillToShow : 0;
        }
    }

    return $postsPerPage;
}

/**
 * global $paged doesn't work in homepage for custom queries (like adding using a Post List)
 * Try to get the paged query in every way possible.
 *
 * @return int
 */
function getPage(){
    global $paged;

    if (is_int($paged)) return $paged;

    /** @var int|null $page */
    $page = get_query_var('page');

    if (is_int($page)) return $page;

    /** @var int|null $pagedFromQuery */
    $pagedFromQuery = get_query_var('paged');

    if (is_int($pagedFromQuery)) return $pagedFromQuery;

    return 0;
}
