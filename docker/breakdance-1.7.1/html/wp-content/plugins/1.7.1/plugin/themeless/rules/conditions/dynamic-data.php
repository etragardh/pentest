<?php

namespace Breakdance\Themeless\Rules;

use function Breakdance\DynamicData\breakdanceDoShortcode;

add_action(
    'breakdance_register_template_types_and_conditions',
    '\Breakdance\Themeless\Rules\registerConditionsDynamicData'
);

// Dynamic Data
function registerConditionsDynamicData()
{
    \Breakdance\Themeless\registerCondition(
        [
            'supports' => ['element_display', 'templating'],
            'availableForType' => ['ALL'],
            'slug' => 'dynamic-data',
            'label' => 'Dynamic Data',
            'category' => 'Other',
            'operands' => [OPERAND_IS, OPERAND_IS_NOT, OPERAND_CONTAINS, OPERAND_NOT_CONTAIN, OPERAND_IS_EMPTY, OPERAND_IS_NOT_EMPTY],
            'values' => function () {
                return false;
            },
            'callback' =>
            /**
             * @param string $operand
             * @param string|null $value
             * @param string|null $ruleDynamic
             *
             * @return bool
             */
                function ($operand, $value, $ruleDynamic = null) {
                if (!$ruleDynamic) {
                    return false;
                }

                $fieldData = \Breakdance\DynamicData\getFieldDataFromShortcode($ruleDynamic);

                /** @var mixed $rule */
                $rule = breakdanceDoShortcode($ruleDynamic);

                if ($value && strpos($value, '[breakdance_dynamic') !== false) {
                    /** @var mixed $value */
                    $value = breakdanceDoShortcode($value);
                }

                switch ($operand) {
                    case OPERAND_IS:
                        return $rule == $value;
                    case OPERAND_IS_NOT:
                        return $rule != $value;
                    case OPERAND_CONTAINS:
                        return is_string($rule) && strpos($rule, (string)$value) !== false;
                    case OPERAND_NOT_CONTAIN:
                        return is_string($rule) && strpos($rule, (string)$value) === false;
                    case OPERAND_IS_EMPTY:
                        return !$fieldData || !$fieldData->hasValue();
                    case OPERAND_IS_NOT_EMPTY:
                        return $fieldData && $fieldData->hasValue();
                    default:
                        return false;
                }
            },
            'templatePreviewableItems' => false,
        ]
    );
}
