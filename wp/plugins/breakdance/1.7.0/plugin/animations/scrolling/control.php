<?php

namespace Breakdance\Animations\Scrolling;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

/**
 * @return Control
 */
function controls()
{
    /** @var Control */
    return controlSection(
        'scrolling_animation',
        'Scrolling Animation',
        [
            control('enabled', 'Enabled', [
                'type' => 'toggle'
            ]),
            controlSection(
                'x',
                'Horizontal Position',
                [
                    control('start', 'Start', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'y',
                'Vertical Position',
                [
                    control('start', 'Start', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'unit',
                        'rangeOptions' => [
                            'min' => -200,
                            'max' => 200,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'opacity',
                'Opacity',
                [
                    control('start', 'Start', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'blur',
                'Blur',
                [
                    control('start', 'Start', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 10,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'rotation',
                'Rotation',
                [
                    control('start', 'Start', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => -360,
                            'max' => 360,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'scale',
                'Scale',
                [
                    control('start', 'Start', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('middle', 'Middle', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('end', 'End', [
                        'type' => 'number',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 5,
                        ],
                    ]),
                    control('trigger', 'Trigger', [
                        'type' => 'slider',
                        'layout' => 'vertical',
                        'rangeOptions' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                            'labels' => ['Bottom', 'Top']
                        ],
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            ),
            controlSection(
                'advanced',
                'Advanced',
                [
                    control('ease', 'Ease', [
                        'type' => 'dropdown',
                        'layout' => 'vertical',
                        'items' => array_map(function ($easing) {
                            return [
                                'text' => ucwords(str_replace(
                                    '.',
                                    ' ',
                                    $easing
                                )),
                                'value' => $easing,
                            ];
                        }, EASING_TYPES),
                    ]),
                    control('scrub', 'Scrub', [
                        'type' => 'unit',
                        'layout' => 'inline',
                        'unitOptions' => ['types' => ['ms', 's'], 'defaultType' => 'ms'],
                        'rangeOptions' => ['min' => 100, 'max' => 2000, 'step' => 100]
                    ]),
                    control('origin', 'Transform Origin', [
                        'type' => 'focus_point',
                        'layout' => 'vertical',
                        'focusPointOptions' => [
                            'gridMode' => true
                        ]
                    ]),
                    control('relative_to', 'Relative To', [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            ['text' => 'Viewport', 'value' => 'viewport'],
                            ['text' => 'Page', 'value' => 'page'],
                            ['text' => 'Custom', 'value' => 'custom'],
                        ],
                    ]),
                    control('relative_selector', 'Custom Selector', [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'condition' => ['path' => '%%CURRENTPATH%%.relative_to', 'operand' => 'equals', 'value' => 'custom']
                    ]),
                    control('disable_at', 'Disable At', [
                        'type' => 'breakpoint_dropdown',
                        'layout' => 'vertical',
                    ]),
                    control('debug', 'Debug', [
                        'type' => 'toggle',
                    ]),
                ],
                ['condition' => ['path' => '%%CURRENTPATH%%.enabled', 'operand' => 'equals', 'value' => true]],
                'popout'
            )
        ],
        ['isExternal' => true],
        'popout'
    );
}
