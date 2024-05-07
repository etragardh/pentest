<?php

namespace Breakdance\Themeless\Rules;

use Breakdance\Themeless\SearchContext;
use function Breakdance\Util\WP\get_terms_in_taxonomy;

if (function_exists('wc_get_attribute_taxonomies')) {
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-attributes',
            'label' => 'Attributes',
            'category' => 'Product',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF, OPERAND_ALL_OF],
            'values' => function () {
                
                /** @var object{attribute_name: string}[] $attributes */
                $attributes = wc_get_attribute_taxonomies();

                $items = [];

                foreach ($attributes as $attribute) {
                    $terms = get_terms_in_taxonomy('pa_'.$attribute->attribute_name, [], SearchContext::getInstance()->search);
                    foreach ($terms as $term) {
                        $items[] = ['text' => $term->name, 'value' => (string) $term->term_id];
                    }
                }

                return [[
                    'label' => 'Attributes',
                    'items' => $items
                ]];

            },
            'callback' => /**
             * @param string $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    $results = array_map(static function($termId) {
                        /** @var \WP_term $term */
                        $term = get_term((int) $termId);
                        if (is_wp_error($term)) {
                            return false;
                        }
                        return has_term($termId, $term->taxonomy);
                    }, $value);
                    if ($operand === OPERAND_ONE_OF) {
                        return in_array(true, $results);
                    }
                    if ($operand === OPERAND_NONE_OF) {
                        return !in_array(true, $results);
                    }
                    if ($operand === OPERAND_ALL_OF) {
                        return !in_array(false, $results);
                    }
                    return false;
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param mixed $value
             * @return WordPressQueryVars
             */
            function ($query, $operand, $value) {
                if (!is_array($value)) {
                    return $query;
                }
                $taxonomies = [];
                /** @var array{value: string} $selected */
                foreach ($value as $selected) {
                    $termId = (int) $selected['value'];
                    if (!$termId) {
                        continue;
                    }
                    // selected value is a term ID which needs to be mapped
                    // to it's taxonomy slug for the tax_query
                    /** @var \WP_Term $term */
                    $term = get_term((int) $termId);
                    if (is_wp_error($term)) {
                        continue;
                    }
                    $taxonomies[$term->taxonomy][] = $term->term_id;
                }
                $taxQuery = $query['tax_query'] ?? [];
                foreach ($taxonomies as $taxonomy => $terms) {
                    $compare = operandToQueryCompare($operand);
                    $taxQuery[] = [
                        'operator' => $compare,
                        'taxonomy' => $taxonomy,
                        'terms' => $terms
                    ];
                }

                if (!empty($taxQuery)) {
                    /** @var array<array-key, WordPressTaxQuery|string> $taxQuery */
                    $query['tax_query'] = $taxQuery;
                }

                return $query;
            },
        ]
    );
}
