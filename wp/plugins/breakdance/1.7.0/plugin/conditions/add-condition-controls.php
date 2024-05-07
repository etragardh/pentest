<?php

namespace Breakdance\Conditions;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_element_controls', '\Breakdance\Conditions\addConditionControls', 69);

/**
 *
 * @param array $controls
 * @return array
 */
function addConditionControls($controls)
{

    /**
     * @psalm-suppress MixedArrayAssignment
     */
    $controls['settingsSections'][] = controlSection('conditions', 'Conditions', [
        control(
            'conditions',
            'Only Show Element If',
            [
                'type' => 'condition',
                'layout' => 'vertical'
            ]
        ),
        control(
            'builder_preview',
            'In-Builder Preview',
            [
                'type' => 'dropdown', 'items' => [
                    ['text' => 'Always Show', 'value' => 'show'],
                    ['text' => 'Always Hide', 'value' => 'hide'],
                ],
                'layout' => 'vertical'
            ]
        ),
    ]);

    return $controls;
}
