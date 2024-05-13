<?php

namespace Breakdance\Util\WP;

/**
 * @param \WP_Term $term
 * @return array{taxonomySlug:string,termId:int}
 */
function term_to_simplified_term($term)
{
    return [
        'taxonomySlug' => $term->taxonomy,
        'termId' => $term->term_id,
    ];
}


/** @return list<\WP_Taxonomy|string> */
function get_all_taxonomies()
{
    return array_values(get_taxonomies([], 'objects'));
}

/**
 * @param string $taxonomyName
 * @param array $args
 * @param string|false $searchTerm
 * @return \WP_Term[]
 */
function get_terms_in_taxonomy($taxonomyName, $args = [], $searchTerm = false)
{
    /**
     * @psalm-suppress PossiblyInvalidArgument
     * @var array
     */
    $terms = safe_get_terms(
        array_merge(
            ['taxonomy' => $taxonomyName],
            $searchTerm
                ? ['name__like' => $searchTerm,]
                : ['number' => TEMPLATE_POSTS_LIMIT],
            $args,
            ['hide_empty' => false]
        ));

    /**
     * @var \WP_Term[]
     * Get an indexed array
     */
    $terms = array_values($terms);


    if ( ! $terms) {
        return [];
    }

    return $terms;
}


/**
 * @param int $termId
 * @return \WP_Error|string
 */
function get_term_permalink($termId)
{
    return get_term_link($termId);
}

/**
 * @param array|string $args
 * @param array|string $deprecated
 * @return \WP_Term[]|int[]|string[]|string
 */
function safe_get_terms( $args = array(), $deprecated = '' ){
    $terms = get_terms($args, $deprecated);

    if (is_wp_error($terms)) {
        return [];
    }

    /** @var \WP_Term[]|int[]|string[]|string $terms */
    $terms = $terms;

    return $terms;
}
