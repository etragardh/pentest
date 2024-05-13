<?php

namespace Breakdance\Themeless\Rules;

/**
 * @param string $operand
 * @param string $default
 * @return string
 */
function operandToQueryCompare($operand, $default = '==') {
    switch ($operand) {
        case OPERAND_IS:
            $compare = '==';
            break;
        case OPERAND_IS_NOT:
            $compare = '!=';
            break;
        case OPERAND_ONE_OF:
            $compare = 'IN';
            break;
        case OPERAND_NONE_OF:
            $compare = 'NOT IN';
            break;
        case OPERAND_ALL_OF:
            $compare = 'AND';
            break;
        case OPERAND_GREATER_THAN:
            $compare = '>';
            break;
        case OPERAND_LESS_THAN:
            $compare = '<';
            break;
        default:
            $compare = $default;
    }
    return $compare;
}
