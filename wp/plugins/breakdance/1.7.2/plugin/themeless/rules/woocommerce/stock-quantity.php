<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-stock-quantity',
        'label' => 'Stock quantity',
        'category' => 'Inventory',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param string $operand
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
                $stockQuantity = (int) $product->get_stock_quantity();
                $valueQuantity = (int) $value;
                if ($operand === OPERAND_GREATER_THAN) {
                    return $stockQuantity > $valueQuantity;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $stockQuantity < $valueQuantity;
                }
                if ($operand === OPERAND_IS) {
                    return $stockQuantity === $valueQuantity;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $stockQuantity !== $valueQuantity;
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
                    'key' => '_stock',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);
