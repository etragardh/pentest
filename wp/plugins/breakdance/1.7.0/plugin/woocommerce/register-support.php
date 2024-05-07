<?php

namespace Breakdance\WooCommerce;

/**
 * TODO: document this for external use
 *
 * @param string $themeName
 * @param Closure():void $disableWooCoStylesCallback
 */
function registerThemeWooCommerceSupportAndDisableStyles($themeName, $disableWooCoStylesCallback){
    RegisteredThemesSupportingWooCommerceAndDisablingWooCoStyles::getInstance()->register($themeName);

    call_user_func($disableWooCoStylesCallback);
}

class RegisteredThemesSupportingWooCommerceAndDisablingWooCoStyles
{
    use \Breakdance\Singleton;

    /**
     * @var string[]
     */
    private $themeNames = [];

    /**
     * @param string $themeName
     */
    public function register($themeName){
        $this->themeNames[] = $themeName;
    }

    /**
     * @return string[]
     */
    public function getThemeNames(){
        return $this->themeNames;
    }
}
