<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;
use function Breakdance\Elements\responsiveControl;


/**
 * @param boolean $hoverable
 * @param boolean $enableMediaQueries
 * @return Control
 */
function getTypographyEffectsSection($hoverable, $enableMediaQueries) {

    $strokeWidthControl = control(
        'stroke_width',
        'Stroke Width',
        [
            'type' => 'unit'
        ],
        $enableMediaQueries
    );

    $strokeColorControl = control(
        'stroke_color',
        'Stroke Color',
        [
            'type' => 'color'
        ],
        $enableMediaQueries
    );

    $fillControl = control(
        'fill',
        'Fill',
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => 'Transparent', 'value' => 'transparent'],
                ['text' => 'Gradient', 'value' => 'gradient'],
                ['text' => 'Image', 'value' => 'image'],
            ]
        ],
        $enableMediaQueries
    );

    $fillGradientControl = control(
        'gradient',
        'Gradient',
        [
            'type' => 'color',
            'colorOptions' => ['type' => 'gradientOnly'],
            'layout' => 'vertical',
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'gradient',
            ]
        ],
        $enableMediaQueries
    );

    $fillImageControl = control(
        'image',
        'Image',
        [
            'type' => 'wpmedia',
            'layout' => 'vertical',
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageSizeControl = control(
        'image_size',
        'Size',
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => 'cover', 'value' => 'cover'],
                ['text' => 'contain', 'value' => 'contain'],
                ['text' => 'auto', 'value' => 'auto'],
                ['text' => 'custom', 'value' => 'custom'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomSizeHeightControl = control(
        'image_height',
        'Height',
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_size',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomSizeWidthControl = control(
        'image_width',
        'Width',
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_size',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageRepeatControl = control(
        'image_repeat',
        'Repeat',
        [
            'type' => 'dropdown',
            'items' => [
                ['text' => 'no-repeat', 'value' => 'no-repeat'],
                ['text' => 'repeat', 'value' => 'repeat'],
                ['text' => 'repeat-x', 'value' => 'repeat-x'],
                ['text' => 'repeat-y', 'value' => 'repeat-y'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImagePositionControl = control(
        'image_position',
        'Position',
        [
            'type' => 'dropdown',
            'items' => [
                ['value' => 'left top', 'text' => 'left top'],
                ['value' => 'left center', 'text' => 'left center'],
                ['value' => 'left bottom', 'text' => 'left bottom'],
                ['value' => 'right top', 'text' => 'right top'],
                ['value' => 'right center', 'text' => 'right center'],
                ['value' => 'right bottom', 'text' => 'right bottom'],
                ['value' => 'center top', 'text' => 'center top'],
                ['value' => 'center center', 'text' => 'center center'],
                ['value' => 'center bottom', 'text' => 'center bottom'],
                ['text' => 'custom', 'value' => 'custom'],
            ],
            'condition' => [
                'path' => '%%CURRENTPATH%%.fill',
                'operand' => 'equals',
                'value' => 'image',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomPositionTopControl = control(
        'image_top',
        'Top',
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_position',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $fillImageCustomPositionLeftControl = control(
        'image_left',
        'Left',
        [
            'type' => 'unit',
            'condition' => [
                'path' => '%%CURRENTPATH%%.image_position',
                'operand' => 'equals',
                'value' => 'custom',
            ],
        ],
        $enableMediaQueries
    );

    $textShadowControl = control(
        'text_shadow',
        'Text Shadow',
        [
            'type' => 'shadow',
            'layout' => 'vertical',
            'shadowOptions' => [
                'type' => 'text'
            ]
        ],
        $enableMediaQueries
    );


    if ($hoverable) {
        $strokeWidthControl['enableHover'] = true;
        $strokeColorControl['enableHover'] = true;
        $fillGradientControl['enableHover'] = true;
        $fillImageControl['enableHover'] = true;
        $textShadowControl['enableHover'] = true;
    }

    return controlSection('effects', 'Effects', [
        $strokeWidthControl,
        $strokeColorControl,
        $fillControl,
        $fillGradientControl,
        $fillImageControl,
        $fillImageSizeControl,
        $fillImageCustomSizeHeightControl,
        $fillImageCustomSizeWidthControl,
        $fillImageRepeatControl,
        $fillImagePositionControl,
        $fillImageCustomPositionTopControl,
        $fillImageCustomPositionLeftControl,
        $textShadowControl,
    ],
    null, 'popout');

}

/**
 * @param array{includeColor?:boolean,includeAlign?:boolean,includeEffects?:boolean,hoverableColorAndEffects?:boolean,hoverableEverything?:boolean} $args
 * @return Control
 */
function getTypographySection($args) {

    $args = array_merge(
        [
            'includeColor' => false,
            'includeAlign' => false,
            'includeEffects' => false,
            'hoverableColorAndEffects' => false,
            'hoverableEverything' => false,
            'enableMediaQueries' => true,
        ],
        $args
    );

    $effectsSection = getTypographyEffectsSection($args['hoverableColorAndEffects'] || $args['hoverableEverything'], $args['enableMediaQueries']);

    $typographySection = controlSection(
        'typography',
        'Typography',
        []
    );

    // COLOR
    if ($args['includeColor']) {
        $colorControl = control(
            "color",
            "Color",
            [
                'type' => "color",
                "layout" => "inline",
                'colorOptions' => ['type' => 'solidOnly']
            ],
            $args['enableMediaQueries']
        );

        if ($args['hoverableColorAndEffects'] || $args['hoverableEverything']) {
            $colorControl['enableHover'] = true;
        }

        $typographySection['children'][] = $colorControl;
    }


    // TEXT ALIGN
    if ($args['includeAlign']) {
        $alignControl = control(
            'text_align',
            'Alignment',
            [
                'type' => 'button_bar',
                'layout' => 'inline',
                'items' => [
                    array('text' => 'left', 'value' => 'left', 'icon' => 'AlignLeftIcon'),
                    array('text' => 'center', 'value' => 'center', 'icon' => 'AlignCenterIcon'),
                    array('text' => 'right', 'value' => 'right', 'icon' => 'AlignRightIcon'),
                    array('text' => 'justify', 'value' => 'justify', 'icon' => 'AlignJustifyIcon'),
                ],
            ],
            true
        );

        // alignControl is deliberately not hoverable, even when $args['hoverableEverything'] is true. It makes no sense to have hoverable alignment.
        $typographySection['children'][] = $alignControl;
    }


    // TYPOGRAPHY
    $typographyControl = control(
        'typography',
        'Typography',
        [
            'type' => 'typography',
            'layout' => 'vertical',
            'noLabel' => true,
            'typographyOptions' => [
                'customTypographyOptions' => [
                    'hoverableEverything' => $args['hoverableEverything'],
                    'enableMediaQueries' => $args['enableMediaQueries'],
                ]
            ]
        ]
    );

    $typographySection['children'][] = $typographyControl;


    // EFFECTS
    if ($args['includeEffects']) {
        $typographySection['children'][] = $effectsSection;
    }

    return $typographySection;


}






function registerTypographySections() {

    /*
    i'm pretty sure we don't need all of these
    we'll figure out what we need as we build elements though
    */

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_nothing",
        getTypographySection(
            [
            ]
        ),
        true
    );


    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => false,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    // -----



    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_hoverable_color",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align_with_hoverable_color",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => false,
                'includeAlign' => true,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_with_hoverable_color_and_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align_with_hoverable_color_and_effects",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => true,
                'hoverableEverything' => false
            ]
        ),
        true
    );

    // ------

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_align_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_with_effects_and_align_with_hoverable_everything",
        getTypographySection(
            [
                'includeColor' => true,
                'includeEffects' => true,
                'includeAlign' => true,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => true
            ]
        ),
        true
    );

    PresetSectionsController::getInstance()->register(
        "EssentialElements\\typography_without_media_queries",
        getTypographySection(
            [
                'includeColor' => true,
                'includeAlign' => false,
                'includeEffects' => false,
                'hoverableColorAndEffects' => false,
                'hoverableEverything' => false,
                'enableMediaQueries' => false
            ]
        ),
        true
    );
}


registerTypographySections();
