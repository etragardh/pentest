<?php

namespace Breakdance\PluginsAPI;

class PluginsController
{

    use \Breakdance\Singleton;

    /**
     * @var string[]
     */
    public $plugins = [];

    /**
     * @param string $js_string
     * @return void
     */
    public function registerBuilderPlugin($js_string)
    {
        $this->plugins[] = $js_string;
    }

    /**
     * @var array{name:string,function:callable}[]
     */
    public $TwigFunctionsPHPSide = [];

    /**
     * @param string $twigName
     * @param callable $phpCallbackFunction
     * @param boolean $shouldMemoize
     * @param string $jsArrowFunction
     */
    public function registerTwigFunction($twigName, $phpCallbackFunction, $jsArrowFunction, $shouldMemoize = true)
    {

        $jsString = "window.addEventListener('breakdanceTwigApiReady', function (e) {
                        window.Breakdance.twigAPI.addFunction('$twigName', $jsArrowFunction, $shouldMemoize)
                    });";

        $this->registerBuilderPlugin($jsString);

        $this->TwigFunctionsPHPSide[] = array('name' => $twigName, 'function' => $phpCallbackFunction);
    }

}
