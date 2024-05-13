<?php

namespace Breakdance\Conditions;

/**
 * @return TemplateConditionWithValues[]
 */
function get_conditions_for_builder()
{
    do_action('breakdance_register_template_types_and_conditions');

    $conditions = \Breakdance\Themeless\getConditionsWithValues();

    return array_values(array_filter($conditions, function ($condition) {
        return in_array('element_display', $condition['supports']);
    }));
}
