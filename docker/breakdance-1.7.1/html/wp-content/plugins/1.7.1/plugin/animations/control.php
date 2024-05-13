<?php

namespace Breakdance\Animations;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_element_controls', 'Breakdance\Animations\addControls', 69, 2);


/**
 * @param Control[] $controls
 * @return Control[]
 */
function addControls($controls)
{
    $controls['settingsSections'][] = controlSection(
        'animations',
        'Animations',
        [
            \Breakdance\Animations\Scrolling\controls(),
            \Breakdance\Animations\Entrance\controls(),
            \Breakdance\Animations\Sticky\controls(),
        ],
        ['isExternal' => true]
    );

    /** @var Control[] $controls */
    return $controls;
}

// add_filter('breakdance_element_default_properties', 'Breakdance\Animations\getDefaultProperties', 100, 2);

/**
 * @param array|false $defaultProperties
 * @return array
 */
function getDefaultProperties($defaultProperties)
{
    if (!is_array($defaultProperties)) {
        $defaultProperties = [];
    }

    /**
     * @psalm-suppress MixedArrayAssignment
     */
    $defaultProperties['settings']['animations'] = [
        'scrolling_animation' => [
            'x' => [
                'start' => ['number' => 100, 'unit' => 'px', 'style' => '100px'],
                'end' => ['number' => 0, 'unit' => 'px', 'style' => '0'],
                'viewport' => [0, 100]
            ],
            'y' => [
                'start' => ['number' => 100, 'unit' => 'px', 'style' => '100px'],
                'end' => ['number' => 0, 'unit' => 'px', 'style' => '0'],
                'viewport' => [0, 100]
            ],
            'opacity' => [
                'start' => 0,
                'end' => 1,
                'viewport' => [0, 100]
            ],
            'blur' => [
                'start' => 10,
                'end' => 0,
                'viewport' => [0, 100]
            ],
            'rotation' => [
                'start' => 160,
                'end' => 0,
                'viewport' => [0, 100]
            ],
            'scale' => [
                'start' => 0,
                'end' => 1,
                'viewport' => [0, 100]
            ],
            'trigger_on' => ['breakpoint_base'],
            'sticky' => [
                'position' => 'none',
                'trigger_on' => ['breakpoint_base'],
            ]
        ],
        'entrance_animation' => [
            'duration' => [
                'number' => 500,
                'unit' => 'ms',
                'style' => '500ms'
            ],
            'advanced' => [
                'ease' => 'power1.out',
                'anchorPlacement' => 'top bottom'
            ]
        ],
    ];

    return $defaultProperties;
}
