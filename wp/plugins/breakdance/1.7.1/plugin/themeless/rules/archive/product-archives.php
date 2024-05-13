<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;
use function Breakdance\Themeless\get_post_type_archives_as_template_previewable_items;
use function Breakdance\Util\WP\get_terms_in_taxonomy;
use function Breakdance\Util\WP\term_to_simplified_term;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerWooProductArchiveTemplates'
);

function registerWooProductArchiveTemplates()
{
    if (! class_exists('WooCommerce')) {
        return;
    }

    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'all-product-archives',
            'label' => 'All Product Archives',
            'callback' => function (): bool {
                // if you change this, change the logic in the swaggy defaults
                return is_shop() || is_product_taxonomy();
            },
            'templatePreviewableItems' =>
                function () {
                    $productArchive = get_post_type_archives_as_template_previewable_items(['product']);
                    $catAndTagArchives = getWooCategoryAndTagTermsPreviewableItems();

                    return array_merge($productArchive, $catAndTagArchives);
                },
            'defaultPriority' => TEMPLATE_PRIORITY_ALL_ARCHIVE_OR_ALL_SINGLE,
        ]
    );

    \Breakdance\Themeless\registerTemplateType(
        'Archive',
        [
            'slug' => 'specific-product-archive',
            'label' => 'Specific Product Archive',
            'callback' =>
                function () {
                    return is_product_taxonomy();
                },
            'templatePreviewableItems' =>
                function () {
                    return getWooCategoryAndTagTermsPreviewableItems();
                },
            'defaultPriority' => TEMPLATE_PRIORITY_SPECIFIC_ARCHIVE,
        ]
    );

    $taxonomyAvailableForType = ['specific-product-archive'];
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => $taxonomyAvailableForType,
            'slug' => 'product-is-taxonomy',
            'label' => 'Is Taxonomy',
            'category' => 'Taxonomy',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT],
            'values' => function () use ($taxonomyAvailableForType){
                return getProductTaxonomiesForMultiselect($taxonomyAvailableForType);
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
 * @return TemplatePreviewableItem[]
 */
function getWooCategoryAndTagTermsPreviewableItems()
{
    $categories = get_terms_in_taxonomy('product_cat');
    $tags = get_terms_in_taxonomy('product_tag');
    $terms = array_merge($categories, $tags);

    $previewableItems = [];

    foreach ($terms as $term) {
        $termLink = get_term_link($term);

        if (is_string($termLink)) {
            $previewableItems[] = [
                'url' => $termLink,
                'label' => $term->name,
                'type' => 'Product term',
            ];
        }
    }

    return $previewableItems;
}

/**
 * @param string[] $availableForTypes
 * @return ItemGroup[]
 */
function getProductTaxonomiesForMultiselect($availableForTypes = [])
{
    $productCategory = get_taxonomy('product_cat');
    $productTag = get_taxonomy('product_tag');

    $allTerms = [];

    foreach ([$productCategory, $productTag] as $taxonomy) {
        if (! is_object($taxonomy)) {
            break;
        }

        $terms = \Breakdance\Util\WP\get_terms_in_taxonomy(
            $taxonomy->name,
            [],
            SearchContext::getInstance()->search
        );

        $items = array_map(
            function ($term) {
                return [
                    'text' => $term->name ?: $term->slug,
                    'value' => json_encode(term_to_simplified_term($term)),
                ];
            },
            $terms);

        $allTerms[] = [
            'label' => $taxonomy->label,
            'items' => $items,
            'availableForType' => $availableForTypes
        ];
    }

    return $allTerms;
}
