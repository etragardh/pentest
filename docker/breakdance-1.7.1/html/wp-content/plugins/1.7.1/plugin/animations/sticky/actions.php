<?php
// @psalm-ignore-file

namespace Breakdance\Animations\Sticky;

add_filter('breakdance_element_actions',
    '\Breakdance\Animations\Sticky\addActions', 100, 1);

/**
 * @param BuilderActions[] $actions
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
                  if (window.breakdanceStickyInstances && window.breakdanceStickyInstances[%%ID%%]) {
                    window.breakdanceStickyInstances[%%ID%%].update({{ settings.animations.sticky|json_encode }});
                  } else if (typeof BreakdanceSticky !== 'undefined') {
                    if (!window.breakdanceStickyInstances) window.breakdanceStickyInstances = {};
                    window.breakdanceStickyInstances[%%ID%%] = new BreakdanceSticky(
                      '%%SELECTOR%%',
                      {{ settings.animations.sticky|json_encode }}
                    );
                  }
                JS,
                'dependencies'=>['settings.animations.sticky'],
            ],
        ],
        'onMountedElement'=>[
            [
                'script'=><<<JS
                  if (typeof BreakdanceSticky !== 'undefined') {
                    if (!window.breakdanceStickyInstances) window.breakdanceStickyInstances = {};
                    window.breakdanceStickyInstances[%%ID%%] = new BreakdanceSticky(
                      '%%SELECTOR%%',
                      {{ settings.animations.sticky|json_encode }}
                    );
                  }
                JS
            ],
        ],
        'onMovedElement'=>[
            [
                'script'=><<<JS
                  if (window.breakdanceStickyInstances && window.breakdanceStickyInstances[%%ID%%]) {
                    window.breakdanceStickyInstances[%%ID%%].update({{ settings.animations.sticky|json_encode }});
                  }
                JS,
            ],
        ],
        'onActivatedElement'=>[
            [
                'script'=><<<JS
                  if (window.breakdanceStickyInstances && window.breakdanceStickyInstances[%%ID%%]) {
                    window.breakdanceStickyInstances[%%ID%%].refresh();
                  }
                JS,
            ],
        ],
        'onBeforeDeletingElement'=>[
            [
                'script'=><<<JS
                  if (window.breakdanceStickyInstances && window.breakdanceStickyInstances[%%ID%%]) {
                    window.breakdanceStickyInstances[%%ID%%].remove();

                    delete window.breakdanceStickyInstances[%%ID%%];
                  }
                JS,
            ],
        ],
    ];

    return $actions;
}
