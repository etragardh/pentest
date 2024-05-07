<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\grid",
    controlSection('grid', 'Grid', [
        responsiveControl(
            "enable",
            "Enable Grid",
            [
                'type' => 'button_bar',
                'layout' => 'inline',
                'items' => [
                    '0' => [
                        'text' => 'True',
                        'label' => 'Label',
                        'value' => 'true',
                        'icon' => 'CheckSquareIcon'
                    ]
                ]
            ],
        ),
        controlSection(
            'columns',
            'Columns',
            [
                responsiveControl(
                    "template",
                    "Template",
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                            'text' => 'Auto',
                            'label' => 'Label',
                            'value' => 'auto'
                            ],
                            '1' => [
                            'text' => 'Custom',
                            'value' => 'custom'
                            ]
                        ]
                    ],
                ),
                responsiveControl(
                    "auto_fit",
                    "Auto-Fit",
                    [
                        'type' => 'button_bar',
                        'layout' => 'inline',
                        'items' => [
                            '0' => [
                                'text' => 'True',
                                'label' => 'Label',
                                'value' => 'true',
                                'icon' => 'CheckSquareIcon'
                            ]
                        ],
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "columns",
                    "Columns",
                    [
                        'type' => 'number',
                        'layout' => 'inline',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "min_width",
                    "Min-Width",
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "max_width",
                    "Max-Width",
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'auto'
                        ]
                    ],
                ),
                responsiveControl(
                    "columns_template",
                    "Column Template",
                    [
                        'type' => 'text',
                        'layout' => 'vertical',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "gap",
                    "Gap",
                    [
                        'type' => 'unit',
                        'layout' => 'inline'
                    ],
                ),
                responsiveControl(
                    "horizontal_alignment",
                    "Horizontal Alignment",
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                                'text' => 'Start',
                                'label' => 'Start',
                                'value' => 'start',
                                'icon' => 'AlignLeftIcon'
                            ],
                            '1' => [
                                'text' => 'Center',
                                'label' => 'Center',
                                'value' => 'center',
                                'icon' => 'MinimizeIcon'
                            ],
                            '2' => [
                                'text' => 'End',
                                'label' => 'End',
                                'value' => 'end',
                                'icon' => 'AlignRightIcon'
                            ],
                            '3' => [
                                'text' => 'Stretch',
                                'label' => 'Stretch',
                                'value' => 'stretch',
                                'icon' => 'LeftAndRightArrowsIcon'
                            ]
                        ]
                    ],
                ),
            ],
        ),
        controlSection(
            'rows',
            'Rows',
            [
                responsiveControl(
                    "template",
                    "Template",
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                            'text' => 'Auto',
                            'label' => 'Label',
                            'value' => 'auto'
                            ],
                            '1' => [
                            'text' => 'Custom',
                            'value' => 'custom'
                            ]
                        ]
                    ],
                ),
                responsiveControl(
                    "rows",
                    "Rows",
                    [
                        'type' => 'number',
                        'layout' => 'inline',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "min_height",
                    "Min-Height",
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "max_height",
                    "Max-Height",
                    [
                        'type' => 'unit',
                        'condition' => [
                            'path' => '%%CURRENTPATH%%.template',
                            'operand' => 'equals',
                            'value' => 'custom'
                        ]
                    ],
                ),
                responsiveControl(
                    "gap",
                    "Gap",
                    [
                        'type' => 'unit',
                        'layout' => 'inline'
                    ],
                ),
                responsiveControl(
                    "vertical_alignment",
                    "Vertical Alignment",
                    [
                        'type' => 'button_bar',
                        'layout' => 'vertical',
                        'items' => [
                            '0' => [
                                'text' => 'Start',
                                'label' => 'Start',
                                'value' => 'start',
                                'icon' => 'FlexAlignTopIcon'
                            ],
                            '1' => [
                                'text' => 'Center',
                                'label' => 'Center',
                                'value' => 'center',
                                'icon' => 'MinimizeIcon'
                            ],
                            '2' => [
                                'text' => 'End',
                                'label' => 'End',
                                'value' => 'end',
                                'icon' => 'FlexAlignBottomIcon'
                            ],
                            '3' => [
                                'text' => 'Stretch',
                                'label' => 'Stretch',
                                'value' => 'stretch',
                                'icon' => 'UpAndDownArrowsIcon'
                            ]
                        ]
                    ],
                ),
            ]
        )
    ]),
    true
);
