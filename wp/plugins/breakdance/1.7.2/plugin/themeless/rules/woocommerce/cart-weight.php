<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-cart-weight',
        'label' => 'Cart Weight (' . strtolower( (string) get_option( 'woocommerce_weight_unit' ) ) . ')',
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
                $cartWeight = (int) WC()->cart->get_cart_contents_weight();
                $totalValue = (int) $value;
                if ($operand === OPERAND_IS) {
                    return $cartWeight === $totalValue;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $cartWeight !== $totalValue;
                }
                if ($operand === OPERAND_GREATER_THAN) {
                    return $cartWeight > $totalValue;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $cartWeight < $totalValue;
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);
