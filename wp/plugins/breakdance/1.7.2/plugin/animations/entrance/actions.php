<?php
// @psalm-ignore-file

namespace Breakdance\Animations\Entrance;


add_filter('breakdance_element_actions', '\Breakdance\Animations\Entrance\addActions', 100, 1);

/**
 * @param BuilderActions[] $actions
 *
 * @return BuilderActions[]
 *
 * this return type is causing an error in psalm I couldn't solve so I'm ignoring this file
 */
function addActions($actions)
{
    $actions[] = [
        'onPropertyChange' => [
            [
                'script' => <<<JS
                  if (window.breakdanceEntranceInstances && window.breakdanceEntranceInstances[%%ID%%]) {
                    window.breakdanceEntranceInstances[%%ID%%].update({{ settings.animations.entrance_animation|json_encode }});
                  } else if (typeof BreakdanceEntrance !== 'undefined') {
                    if (!window.breakdanceEntranceInstances) window.breakdanceEntranceInstances = {};
                    window.breakdanceEntranceInstances[%%ID%%] = new BreakdanceEntrance(
                      '%%SELECTOR%%',
                      {{ settings.animations.entrance_animation|json_encode }}
                    );
                  }
                JS,
                'dependencies' => ['settings.animations.entrance_animation']
            ],
        ],
        'onMountedElement' => [
            [
                'script' => <<<JS
                  if (typeof BreakdanceEntrance !== 'undefined') {
                      if (!window.breakdanceEntranceInstances) window.breakdanceEntranceInstances = {};
                      window.breakdanceEntranceInstances[%%ID%%] = new BreakdanceEntrance(
                        '%%SELECTOR%%',
                        {{ settings.animations.entrance_animation|json_encode }}
                      );
                  }
                JS
            ],
        ],
        'onMovedElement' => [
            [
                'script' => <<<JS
                  if (window.breakdanceEntranceInstances && window.breakdanceEntranceInstances[%%ID%%]) {
                    window.breakdanceEntranceInstances[%%ID%%].update({{ settings.animations.entrance_animation|json_encode }});
                  }
                JS,
            ]
        ],
        'onBeforeDeletingElement' => [
            [
                'script' => <<<JS
                  if (window.breakdanceEntranceInstances && window.breakdanceEntranceInstances[%%ID%%]) {
                    window.breakdanceEntranceInstances[%%ID%%].destroy();
                    delete window.breakdanceEntranceInstances[%%ID%%];
                  }
                JS,
            ]
        ],
    ];

    return $actions;
}
