<?php

namespace Breakdance\Elements;

use Breakdance\Render\ScriptAndStyleHolder;
use function Breakdance\Render\replaceVariableInDependencies;

class TwigAssetLoader {
    use \Breakdance\Singleton;

    /**
     * @var array<string,string>
     */
    public $styles = [];

    /**
     * @var array<string,string>
     */
    public $scripts = [];

    /**
     * @param string $name
     * @param string $url
     */
    public function registerStyle($name, $url)
    {
        $this->styles[$name] = $url;
    }

    /**
     * @param string $name
     * @param string $url
     */
    public function registerScript($name, $url)
    {
        $this->scripts[$name] = $url;
    }

    /**
     * @param string $name
     */
    public function enqueueStyle($name)
    {
        $url = $this->styles[$name];
        $dependencies = ['styles' => [$url]];
        ScriptAndStyleHolder::getInstance()->append($dependencies);
    }

    /**
     * @param string $name
     */
    public function enqueueScript($name)
    {
        $url = $this->scripts[$name];
        $dependencies = ['scripts' => [$url]];
        ScriptAndStyleHolder::getInstance()->append($dependencies);
    }

    /**
     * @param ElementDependenciesAndConditions $dependency
     */
    public function addDependency($dependency)
    {
        if (isset($dependency['scripts'])) {
            $dependency['scripts'] = replaceVariableInDependencies($dependency['scripts']);
        }

        if (isset($dependency['styles'])) {
            $dependency['styles'] = replaceVariableInDependencies($dependency['styles']);
        }

        ScriptAndStyleHolder::getInstance()->append($dependency);
    }
}

/**
 * @param string $name
 * @param string $url
 */
function registerStyle($name, $url)
{
    TwigAssetLoader::getInstance()->registerStyle($name, $url);
}

/**
 * @param string $name
 * @param string $url
 */
function registerScript($name, $url)
{
    TwigAssetLoader::getInstance()->registerScript($name, $url);
}
