<?php

namespace Breakdance\Themeless\Rules;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsCustomPHP'
);

// Dynamic Data
function registerConditionsCustomPHP()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'custom-php',
            'label' => 'Custom PHP',
            'category' => 'Other',
            'operands' => [],
            'values' => function () {
                return false;
            },
            'callback' => /**
             * @param mixed $operand
             * @param string $value
             * @return bool
             */
            function ($operand, $value): bool {
                if (!$value) {
                    return false;
                }

                // Will throw if the code is invalid.
                return !!eval($value);
            },
            'templatePreviewableItems' => false,
        ]
    );
}
