<?php

namespace Breakdance\PluginsAPI;

/**
 * @param string $js_string
 * @return void
 */
function registerBuilderPlugin($js_string)
{
    PluginsController::getInstance()->registerBuilderPlugin($js_string);
}
