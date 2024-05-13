<?php

namespace Breakdance\GlobalSettings;

use function Breakdance\Elements\c;
use function Breakdance\Elements\control;

/**
 * @return Control
 */
function OTHER_SECTION()
{
    return c(
        "other",
        "Other",
        [c(
            "transition_duration",
            "Transition Duration",
            [],
            ['type' => 'unit', 'layout' => 'inline', 'unitOptions' => ['types' => ['0' => 'ms']]],
            false,
            false,
            [],
        )],
        ['type' => 'section'],
        false,
        false,
        [],
    );
}

/**
 * @return string
 */
function OTHER_DEFAULT_CSS()
{
    return (string) file_get_contents(dirname(__FILE__) . '/other-default-css.css.twig');
}

/**
 * @return string
 */
function GLOBAL_CSS_VARS()
{
    return (string) file_get_contents(dirname(__FILE__) . '/global-css-vars.css.twig');
}
