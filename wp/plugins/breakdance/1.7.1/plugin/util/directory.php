<?php

namespace Breakdance\Util;

/**
 * @param string $dir
 * @return string
 */
function getDirectoryPathRelativeToPluginFolder($dir){
    /**
     * @psalm-suppress UndefinedConstant
     * @var string
     */
    $wpPluginDir = realpath((string) WP_PLUGIN_DIR);
    $wpPluginDir = normalizeDirectorySeparatorsInPath($wpPluginDir);
    $normalizedDir = normalizeDirectorySeparatorsInPath(realpath($dir));

    return str_replace($wpPluginDir . DIRECTORY_SEPARATOR, '', $normalizedDir);
}

/**
 * Windows and WP are stupid, and some things return '\' and some '/' *for the same path*.
 * e.g __DIR__ returns "app\public\wp-content\plugins\" while WP_PLUGIN_DIR returns "app\public/wp-content/plugins"
 *
 * @param string $path
 * @return string
 */
function normalizeDirectorySeparatorsInPath($path){
    return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
}
