<?php

namespace Breakdance\Themeless;

use function Breakdance\Util\WP\get_author_permalink;
use function Breakdance\Util\WP\get_authors;
use function Breakdance\Util\WP\get_term_permalink;
use function Breakdance\Util\WP\get_terms_in_taxonomy;
use function Breakdance\Util\WP\term_to_simplified_term;

/**
 * @return TemplatePreviewableItem[]
 */
function get_all_archives_as_template_previewable_items()
{
    return array_merge(
        get_post_type_archives_as_template_previewable_items(),
        get_all_terms_as_template_previewable_items(),
        get_date_archives_as_template_previewable_items(),
        get_author_archives_as_template_previewable_items(),
    );
}

/**
 * @return TemplatePreviewableItem[]
 */
// How robust is this implementation?
// Here are more ideas if this blows up in the future: https://stackoverflow.com/questions/38985334/year-and-month-in-wordpress-archive-list
function get_date_archives_as_template_previewable_items()
{
    $html   = wp_get_archives(['echo' => false]);
    $search = SearchContext::getInstance()->search;

    if (! $html) {
        return [];
    }

    // https://stackoverflow.com/questions/4423272/how-to-extract-links-and-titles-from-a-html-page

    $dom = new \DOMDocument();
    // Silence operator is required because function may generate E_WARNING errors when it encounters bad markup
    @$dom->loadHTML($html);
    $links = $dom->getElementsByTagName('a');

    /** @var string[] */
    $arr = [];

    foreach ($links as $link) {
        $arr[] = $link->getAttribute('href');
    }

    return array_map(function ($archiveLink) {
        return [
            'url' => $archiveLink,
            // Would be nice to show the date here, but it seems to be a PITA to do
            'label' => $archiveLink,
            'type' => 'date archive',
        ];
    },
        filterBySearchOrReturnOriginal($arr, $search));
}


/**
 * @param \WP_User[]|false $authors
 * @return TemplatePreviewableItem[]
 */
function get_author_archives_as_template_previewable_items($authors = false)
{
    $search = SearchContext::getInstance()->search;

    return array_map(
        function ($author) {
            return [
                'url' => get_author_permalink($author->ID),
                'label' => $author->display_name,
                'type' => 'author archive',
            ];
        },
        $authors ? $authors : get_authors($search)
    );
}


/**
 * @param string[]|int[] $authorsId
 * @param string $operand
 * @return TemplatePreviewableItem[]
 */
function get_specific_author_archives_as_template_previewable_items($authorsId, $operand)
{
    $authorsIdInt = array_map(function ($str) {
        return (int)$str;
    }, $authorsId);

    $authorsWithArchives = array_filter(
        get_authors(SearchContext::getInstance()->search),
        function ($author) use ($authorsIdInt, $operand) {
            $authorMatches =  in_array($author->ID, $authorsIdInt, false);

            switch ($operand) {
                case OPERAND_IS:
                    return $authorMatches;
                case OPERAND_IS_NOT:
                    return !$authorMatches;
                default:
                    return false;
            }
        }
    );

    return get_author_archives_as_template_previewable_items($authorsWithArchives);
}

/**
 * @param string[] $post_type_slugs
 * @return TemplatePreviewableItem[]
 */
function get_post_type_archives_as_template_previewable_items(
    $post_type_slugs = null
) {
    $post_types = $post_type_slugs ?: \Breakdance\Util\get_post_types_with_archives(SearchContext::getInstance()->search);

    $previewableItems = [];
    foreach ($post_types as $postType) {
        $permalink = get_post_type_archive_link($postType);

        if ($permalink) {
            $previewableItems[] = [
                'url' => $permalink,
                'label' => $postType,
                'type' => $postType . ' archive',
            ];
        }
    }

    return $previewableItems;
}

/**
 * @return TemplatePreviewableItem[]
 */
function get_all_terms_as_template_previewable_items()
{
    /** @var array{taxonomySlug:string,termId:int}[] $simplified_terms */
    $simplified_terms = array_map(function ($term) {
        return term_to_simplified_term($term);
    }, performant_get_all_terms(SearchContext::getInstance()->search));

    $termPermalinks = [];
    foreach ($simplified_terms as $simplifiedTerm) {
        $permalink = get_term_permalink($simplifiedTerm['termId']);
        /** @var \WP_Term */
        $term = get_term($simplifiedTerm['termId']);

        if (is_wp_error($term)) {
            continue;
        }

        if (is_string($permalink)) {
            $termPermalinks[] = [
                'url' => $permalink,
                'label' => $term->name,
                'type' => $term->taxonomy . ' archive',
            ];
        }
    }

    return $termPermalinks;
}

/**
 * @param string|false $searchTerm
 * @return \WP_Term[]
 */
function performant_get_all_terms($searchTerm)
{
    $allTaxonomies = \Breakdance\Util\WP\get_all_taxonomies();
    $allTerms      = [];

    foreach ($allTaxonomies as $taxonomy) {
        if (! is_object($taxonomy)) {
            break;
        }

        // Limit the amount of terms we get by default when not searching.
        if (
            ! $searchTerm
             && count($allTerms) > 0
             && count(array_merge(...$allTerms)) >= TEMPLATE_POSTS_LIMIT
        ) {
            break;
        }

        $allTerms[]
            = \Breakdance\Util\WP\get_terms_in_taxonomy(
                $taxonomy->name,
                [],
                $searchTerm
            );
    }

    // we're pushing a set of arrays in an array, so spread them into a single array.
    return array_merge(...$allTerms);
}

/**
 * @param string[] $encodedTaxonomies
 * @return TemplatePreviewableItem[]
 */
function get_all_terms_in_list_of_taxonomies($encodedTaxonomies)
{
    /**
     * @var TemplatePreviewableItem[]
     */
    $previewableItems = [];

    foreach ($encodedTaxonomies as $encodedAllInTaxOrSimplifiedTerm) {
        /**
         * @var array{taxonomySlug?:string, termId?:int, allInTax?:string}|null
         */
        $allInTaxOrSimplifiedTerm
            = json_decode(
                $encodedAllInTaxOrSimplifiedTerm,
                true
            );

        if (! is_array($allInTaxOrSimplifiedTerm)) {
            continue;
        }

        if (isset($allInTaxOrSimplifiedTerm['termId'])) {
            $termPermalink
                = get_term_permalink((int)$allInTaxOrSimplifiedTerm['termId']);
            /** @var \WP_Term */
            $term
                = get_term($allInTaxOrSimplifiedTerm['termId']);

            if (
                is_string($termPermalink)
                && ! is_wp_error($term)
            ) {
                $previewableItems[] = [
                    'url' => $termPermalink,
                    'label' => $term->name,
                    'type' => 'taxonomy archive',
                ];
            }
        } elseif (
            isset($allInTaxOrSimplifiedTerm['allInTax'])
        ) {
            $termsInTaxonomy
                = get_terms_in_taxonomy(
                    $allInTaxOrSimplifiedTerm['allInTax'],
                    [],
                    SearchContext::getInstance()->search
                );

            foreach ($termsInTaxonomy as $term) {
                $termPermalink
                    = get_term_permalink($term->term_id);

                if (is_string($termPermalink)) {
                    $previewableItems[] = [
                        'url' => $termPermalink,
                        'label' => $term->name,
                        'type' => 'taxonomy archive',
                    ];
                    ;
                }
            }
        }
    }

    return $previewableItems;
}

/**
 * @param string[] $encodedTaxonomies
 * @return TemplatePreviewableItem[]
 */
function get_all_terms_excluding_list_of_taxonomies($encodedTaxonomies)
{
    $allPreviewableItemTerms = get_all_terms_as_template_previewable_items();

    foreach ($encodedTaxonomies as $encodedAllInTaxOrSimplifiedTerm) {
        /**
         * @var array{taxonomySlug?:string, termId?:int, allInTax?:string}|null
         */
        $allNotInTaxOrSimplifiedTerm
            = json_decode(
                $encodedAllInTaxOrSimplifiedTerm,
                true
            );

        if (! is_array($allNotInTaxOrSimplifiedTerm)) {
            continue;
        }

        if (isset($allNotInTaxOrSimplifiedTerm['termId'])) {
            /** @var \WP_Term $excludedTerm */
            $excludedTerm
                = get_term($allNotInTaxOrSimplifiedTerm['termId']);

            if (!is_wp_error($excludedTerm)) {
                $allPreviewableItemTerms
                    = array_filter(
                        $allPreviewableItemTerms,
                        function ($previewableItem) use ($excludedTerm) {
                            return $previewableItem['label']
                                   !== $excludedTerm->name;
                        }
                    );
            }
        } elseif (
            isset($allNotInTaxOrSimplifiedTerm['allInTax'])
        ) {
            $allPreviewableItemTerms = array_filter(
                $allPreviewableItemTerms,
                function ($previewableItem) use ($allNotInTaxOrSimplifiedTerm) {
                    return $previewableItem['type']
                           !== $allNotInTaxOrSimplifiedTerm['allInTax'];
                }
            );
        }
    }

    return $allPreviewableItemTerms;
}
