<?php

namespace Breakdance\Elements\PresetSections;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

PresetSectionsController::getInstance()->register(
    "EssentialElements\\lightbox_design",
    controlSection("lightbox", "Lightbox", [
        control(
            'info_notice', 'Info Notice',
            [
                'type' => 'alert_box',
                'layout' => 'vertical',
                'alertBoxOptions' => [
                    'style' => 'info',
                    'content' => '<p>View the page on the frontend to preview the lightbox.</p>'
                ]
            ]
        ),
        control("background", "Background", [
            'type' => 'color',
            'layout' => 'inline',
            'colorOptions' => ['type' => 'solidAndGradient']
        ]),
        c(
            "controls",
            "Controls",
            [],
            [
                'type' => 'color',
                'layout' => 'inline'
            ],
            false,
            true
        ),
        control(
            "thumbnails",
            "Thumbnails",
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control('thumbnail', 'Thumbnail', [
            'type' => 'color',
            'condition' => ['path' => '%%CURRENTPATH%%.thumbnails', 'operand' => 'is set', 'value' => null]
        ]),
        control('thumbnail_active', 'Thumbnail Active', [
            'type' => 'color',
            'condition' => ['path' => '%%CURRENTPATH%%.thumbnails', 'operand' => 'is set', 'value' => null]
        ]),
        control(
            "animated_thumbnails",
            "Animated Thumbnails",
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control(
            "autoplay",
            "Autoplay",
            ['type' => 'toggle', 'layout' => 'inline'],
        ),
        control(
            "speed",
            "Speed",
            ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => [], 'defaultType' => 'ms'], 'condition' => ['path' => '%%CURRENTPATH%%.autoplay', 'operand' => 'is set', 'value' => null]]
        ),
//        control(
//            "autoplay_videos",
//            "Autoplay Videos",
//            ['type' => 'toggle', 'layout' => 'inline'],
//        ),
        ],
    ),
    true
);

PresetSectionsController::getInstance()->register(
    "EssentialElements\\lightbox_single_design",
    controlSection("lightbox", "Lightbox", [
        control("background", "Background", [
            'type' => 'color',
            'layout' => 'inline',
            'colorOptions' => ['type' => 'solidAndGradient']
        ]),
        c(
            "controls",
            "Controls",
            [],
            [
                'type' => 'color',
                'layout' => 'inline'
            ],
            false,
            true
        )],
    ),
    true
);
