<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display', 'query_builder'],
        'availableForType' => getProductConditionPostTypes(),
        'slug' => 'woocommerce-product-cart',
        'label' => 'Cart',
        'category' => 'Product',
        'operands' => [OPERAND_IS],
        'valueInputType' => 'dropdown',
        'values' => function () {
            return [
                [
                    'label' => 'Status',
                    'items' => [
                        ['text' => 'in cart', 'value' => 'in'],
                        ['text' => 'not in cart', 'value' => 'not_in']
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
                $productId = get_the_ID();
                if (!$productId) {
                    return false;
                }
                /** @var array{product_id: int}[] $cartItems */
                $cartItems = WC()->cart->get_cart();
                $cartIds = array_map(static function($cartItem) {
                    return $cartItem['product_id'];
                }, $cartItems);
                $productIsInCart = in_array($productId, $cartIds);
                if ($value === 'not_in') {
                    return !$productIsInCart;
                }
                return $productIsInCart;
            },
        'templatePreviewableItems' => false,
        'queryCallback' => /**
         * @param WordPressQueryVars $query
         * @param string $operand
         * @param mixed $value
         * @return WordPressQueryVars
         */
        function ($query, $operand, $value) {
            /** @var array{product_id: int}[] $cartItems */
            $cartItems = WC()->cart->get_cart();
            $cartIds = array_values(array_map(static function($cartItem) {
                return $cartItem['product_id'];
            }, $cartItems));
            if ($value === 'not_in') {
                $postNotIn = $query['post__not_in'] ?? [];
                $query['post__not_in'] = array_merge($postNotIn, $cartIds);
            } else {
                $postIn = $query['post__in'] ?? [];
                $query['post__in'] = array_merge($postIn, $cartIds);
            }

            return $query;
        },
    ]
);
