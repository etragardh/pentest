<?php
// @psalm-ignore-file

namespace Breakdance\Animations\Scrolling;


add_filter('breakdance_element_actions',
    '\Breakdance\Animations\Scrolling\addActions', 100, 1);

/**
 * @param BuilderActions[] $actions
 * @return BuilderActions[]
 *
 * this return type is causing an error in psalm I couldn't solve so I'm ignoring this file
 */
function addActions($actions)
{
    // TODO: This code is ugly, find a way to refactor it and all other elements like it.
    //
    // Maybe something like this:
    //
    // **onPropertyChange**
    // InstanceRegistry.get('%%ID%%', 'parallax').refresh(props);
    //
    // **onBeforeDeletingElement**
    // InstanceRegistry.get('%%ID%%', 'parallax').destroy();
    //
    // **onMountedElement**
    // InstanceRegistry.register({
    //    id: '%%ID%%',
    //    instance: new BreakdanceParallax('%%SELECTOR%%'),
    //    namespace: 'parallax'
    // });

    $actions[] = [
        'onPropertyChange' => [
            [
                'script' => <<<JS
                  if (window.breakdanceParallaxInstances && window.breakdanceParallaxInstances[%%ID%%]) {
                    window.breakdanceParallaxInstances[%%ID%%].update({{ settings.animations.scrolling_animation|json_encode }});
                  } else if (typeof BreakdanceParallax !== 'undefined') {
                    if (!window.breakdanceParallaxInstances) window.breakdanceParallaxInstances = {};
                    window.breakdanceParallaxInstances[%%ID%%] = new BreakdanceParallax(
                     '%%SELECTOR%%',
                     {{ settings.animations.scrolling_animation|json_encode }}
                    );
                  }
                JS,
                'dependencies' => ['settings.animations.scrolling_animation']
            ],
        ],
        'onMountedElement' => [
            [
                'script' => <<<JS
                  if (typeof BreakdanceParallax !== 'undefined') {
                      if (!window.breakdanceParallaxInstances) window.breakdanceParallaxInstances = {};
                      window.breakdanceParallaxInstances[%%ID%%] = new BreakdanceParallax(
                        '%%SELECTOR%%',
                        {{ settings.animations.scrolling_animation|json_encode }}
                      );
                  }
                JS
            ],
        ],
        'onMovedElement' => [
            [
                'script' => <<<JS
                  if (window.breakdanceParallaxInstances && window.breakdanceParallaxInstances[%%ID%%]) {
                    window.breakdanceParallaxInstances[%%ID%%].update({{ settings.animations.scrolling_animation|json_encode }});
                  }
                JS,
            ]
        ],
        'onActivatedElement' => [
            [
                'script' => <<<JS
                  if (window.breakdanceParallaxInstances && window.breakdanceParallaxInstances[%%ID%%]) {
                    window.breakdanceParallaxInstances[%%ID%%].refresh();
                  }
                JS,
            ]
        ],
        'onBeforeDeletingElement' => [
            [
                'script' => <<<JS
                  if (window.breakdanceParallaxInstances && window.breakdanceParallaxInstances[%%ID%%]) {
                    window.breakdanceParallaxInstances[%%ID%%].destroy();
                    delete window.breakdanceParallaxInstances[%%ID%%];
                  }
                JS,
            ]
        ],
    ];

    return $actions;
}
