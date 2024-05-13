<?php

namespace Breakdance\PluginsAPI;

/**
 * @return array{script:string}[]
 */
function get_plugins_for_builder()
{

    return array_map(
        function ($plugin) {
            return ['script' => $plugin];
        },
        PluginsController::getInstance()->plugins
    );

}
