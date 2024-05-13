<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\PresetSections\getPresetSection;
use function Breakdance\Elements\repeaterControl;
use function Breakdance\Elements\responsiveControl;
use function Breakdance\Elements\responsiveControlWithHover;

/**
 * @return Control
 */
function TYPOGRAPHY_PRESETS_SECTION()
{
    return controlSection(
        'global_typography',
        'Presets',
        [
            repeaterControl(
                'typography_presets',
                'Typography Presets',
                [
                    control(
                        'preset',
                        'Preset Name',
                        ['type' => 'typography_preset', 'layout' => 'vertical']
                    ),
                    control(
                        'custom',
                        'Typography',
                        [
                            'type' => 'custom_typography',
                            'layout' => 'vertical',
                            'noLabel' => true,
                            'customTypographyOptions' => [
                                'hoverableEverything' => false,
                                'enableMediaQueries' => true,
                            ],
                        ]
                    ),
                ],
                [
                    'repeaterOptions' => [
                        'noDuplicate' => true,
                        'titleTemplate' => '{preset?.label}',
                        'defaultTitle' => 'Preset',
                        'buttonName' => 'Add Preset',
                        'defaultNewValue' => [
                            'preset' => [
                                'label' => '',
                                'id' => '',
                            ],
                        ],
                        'deleteConfirm' => true,
                        'deleteText' => 'Are you sure you want to delete this preset?',
                    ],
                ]
            ),
        ],
        null,
        'popout'
    );
}

/**
 * @return Control
 */
function TYPOGRAPHY_SECTION()
{
    return c(
        "typography",
        "Typography",
        [c(
            "heading_font",
            "Heading Font",
            [],
            ['type' => 'font_family', 'layout' => 'inline'],
            false,
            false,
            [],
        ), c(
            "body_font",
            "Body Font",
            [],
            ['type' => 'font_family', 'layout' => 'inline'],
            false,
            false,
            [],
        ),
            c(
                "base_size",
                "Base Size",
                [],
                ['type' => 'unit', 'layout' => 'inline'],
                true,
                false,
                [],
            ),
            c(
                "ratio",
                "Ratio",
                [],
                ['type' => 'number', 'layout' => 'inline', 'rangeOptions' => ['min' => 1, 'max' => 2, 'step' => 0.01]],
                true,
                false,
                [],
            ),
            c(
                "advanced",
                "Advanced",
                [getPresetSection("EssentialElements\\typography", 'Body', 'body', ['type' => 'popout']),
                    c(
                        "headings",
                        "Headings",
                        [
                            getPresetSection("EssentialElements\\typography", 'All Headings', 'all_headings', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H1', 'h1', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H2', 'h2', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H3', 'h3', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H4', 'h4', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H5', 'h5', ['type' => 'popout']),
                            getPresetSection("EssentialElements\\typography", 'H6', 'h6', ['type' => 'popout']),
                        ],
                        ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
                        false,
                        false,
                        [],
                    ),
                    controlSection(
                        'links',
                        'Links',
                        [
                            responsiveControlWithHover("color", "Color", ['type' => "color"]),
                            responsiveControl('font_weight', 'Font Weight', [
                                'type' => 'dropdown',
                                'items' =>
                                [
                                    array('text' => '100', 'value' => '100'),
                                    array('text' => '200', 'value' => '200'),
                                    array('text' => '300', 'value' => '300'),
                                    array('text' => '400', 'value' => '400'),
                                    array('text' => '500', 'value' => '500'),
                                    array('text' => '600', 'value' => '600'),
                                    array('text' => '700', 'value' => '700'),
                                    array('text' => '800', 'value' => '800'),
                                    array('text' => '900', 'value' => '900'),
                                ],
                            ]),
                            controlSection('decoration', 'Decoration', [
                                responsiveControlWithHover(
                                    "style",
                                    "Style",
                                    [
                                        'type' => "dropdown",
                                        'items' => [
                                            ['text' => 'none', 'value' => 'none'],
                                            ['text' => 'solid', 'value' => 'solid'],
                                            ['text' => 'double', 'value' => 'double'],
                                            ['text' => 'dotted', 'value' => 'dotted'],
                                            ['text' => 'dashed', 'value' => 'dashed'],
                                            ['text' => 'wavy', 'value' => 'wavy'],
                                        ],
                                    ]
                                ),
                                responsiveControlWithHover("color", "Color", [
                                    'type' => 'color',
                                ]),
                                responsiveControlWithHover("line", "Line", [
                                    'type' => "dropdown",
                                    'items' => [
                                        ['text' => 'underline', 'value' => 'underline'],
                                        ['text' => 'none', 'value' => 'none'],
                                        ['text' => 'overline', 'value' => 'overline'],
                                        ['text' => 'line-through', 'value' => 'line-through'],
                                    ],
                                ]),
                                responsiveControlWithHover("thickness", "Thickness", [
                                    'type' => "unit",
                                ]),
                            ], null, 'popout'),

                        ],
                        null,
                        'popout'
                    ), ],
                ['type' => 'section', 'layout' => 'inline', 'sectionOptions' => ['type' => 'popout']],
                false,
                false,
                [],
            ), TYPOGRAPHY_PRESETS_SECTION()],
        ['type' => 'section'],
        false,
        false,
        [],
    );
}

/**
 * @return string
 */
function TYPOGRAPHY_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/typography.css.twig') . "\n" . GLOBAL_TYPOGRAPHY_TEMPLATE();
}

/**
 * @return string
 */
function GLOBAL_TYPOGRAPHY_TEMPLATE()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-typography.css.twig');
}

/**
 * @return string
 */
function typographyPathToPresets()
{
    return 'settings.typography.global_typography.typography_presets';
}

/**
 * @return string[]
 */
function typographyPropertyPathsToWhitelistInFlatProps()
{
    return [typographyPathToPresets() . "[].preset.id"];
}
