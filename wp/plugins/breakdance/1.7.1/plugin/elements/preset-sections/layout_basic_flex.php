<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\layout_basic_flex",
    c(
        "layout_basic_flex",
        "Layout (Basic Flex)",
        [
            c(
                "stack",
                "Stack",
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'items' => [
                        ['text' => 'Vertical', 'label' => 'Label', 'value' => 'vertical'],
                        ['text' => 'Horizontal', 'value' => 'horizontal']
                    ]
                ],
                true,
                false
            ),
            c(
                "alignment",
                "Alignment",
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'not equals',
                        'value' => 'horizontal'
                    ],
                    'items' => [
                        ['text' => 'Left', 'label' => 'Label', 'value' => 'left'],
                        ['text' => 'Center', 'value' => 'center'],
                        ['text' => 'Right', 'value' => 'right']
                    ]
                ],
                true,
                false
            ),
            c(
                "h_h_alignment",
                "H Alignment",
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'equals',
                        'value' => 'horizontal'
                    ],
                    'items' => [
                        ['text' => 'Left', 'label' => 'Label', 'value' => 'left'],
                        ['text' => 'Center', 'value' => 'center'],
                        ['text' => 'Right', 'value' => 'right'],
                        ['text' => 'Space Around', 'value' => 'space_around'],
                        ['text' => 'Space Between', 'value' => 'space_between']
                    ]
                ],
                true,
                false
            ),
            c(
                "h_v_alignment",
                "V Alignment",
                [],
                [
                    'type' => 'dropdown',
                    'layout' => 'inline',
                    'items' => [
                        ['text' => 'Top', 'label' => 'Label', 'value' => 'top'],
                        ['text' => 'Middle', 'value' => 'middle'],
                        ['text' => 'Bottom', 'value' => 'bottom']
                    ],
                    'condition' => [
                        'path' => '%%CURRENTPATH%%.stack',
                        'operand' => 'equals',
                        'value' => 'horizontal']
                    ],
                true,
                false
            )
        ],
        [
            'type' => 'section',
        ],
        false,
        false
    ),
    true
);
