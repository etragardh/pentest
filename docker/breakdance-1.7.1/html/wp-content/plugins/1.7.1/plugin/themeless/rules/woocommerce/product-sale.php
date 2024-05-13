<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-sale',
        'label' => 'On Sale',
        'category' => 'Price',
        'operands' => [OPERAND_IS],
        'valueInputType' => 'dropdown',
        'values' => function () {
            return [
                [
                    'label' => 'Status',
                    'items' => [
                        ['text' => 'on sale', 'value' => 'on_sale'],
                        ['text' => 'not on sale', 'value' => 'not_on_sale']
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
                if ($value === 'on_sale') {
                    return $product->is_on_sale();
                }
                if ($value === 'not_on_sale') {
                    return !$product->is_on_sale();
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
                /** @var int[] $saleIds */
                $saleIds = wc_get_product_ids_on_sale();

                if ($value === 'not_on_sale') {
                    $postNotIn = $query['post__not_in'] ?? [];
                    $query['post__not_in'] = array_merge($postNotIn, $saleIds);
                }

                if ($value === 'on_sale') {
                    $postIn = $query['post__in'] ?? [];
                    $query['post__in'] = array_merge($postIn, $saleIds);
                }

                return $query;
            },
    ]
);
