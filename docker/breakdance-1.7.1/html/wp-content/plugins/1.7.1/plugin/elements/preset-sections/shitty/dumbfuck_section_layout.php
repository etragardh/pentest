<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

add_action('init', function() {
    PresetSectionsController::getInstance()->register(
        "EssentialElements\\dumbfuck_section_layout",
        controlSection('dumbfuck_section_layout', 'Layout', [
            responsiveControl("stack_content", "Stack Content", [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => 'Vertical', 'value' => 'vertical'),
                        array('text' => 'Horizontal', 'value' => 'horizontal'),
                    ],
            ]),
            responsiveControl("alignment_for_horizontal_layout", "Alignment", [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => 'Space Around', 'value' => 'space-around'),
                        array('text' => 'Space Between', 'value' => 'space-between'),
                    ],
                'condition' => [
                    'path' => 'design.dumbfuck_section_layout.stack_content', // TODO
                    'operand' => 'equals',
                    'value' => 'horizontal',
                ],
            ]),
            responsiveControl("alignment_for_vertical_layout", "Alignment", [
                'type' => 'dropdown',
                'items' =>
                    [
                        array('text' => 'Left', 'value' => 'flex-start'),
                        array('text' => 'Center', 'value' => 'center'),
                        array('text' => 'Right', 'value' => 'flex-end'),
                    ],
                'condition' => [
                    'path' => 'design.dumbfuck_section_layout.stack_content', // TODO
                    'operand' => 'equals',
                    'value' => 'vertical', /* this control should also be visible if no choice was made */
                ],
            ]),
        ]),
    );
});
