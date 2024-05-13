<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-price',
        'label' => 'Price (' . html_entity_decode(get_woocommerce_currency_symbol()) . ')',
        'category' => 'Price',
        'operands' => [OPERAND_IS, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param int|float $value
         * @return bool
         */
        function ($operand, $value): bool {
            global $product;
            if (!$product) {
                return false;
            }
            /** @var \WC_Product $product */
            $product = $product;
            $productPrice = round((float) $product->get_price(), 2);
            $conditionPrice = round((float) $value, 2);
            if ($operand === OPERAND_GREATER_THAN) {
                return $productPrice > $conditionPrice;
            }
            if ($operand === OPERAND_LESS_THAN) {
                return $productPrice < $conditionPrice;
            }
            if ($operand === OPERAND_IS) {
                return $productPrice === $conditionPrice;
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
                    'key' => '_price',
                    'type' => 'numeric',
                    'value' => $value
                ];
                return $query;
            },
    ]
);
