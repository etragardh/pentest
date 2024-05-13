<?php

/**
 * Plugin Name: Breakdance Element Development
 * Plugin URI: https://breakdance.com/
 * Description: ALPHA - NOT TO BE USED IN PRODUCTION
 * Author: Breakdance
 * Author URI: https://breakdance.com/
 * License: GPLv2
 * Text Domain: breakdance
 * Domain Path: /languages/
 * Version: 0.0.1
 */

namespace EssentialElements;

use function Breakdance\Util\getDirectoryPathRelativeToPluginFolder;

define('BREAKDANCE_ELEMENTS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('__BREAKDANCE_ELEMENTS_DIR__', __DIR__);

require __DIR__ . "/macros-manual/woo/base.php";
require_once __DIR__ . "/lib/base.php";

add_action('breakdance_loaded', function() {
    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements',
        'EssentialElements',
        'element',
        'Breakdance Elements',
        true
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/elements-manual',
        'EssentialElements',
        'element',
        'Breakdance Manual Elements',
        true,
        true
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/macros',
        'EssentialElements',
        'macro',
        'Breakdance Macros',
        true,
    );

    \Breakdance\ElementStudio\registerSaveLocation(
        getDirectoryPathRelativeToPluginFolder(__DIR__) . '/presets',
        'EssentialElements',
        'preset',
        'Breakdance Presets',
        true,
    );

    // TODO move all presets?
    // TODO: create a manual folder? Register them? Some of them need to be done through an element instead????!
});
