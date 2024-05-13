<?php

namespace Breakdance\Elements;

use Breakdance\PluginsAPI\PluginsController;
use Breakdance\Render\ScriptAndStyleHolder;
use function Breakdance\Render\replaceVariableInDependencies;

$scripts = json_encode(TwigAssetLoader::getInstance()->scripts);
$styles = json_encode(TwigAssetLoader::getInstance()->styles);

// Scripts
PluginsController::getInstance()->registerTwigFunction(
    'enqueue_script',
    'Breakdance\Elements\enqueueScript',
    <<<JS
    (name) => {
        const scripts = {$scripts};
        const url = scripts[name];
        if (!url) return;
        window.Breakdance.canvas.addUniqueScriptToHead({ scriptUrl: url });
    }
    JS
);

/**
 * @param string $name
 */
function enqueueScript($name)
{
    TwigAssetLoader::getInstance()->enqueueScript($name);
}

// Styles
PluginsController::getInstance()->registerTwigFunction(
    'enqueue_style',
    'Breakdance\Elements\enqueueStyle',
    <<<JS
    (name) => {
        const styles = {$styles};
        const url = styles[name];
        if (!url) return;
        window.Breakdance.canvas.addUniqueStylesheetToEndOfHead({ stylesheetUrl: url });
    }
    JS
);

/**
 * @param string $name
 */
function enqueueStyle($name)
{
    TwigAssetLoader::getInstance()->enqueueStyle($name);
}

// Styles
PluginsController::getInstance()->registerTwigFunction(
    'add_dependency',
    'Breakdance\Elements\addDependency',
    <<<JS
    (dependency) => {
        window.Breakdance.canvas.addDependenciesToHead({
          dependencies: [dependency]
        });
    }
    JS
);

/**
 * @param ElementDependenciesAndConditions $dependency
 */
function addDependency($dependency)
{
    TwigAssetLoader::getInstance()->addDependency($dependency);
}
