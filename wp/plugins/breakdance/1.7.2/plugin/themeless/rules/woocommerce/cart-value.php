<?php

namespace Breakdance\Themeless\Rules;

\Breakdance\Themeless\registerCondition(
    [
        'supports' => ['element_display'],
        'availableForType' => ['ALL'],
        'slug' => 'woocommerce-cart-value',
        'label' => 'Cart Value (' . html_entity_decode(get_woocommerce_currency_symbol()) . ')',
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
                $cartValue = (float) WC()->cart->get_total('float');
                $totalValue = (float) $value;
                if ($operand === OPERAND_IS) {
                    return $cartValue === $totalValue;
                }
                if ($operand === OPERAND_IS_NOT) {
                    return $cartValue !== $totalValue;
                }
                if ($operand === OPERAND_GREATER_THAN) {
                    return $cartValue > $totalValue;
                }
                if ($operand === OPERAND_LESS_THAN) {
                    return $cartValue < $totalValue;
                }
                return false;
            },
        'templatePreviewableItems' => false,
    ]
);
