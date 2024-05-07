<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-virtual',
        'label' => 'Virtual',
        'category' => 'Product',
        'operands' => [OPERAND_IS],
        'valueInputType' => 'dropdown',
        'values' => function () {
            return [
                [
                    'label' => 'Status',
                    'items' => [
                        ['text' => 'virtual', 'value' => 'yes'],
                        ['text' => 'not virtual', 'value' => 'no']
                    ]
                ]
            ];
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                global $product;
                if (!$product) {
                    return false;
                }
                /** @var \WC_Product $product */
                $product = $product;
                if ($value === 'yes') {
                    return $product->is_virtual();
                }
                if ($value === 'no') {
                    return !$product->is_virtual();
                }
                return false;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param string $value
         * @return WordPressQueryVars
         */
            function ($query, $operand, $value) {
                $compare = operandToQueryCompare($operand);
                $query['meta_query'][] = [
                    'compare' => $compare,
                    'key' => '_virtual',
                    'value' => $value
                ];
                return $query;
            },
    ]
);
