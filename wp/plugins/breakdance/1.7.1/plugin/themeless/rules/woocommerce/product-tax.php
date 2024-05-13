<?php

namespace Breakdance\Themeless\Rules;

if (function_exists('wc_get_product_tax_class_options')) {
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-tax-status',
            'label' => 'Tax Status',
            'category' => 'Price',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                return [[
                    'label' => 'Status',
                    'items' => [
                        ['text' => 'Taxable', 'value' => 'taxable'],
                        ['text' => 'Shipping only', 'value' => 'shipping'],
                        ['text' => 'None', 'value' => 'none'],
                    ]
                ]];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $product;
                    if (!$product) {
                        return false;
                    }
                    /** @var \WC_Product $product */
                    $product = $product;
                    return in_array($product->get_tax_status(), $value);
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param DropdownData[]|false $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    if (!$value) {
                        return $query;
                    }
                    $compare = operandToQueryCompare($operand);
                    $query['meta_query'][] = [
                        'compare' => $compare,
                        'key' => '_tax_status',
                        'value' => array_map(static function ($option) {
                            return $option['value'];
                        }, $value),
                    ];

                    return $query;
                },
        ]
    );
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-product-tax-class',
            'label' => 'Tax Class',
            'category' => 'Price',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                /** @var array<string,string> $taxClasses */
                $taxClasses = wc_get_product_tax_class_options();
                $items = [];
                foreach ($taxClasses as $taxClassValue => $taxClassLabel) {
                    $items[] = ['text' => $taxClassLabel, 'value' => (string) $taxClassValue];
                }
                return [[
                    'label' => 'Class',
                    'items' => $items
                ]];
            },
            'callback' => /**
             * @param mixed $operand
             * @param string[] $value
             * @return bool
             */
                function ($operand, $value): bool {
                    global $product;
                    if (!$product) {
                        return false;
                    }
                    /** @var \WC_Product $product */
                    $product = $product;
                    return in_array($product->get_tax_class(), $value);
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param DropdownData[]|false $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    if (!$value) {
                        return $query;
                    }
                    $compare = operandToQueryCompare($operand);
                    $query['meta_query'][] = [
                        'compare' => $compare,
                        'key' => '_tax_class',
                        'value' => array_map(static function ($option) {
                            return $option['value'];
                        }, $value),
                    ];

                    return $query;
                },
        ]
    );
}
