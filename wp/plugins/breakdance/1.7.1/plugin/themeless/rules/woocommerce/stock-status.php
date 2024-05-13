<?php

namespace Breakdance\Themeless\Rules;

if (function_exists('wc_get_product_stock_status_options')) {
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'query_builder'],
            'availableForType' => getProductConditionPostTypes(),
            'slug' => 'woocommerce-stock-status',
            'label' => 'Stock status',
            'category' => 'Inventory',
            'operands' => [OPERAND_ONE_OF, OPERAND_NONE_OF],
            'values' => function () {
                /** @var array<string,string> $statusOptions */
                $statusOptions = wc_get_product_stock_status_options();
                return [[
                    'label' => 'Status',
                    'items' => array_map(static function ($optionKey, $option) {
                        return ['text' => $option, 'value' => $optionKey];
                    }, array_keys($statusOptions), $statusOptions),
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
                    return in_array($product->get_stock_status(), $value);
                },
            'templatePreviewableItems' => false,
            'queryCallback' => /**
             * @param WordPressQueryVars $query
             * @param string $operand
             * @param DropdownData[] $value
             * @return WordPressQueryVars
             */
                function ($query, $operand, $value) {
                    $compare = operandToQueryCompare($operand);

                    if (!$value) {
                        return $query;
                    }
                    $query['meta_query'][] = [
                        'compare' => $compare,
                        'key' => '_stock_status',
                        'value' => array_map(static function ($selectedStatus) {
                            return $selectedStatus['value'];
                        }, $value),
                    ];
                    return $query;
                },
        ]
    );
}
