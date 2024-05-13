<?php

namespace Breakdance\Elements\UniversalControls;

use function Breakdance\Elements\control;
use function Breakdance\Elements\controlSection;

add_filter('breakdance_element_controls', '\Breakdance\Elements\UniversalControls\addHideByBreakpointControls', 69, 2);

/**
 * @param mixed $controls
 * @param mixed $element
 * @return mixed
 */
function addHideByBreakpointControls($controls, $element)
{

    /** @psalm-suppress MixedArrayAccess */
    $controls['settingsSections'][] = controlSection('hide_on_breakpoint', 'Hide On Breakpoint', [
        control(
            'hide',
            'Hide',
            ['type' => 'breakpoint_dropdown', 'breakpointOptions' => ['multiple' => true], 'layout' => 'vertical']
        ),
        control(
            'builder_preview',
            'In-Builder Preview',
            [
                'type' => 'dropdown', 'items' => [
                ['text' => 'Greyed Out', 'value' => 'show'],
                ['text' => 'Hidden', 'value' => 'hide'],
            ],
                'layout' => 'vertical'
            ]
        )
    ]);

    return $controls;
}

add_filter('breakdance_element_css_template', '\Breakdance\Elements\UniversalControls\addHideByBreakpointCssTemplate', 100, 1);

/**
 * @param string $cssTemplate
 * @return string
 */
function addHideByBreakpointCssTemplate($cssTemplate)
{
    return $cssTemplate . "\n\n" . (string) file_get_contents(dirname(__FILE__) . "/hide-by-breakpoint.twig");
}

\Breakdance\PluginsAPI\PluginsController::getInstance()->registerTwigFunction(
    'make_above_and_below_query',
    'Breakdance\Elements\UniversalControls\make_above_and_below_query',
    '(breakpoint, builtinBreakpoints) => {
          /*
            this code assumes builtinBreakpoints is sorted correctly, which it is by default.
            it cant be modified, so it should stay sorted.
            */

          const thisBreakpointIndex = builtinBreakpoints.findIndex(
            b => b.id === breakpoint.id
          );
          const nextBreakpoint = builtinBreakpoints[thisBreakpointIndex + 1];

          const rules = [];

          if (breakpoint.id !=="'.BASE_BREAKPOINT_ID.'") {
            rules.push(`(max-width: ${breakpoint.maxWidth}px)`);
          }

          if (nextBreakpoint) {
            rules.push(`(min-width: ${nextBreakpoint.maxWidth + 1}px)`);
          }

          const query = "@media" + rules.join(" and ");

          return query;
        }
    '
);

/**
 * @param Breakpoint $breakpoint
 * @param Breakpoint[] $builtinBreakpoints
 * @return string
 *
 * https://twig.symfony.com/doc/3.x/advanced.html#filters
 * twig sends the argument to the called function
 */
function make_above_and_below_query($breakpoint, $builtinBreakpoints)
{

    /*
    this code assumes builtinBreakpoints is sorted correctly, which it is by default.
    it cant be modified, so it should stay sorted.
    */


    $nextOneFlag = false;
    $nextBreakpoint = false;
    foreach ($builtinBreakpoints as $b) {
        if ($nextOneFlag === true) {
            $nextBreakpoint = $b;
            break;
        }

        if ($b['id'] === $breakpoint['id']) {
            $nextOneFlag = true;
        }
    }

    $ruleString = "";

    if ($breakpoint['id'] !== BASE_BREAKPOINT_ID && array_key_exists('maxWidth', $breakpoint)) {
        $ruleString = '(max-width: ' . $breakpoint['maxWidth'] . 'px)';
    }

    if ($nextBreakpoint && array_key_exists('maxWidth', $nextBreakpoint) && $nextBreakpoint['maxWidth']) {
        $ruleString .= ' and (min-width: ' . ($nextBreakpoint['maxWidth'] + 1) . 'px)';
    }

    if (strpos($ruleString, ' and ') === 0) {
        $ruleString = str_replace(' and ', '', $ruleString);
    }

    $query = '@media ' . $ruleString;

    return $query;
}
