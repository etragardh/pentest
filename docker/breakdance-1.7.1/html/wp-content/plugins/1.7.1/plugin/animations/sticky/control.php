<?php

namespace Breakdance\Animations\Sticky;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control
 */
function controls()
{
    /** @var Control */
    return controlSection(
        'sticky',
        'Sticky',
        [
            control('position', 'Position', [
                'type' => 'dropdown',
                'layout' => 'inline',
                'items' => [
                    ['text' => 'Top', 'value' => 'top'],
                    ['text' => 'Center', 'value' => 'center'],
                    ['text' => 'Bottom', 'value' => 'bottom'],
                ],
            ]),
            control('offset', 'Offset', [
                'type' => 'unit',
                'layout' => 'inline',
                'multiple' => true,
                'unitOptions' => ['types' => ['px']],
                'rangeOptions' => ['min' => 0, 'max' => 100, 'step' => 1],
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ], true),
            control('relative_to', 'Relative To', [
                'type' => 'button_bar',
                'layout' => 'vertical',
                'items' => [
                    ['text' => 'Parent', 'value' => 'parent'],
                    ['text' => 'Viewport', 'value' => 'viewport'],
                    ['text' => 'Custom', 'value' => 'custom'],
                ],
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ]),
            control('relative_selector', 'Custom Selector', [
                'type' => 'text',
                'layout' => 'vertical',
                'condition' => ['path' => '%%CURRENTPATH%%.relative_to', 'operand' => 'equals', 'value' => 'custom'],
            ]),
            control('disable_at', 'Disable At', [
                'type' => 'breakpoint_dropdown',
                'layout' => 'vertical',
                'condition' => ['path' => '%%CURRENTPATH%%.position', 'operand' => 'is set'],
            ]),
        ],
        ['isExternal' => true],
        'popout'
    );
}
