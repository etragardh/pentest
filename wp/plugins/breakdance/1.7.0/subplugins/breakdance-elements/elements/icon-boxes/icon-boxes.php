<?php

namespace EssentialElements;

use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;
use function Breakdance\Elements\responsiveControlWithHover;

class IconBoxes extends \Breakdance\Elements\Element
{

    static function uiIcon() {
        return 'IconsIcon';
    }

    static function tag()
    {
        return 'div';
    }

    static function name()
    {
        return 'Icon Boxes';
    }

    static function slug()
    {
        return get_class();
    }

    static function category()
    {
        return 'blocks';
    }

    static function defaultCss()
    {
        return file_get_contents(__DIR__ . '/icon-boxes.default.css');
    }

    static function template()
    {
        return file_get_contents(__DIR__.'/icon-boxes.html.twig');
    }

    static function cssTemplate()
    {
        return str_replace(
            '%%TEXT_TYPOGRAPHY%%',
            '{{ macros.typography(design.typography.text, globalSettings)  }}',
            str_replace(
                '%%TITLE_TYPOGRAPHY%%',
                '{{ macros.typography(design.typography.title, globalSettings)  }}',
                '%%SELECTOR%% {
                    {{ macros.spacing_margin_y(design.spacing) }}
                }
                '
                . file_get_contents(__DIR__.'/icon-boxes.css.twig')
            )
        );
    }

    static function designControls()
    {

        /* we need a way to set behavior for a wrapped line of icon boxes that doesn't take up the full width */


        return [
            controlSection('icon', 'Icon', [
                responsiveControlWithHover('color', 'Color', ['type' => 'color']),
                responsiveControl('size', 'Size', [
                    'type' => 'unit',
                    "rangeOptions" => ['min' => 10, 'max' => 128]
                ]),
                responsiveControl('position', 'Position',
                    [
                        'type' => 'button_bar',
                        'layout' => 'inline',
                        'items' =>
                        [
                            ['text' => 'Top', 'value' => 'top', 'icon' => 'FlexAlignTopIcon'],
                            ['text' => 'Left', 'value' => 'left', 'icon' => 'FlexAlignLeftIcon'],
                            ['text' => 'Right', 'value' => 'right', 'icon' => 'FlexAlignRightIcon'],
                        ]
                    ]
                ),
                responsiveControl('space_after', 'Space After', [
                    'type' => 'unit',
                    'layout' => 'inline',
                    "rangeOptions" => ['min' => 10, 'max' => 128]
                ]),
                responsiveControl(
                    'style',
                    'Style',
                    [
                        'type' => 'button_bar',
                        'layout' => 'inline',
                        'items' => [
                            ['text' => 'none', 'value' => 'none', 'icon' => 'CloseIcon'],
                            ['text' => 'outline', 'value' => 'outline', 'icon' => 'SquareIcon'],
                            ['text' => 'solid', 'value' => 'solid', 'icon' => 'SquareIcon'],
                        ]
                    ]
                ),
                responsiveControlWithHover('background_color', 'Background', ['type' => 'color']),
                responsiveControl('radius', 'Radius', ['type' => 'unit', "rangeOptions" => ['min' => 0, 'max' => 100]]),
                responsiveControl('padding', 'Padding', ['type' => 'unit', "rangeOptions" => ['min' => 0, 'max' => 75]]),
            ]),
            controlSection('title', 'Title', [
                \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\css", 'Typography', 'typography', ['type' => 'popout']),
                responsiveControl('space_after', 'Space After', ['type' => 'unit', 'layout' => 'inline']),
            ]),
            controlSection('text', 'Text', [
                \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\css", 'Typography', 'typography', ['type' => 'popout']),
            ]),
            controlSection('box', 'Box', [
                responsiveControl('boxes_per_row', 'Boxes Per Row', ['type' => 'number', 'layout' => 'inline']),
                controlSection('box_spacing', 'Space Between Boxes',
                    [
                        responsiveControl('spacing', 'Horizontal', ['type' => 'unit', 'layout' => 'vertical50']),
                        responsiveControl('vertical_spacing', 'Vertical', ['type' => 'unit', 'layout' => 'vertical50']),                    ],
                ),
                responsiveControl('content_alignment', 'Content Alignment', [
                    'type' => 'button_bar',
                    'layout' => 'inline',
                    'items' =>
                    [
                        array('text' => 'left', 'value' => 'left', 'icon' => 'AlignLeftIcon'),
                        array('text' => 'center', 'value' => 'center', 'icon' => 'AlignCenterIcon'),
                        array('text' => 'right', 'value' => 'right', 'icon' => 'AlignRightIcon'),
                    ],
                ]),
                responsiveControlWithHover('background_color', 'Background', ['type' => 'color']),
                responsiveControl('padding', 'Padding', ['type' => 'unit']),
                responsiveControl('radius', 'Radius', ['type' => 'unit']),
                responsiveControlWithHover('shadow', 'Shadow', ['type' => 'text']),
            ]),
            controlSection('spacing', 'Spacing', [
                controlSection('spacing', 'Container Spacing',
                    [
                        responsiveControl('before', 'Before', ['type' => 'unit', 'layout' => 'vertical50']),
                        responsiveControl('after', 'After', ['type' => 'unit', 'layout' => 'vertical50']),
                    ],
                )
            ]),
        ];

        /**
         * try reorganizing the boxes and layout stuff
         */



        return [
            controlSection('icon', 'Icon', [
                // responsiveControl('icon', 'Icon', ['type' => 'icon_styles']),
                responsiveControlWithHover('color', 'Color', ['type' => 'color']),
                responsiveControl('size', 'Size', ['type' => 'unit', 'layout' => 'inline']),
                controlSection('advanced', 'Advanced', [
                    responsiveControl(
                        'style',
                        'Style',
                        ['type' => 'dropdown', 'items' => [
                            ['text' => 'none', 'value' => 'none'],
                            ['text' => 'outline', 'value' => 'outline'],
                            ['text' => 'solid', 'value' => 'solid'],
                        ]]
                    ),
                    responsiveControlWithHover('background_color', 'Background Color', ['type' => 'color']),
                    responsiveControl('radius', 'Radius', ['type' => 'unit']),
                    responsiveControl('padding', 'Padding', ['type' => 'unit']),
                ], null, 'popout'),
                controlSection('penis', 'Penis', [
                    responsiveControl('radius', 'Radius', ['type' => 'unit', 'layout' => 'vertical50']),
                    responsiveControl('padding', 'Padding', ['type' => 'unit', 'layout' => 'vertical50']),
                ])
                // responsiveControl('size', 'Size', ['type' => 'unit']),
                // responsiveControl(
                //     'style',
                //     'Style',
                //     ['type' => 'dropdown', 'items' => [
                //         ['text' => 'none', 'value' => 'none'],
                //         ['text' => 'outline', 'value' => 'outline'],
                //         ['text' => 'solid', 'value' => 'solid'],
                //     ]]
                // ),
                // responsiveControlWithHover('background_color', 'Background Color', ['type' => 'color']),
                // responsiveControl('radius', 'Radius', ['type' => 'unit']),
                // responsiveControl('padding', 'Padding', ['type' => 'unit']),
                /* how about border / and border width ? */
            ]),
            controlSection('layout', 'Layout', [
                responsiveControl('content_alignment', 'Content Alignment', [
                    'type' => 'button_bar',
                    'items' =>
                    [
                        array('text' => 'left', 'value' => 'left', 'icon' => 'AlignLeftIcon'),
                        array('text' => 'center', 'value' => 'center', 'icon' => 'AlignCenterIcon'),
                        array('text' => 'right', 'value' => 'right', 'icon' => 'AlignRightIcon'),
                    ],
                ]),
                responsiveControl('icon_position', 'Icon Position', [
                    'type' => 'dropdown',
                    // 'condition' => ['path' => 'design.layout.content_alignment', 'value' => 'left'], /* or right */
                    'items' => [
                        ['text' => 'top', 'value' => 'top'],
                        ['text' => 'left', 'value' => 'left'],
                        ['text' => 'right', 'value' => 'right'],
                    ],
                ]),
                responsiveControl('space_after_icon', 'Space After Icon', ['type' => 'unit']),
                responsiveControl('space_after_title', 'Space After Title', ['type' => 'unit']),
                responsiveControl('boxes_per_row', 'Boxes Per Row', ['type' => 'number']),
                responsiveControl('spacing', 'Space Between Boxes', ['type' => 'unit']),
                responsiveControl('vertical_spacing', 'Vertical Space Between Boxes', ['type' => 'unit']),
            ]),
            controlSection('typography', 'Typography', [
                \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\typography", 'title', 'Title', ['type' => 'popout']),
                \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\typography", 'text', 'Text', ['type' => 'popout']),
            ]),
            controlSection('box_style', 'Box Style', [
                responsiveControlWithHover('background_color', 'Background Color', ['type' => 'color']),
                responsiveControl('padding', 'Padding', ['type' => 'unit']),
                responsiveControl('radius', 'Radius', ['type' => 'unit']),
                responsiveControlWithHover('shadow', 'Shadow', ['type' => 'text']),
            ]),
            \Breakdance\Elements\PresetSections\getPresetSection("EssentialElements\\spacing_margin_y", 'Spacing', 'spacing'),
        ];
    }

    static function contentControls()
    {

        return
            [
            \Breakdance\Elements\controlSection(
                'content',
                'Content',
                [
                    \Breakdance\Elements\repeaterControl(
                        'icon_boxes',
                        'Icon Boxes',
                        [
                            \Breakdance\Elements\control('icon', 'Icon', ['type' => 'icon', 'layout' => 'vertical']),
                            \Breakdance\Elements\control('title', 'Title', ['type' => 'text', 'layout' => 'vertical']),
                            \Breakdance\Elements\control('text', 'Text', ['type' => 'text', 'layout' => 'vertical']),
                        ],
                    ),
                ]
            ),
        ];
    }

    static function settingsControls()
    {
        return [];
    }

    static function defaultProperties()
    {
        return
            [
            'content' =>
            [
                'content' =>
                [
                    'icon_boxes' => array(
                        ['icon' => 'icon 1', 'title' => 'My Icon Box Title', 'text' => 'text for 1'],
                        ['icon' => 'icon 2', 'title' => 'My Icon Box Title 2', 'text' => 'and 2'],
                        ['icon' => 'icon 3', 'title' => 'My Icon Box Title 3', 'text' => 'and 3'],
                    ),
                ],
            ],
        ];
    }

    static function nestingRule()
    {
        return ['type' => 'final'];
    }

    static function spacingBars()
    {
        return \Breakdance\Elements\getMarginYSpacingBars();
    }

    static function experimental()
    {
        return true;
    }

}
