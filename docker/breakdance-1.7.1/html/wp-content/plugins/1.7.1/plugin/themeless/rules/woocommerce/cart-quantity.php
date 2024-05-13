<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-cart-quantity',
        'label' => 'Cart Quantity',
        'category' => 'Cart',
        'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_GREATER_THAN, OPERAND_LESS_THAN],
        'valueInputType' => 'number',
        'values' => function () {
            return false;
        },
        'callback' => /**
         * @param mixed $operand
         * @param string $value
         * @return bool
         */
            function ($operand, $value): bool {
                /** @var array{product_id: int}[] $cartItems */
                $cartItemQuantities = WC()->cart->get_cart_item_quantities();
                $totalQuantity = (int) array_sum($cartItemQuantities);
                $totalValue = (int) $value;
                if ($operand === OPERAND_IS) {
                    return $totalQuantity === $totalValue;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $totalQuantity !== $totalValue;
                }
                if ($operand === OPERAND_GREATER_THAN) {
                    return $totalQuantity > $totalValue;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $totalQuantity < $totalValue;
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);
