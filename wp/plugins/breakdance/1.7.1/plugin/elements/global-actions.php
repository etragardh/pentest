<?php

namespace Breakdance\Elements;


add_filter('breakdance_element_actions', '\Breakdance\Elements\addGlobalActions', 100, 1);

/**
 * @param BuilderActions[] $actions
 * @return BuilderActions[]
 */
function addGlobalActions($actions)
{
    $actions[] = [
        'onMountedElement' => [
            [
                'script' => <<<JS
                  if (typeof BreakdanceLightbox !== 'undefined') {
                      BreakdanceLightbox.autoload();
                  }
                JS
            ],
        ]
    ];

    return $actions;
}
