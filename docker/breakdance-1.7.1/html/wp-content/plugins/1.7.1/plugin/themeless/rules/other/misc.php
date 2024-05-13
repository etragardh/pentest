<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerOtherMiscRules'
);

/**
 * @return bool
 */
function isSearch(){
    // is_search doesn't work well with pagination
    // get_search_query is empty if there's no search value, but that's still "search"
    return !!get_search_query() || is_search();
}

/**
* @return string
 */
function getSearchUrl(){
    // `get_search_link` is a 404 in certain custom permalink setups. For example with /%category%/, since `/search` tries to match it to a category
    // append '?s=' so it shows search in the frontend. Does nothing in the builder
    return get_home_url() . "?s=";
}

function registerOtherMiscRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Other',
        [
            'slug' => '404',
            'label' => '404 - Not Found Error',
            'callback' => function () {
                return is_404();
            },
            'templatePreviewableItems' => function () {
                return false;
            },
        ]
    );

    \Breakdance\Themeless\registerTemplateType(
        'Other',
        [
            'slug' => 'search',
            'label' => 'Search Results',
            'callback' =>
            /**
             * @return bool
             */
                function () {
                    return isSearch();
                },
            'templatePreviewableItems' => function () {
                return [[
                    'url' => getSearchUrl(),
                    'label' => "Search",
                    'type' => 'search results',
                ]];
            },
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['search'],
            'slug' => 'search',
            'label' => 'Search',
            'category' => 'Other',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => fn() => false,
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             */
                function ($operand, $value): bool {
                    switch ($operand) {
                        case OPERAND_IS:
                            return $value === get_search_query();
                        case OPERAND_IS_NOT:
                            return $value !== get_search_query();
                        default:
                            return false;
                    }
                },
            'templatePreviewableItems' => function () {
                return [[
                    'url' => getSearchUrl(),
                    'label' => "Search",
                    'type' => 'search archive',
                ]];
            }
        ]
    );

    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['search', 'all-archives', 'specific-product-archive', 'author-archive', 'date-archive', 'post-type-archive', 'post-archives', 'all-product-archives'],
            'slug' => 'wp_query_found_posts_count',
            'label' => 'WP_Query Found Posts Count',
            'category' => 'Other',
            'operands' => [OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
            'values' => fn() => false,
            'callback' =>
            /**
             * @param string $operand
             * @param string $value
             */
                function ($operand, $value): bool {

                    global $wp_query;
                    
                    /**
                     * @psalm-suppress MixedPropertyFetch
                     * @var int
                     */
                    $found_posts = $wp_query ? @$wp_query->found_posts : 0;

                    if ($operand === OPERAND_GREATER_THAN) {
                        return $found_posts > ((int) $value);
                    } else {
                        return $found_posts < ((int) $value);
                    }

                },
            'templatePreviewableItems' => false
        ]
    );

}
