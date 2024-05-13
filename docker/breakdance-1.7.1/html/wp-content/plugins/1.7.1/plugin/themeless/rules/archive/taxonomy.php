<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;

use function Breakdance\Themeless\get_all_terms_as_template_previewable_items;
use function Breakdance\Themeless\get_all_terms_excluding_list_of_taxonomies;
use function Breakdance\Themeless\get_all_terms_in_list_of_taxonomies;
use function Breakdance\Util\WP\term_to_simplified_term;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerArchiveTaxonomyRules'
);

function registerArchiveTaxonomyRules()
{
    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'taxonomy-archive',
            'label' => 'Taxonomy Archive',
            'callback' => function (): bool {
                /* is_tax returns false for built-in taxonomies (categories and tags) */
                return is_tax() || is_category() || is_tag();
            },
            'templatePreviewableItems' =>
                function () {
                    return get_all_terms_as_template_previewable_items();
                },
            'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE,
        ]
    );


    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['taxonomy-archive', 'all-archives', 'specific-product-archive'],
            'slug' => 'taxonomy',
            'label' => 'Is Taxonomy',
            'category' => 'Taxonomy',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () {
                return getTaxonomiesForMultiselect(
                /**
                 * @param \WP_Term $term
                 */
                    function ($term) {
                        return [
                            'text' => $term->name ? $term->name : $term->slug,
                            'value' => json_encode(term_to_simplified_term($term)),
                        ];
                    },
                    [],
                    ['taxonomy-archive', 'all-archives']
                );
            },
            'callback' =>
                /**
                 * @param string $operand
                 * @param string[] $value
                 * @return bool
                 */
                function($operand, $value){
                return isTaxonomyConditionCallback($operand, $value);
            },
            'templatePreviewableItems' =>
            /**
             * @param string $operand
             * @param string[] $value
             * @return TemplatePreviewableItem[]
             */
            function($operand, $value){
                return isTaxonomyConditionTemplatePreviewableItems($operand, $value);
            },
        ]
    );
}

/**
 * @param Closure(\WP_Term, \WP_Taxonomy):TemplateConditionValue $create_item_callback
 * @param string[]|null $availableForTypes
 * @param string[] $alwaysAvailablePostTypes
 * @return ItemGroup[]
 */
function getTaxonomiesForMultiselect($create_item_callback, $availableForTypes = null, $alwaysAvailablePostTypes = [])
{
    $allTaxonomies = \Breakdance\Util\WP\get_all_taxonomies();
    $allTerms      = [];
    $search        = SearchContext::getInstance()->search;


    foreach ($allTaxonomies as $taxonomy) {
        if (! is_object($taxonomy)) {
            break;
        }

        // Limit the amount of terms we get by default when not searching.
        if (
            ! $search
            && count($allTerms) > 0
            && count(array_merge(...$allTerms)) >= TEMPLATE_POSTS_LIMIT
        ) {
            break;
        }

        $terms
            = \Breakdance\Util\WP\get_terms_in_taxonomy(
                $taxonomy->name,
                ['hide_empty' => false],
                $search
            );

        $items = array_map(function ($term) use (
            $taxonomy,
            $create_item_callback
        ) {
            return $create_item_callback($term, $taxonomy);
        }, $terms);

        // Add "all in X taxonomy" as the first item
        array_unshift($items, [
            'text' => 'All ' . $taxonomy->name,
            'value' => json_encode(['allInTax' => $taxonomy->name]),
        ]);

        $availablePostTypesForThisTaxonomy = [];

        // Which post types is the  taxonomy available for (object_type)?
        // Then filter if its applicable to $availableForType
        if ($availableForTypes) {
            /**
             * @var string[]
             */
            $availablePostTypesForThisTaxonomy = array_filter(
                $taxonomy->object_type,
                function ($postType) use ($availableForTypes) {
                    return in_array($postType, $availableForTypes);
                }
            );
        }

        $allTerms[] = [
            'label' => (string) $taxonomy->label,
            'items' => $items,
            'availableForType' => array_values(array_merge($availablePostTypesForThisTaxonomy, $alwaysAvailablePostTypes))
        ];
    }

    return $allTerms;
}

/**
 * @param string $operand
 * @param string[] $value
 * @return bool
 */
function isTaxonomyConditionCallback($operand, $value) {
    $results = array_map(
    /**
     * @param  string $encodedAllInTaxOrSimplifiedTerm
     * @return bool
     */
        function ($encodedAllInTaxOrSimplifiedTerm) use ($operand) {
            /**
             * @var array{taxonomySlug?:string, termId?:int, allInTax?:string}|null
             */
            $allInTaxOrSimplifiedTerm
                = json_decode(
                $encodedAllInTaxOrSimplifiedTerm,
                true
            );

            if (! is_array($allInTaxOrSimplifiedTerm)) {
                return false;
            }

            if (
                isset(
                    $allInTaxOrSimplifiedTerm['taxonomySlug'],
                    $allInTaxOrSimplifiedTerm['termId']
                )
            ) {
                $taxonomySlug
                    = $allInTaxOrSimplifiedTerm['taxonomySlug'];
                $termId
                    = $allInTaxOrSimplifiedTerm['termId'];

                $isTaxonomy =  is_category($termId)
                    || is_tag($termId)
                    || is_tax($taxonomySlug, $termId);
            } elseif (isset($allInTaxOrSimplifiedTerm['allInTax'])) {
                $isTaxonomy = is_category()
                    || is_tag()
                    || is_tax($allInTaxOrSimplifiedTerm['allInTax']);
            }

            if ($operand === OPERAND_IS && isset($isTaxonomy)) {
                return (bool) $isTaxonomy;
            } elseif ($operand === OPERAND_IS_NOT && isset($isTaxonomy)) {
                return !$isTaxonomy;
            }

            return false;
        },
        $value
    );

    return in_array(true, $results);
}


/**
 * @param string $operand
 * @param string[] $value
 * @return TemplatePreviewableItem[]
 */
function isTaxonomyConditionTemplatePreviewableItems($operand, $value) {
    if ($operand === OPERAND_IS) {
        return get_all_terms_in_list_of_taxonomies($value);
    } elseif ($operand === OPERAND_IS_NOT) {
        return get_all_terms_excluding_list_of_taxonomies($value);
    }

    return [];
}
