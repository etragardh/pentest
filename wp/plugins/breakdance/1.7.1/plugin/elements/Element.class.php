<?php

namespace Breakdance\Elements;

class Element
{
    /**
     * @return string
     */
    public static function defaultTag()
    {
        return 'div';
    }

    /**
     * @return string
     * @psalm-suppress InvalidFalsableReturnType
     */
    public static function uiIcon()
    {
        /** @psalm-suppress FalsableReturnStatement */
        return false;
    }

    /**
     * @return string[]
     */
    public static function tagOptions()
    {
        return [];
    }

    /**
     * @return string|false
     */
    public static function tagControlPath()
    {
        return false;
    }

    /**
     * @return string
     */
    public static function template()
    {
        return '%%CHILDREN%%';
    }

    /**
     * @return string
     * @psalm-suppress InvalidFalsableReturnType
     */
    public static function slug()
    {
        /** @psalm-suppress FalsableReturnStatement */
        return false;
    }

    /**
     * @return string|false
     */
    public static function name()
    {
        return false;
    }

    /**
     * @return string|false
     */
    public static function className()
    {
        return false;
    }

    /**
     * @return string
     */
    public static function category()
    {
        return 'other';
    }

    /**
     * @return mixed|false
     */
    public static function badge()
    {
        return false;
    }

    /**
     * @return Control[]
     */
    public static function contentControls()
    {
        return [];
    }

    /**
     * @return Control[]
     */
    public static function designControls()
    {
        return [];
    }

    /**
     * @return Control[]
     */
    public static function settingsControls()
    {
        return [];
    }

    /**
     * @return DynamicPropertyPath[]|false|null
     */
    public static function dynamicPropertyPaths()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public static function defaultProperties()
    {
        return false;
    }

    /**
     * @return false|string[]
     */
    public static function defaultChildren()
    {
        return false;
    }

    /**
     * @return ElementAttribute[]|false
     */
    public static function attributes()
    {
        return false;
    }

    /**
     * @return string
     */
    public static function defaultCSS()
    {
        return '';
    }

    /**
     * @return string
     */
    public static function cssTemplate()
    {
        return '';
    }

    /**
     * @return ElementDependenciesAndConditions[]|false
     */
    public static function dependencies()
    {
        return false;
    }

    /**
     * @return ElementSettings|false
     */
    public static function settings()
    {
        return false;
    }

    /**
     * @return BuilderActions|false
     */
    public static function actions()
    {
        return false;
    }

    /**
     * @return array{location:string,cssProperty:string,affectedPropertyPath:string}[]|false
     */
    public static function spacingBars()
    {
        return false;
    }

    /**
     * @return mixed
     */
    public static function nestingRule()
    {
        return ['type' => 'final'];
    }

    /**
     * @return string
     */
    public static function tag()
    {
        return "div";
    }

    /**
     * @param mixed $propertiesData
     * @param mixed $parentPropertiesData
     * @param bool $isBuilder
     * @param int $repeaterItemNodeId
     * @return string
     */
    public static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
    {
        ob_start();

        $reflection = new \ReflectionClass(get_called_class());
        /** @var ElementSettings|false $settings */
        $settings = $reflection->getMethod('settings')->invoke(null);

        if (!hasRequiredPluginsAndTheyAreAvailable($settings)) {
            /** @var string $elementName */
            $elementName = $reflection->getMethod('name')->invoke(null);
            $requiredPlugins = $settings['requiredPlugins'] ?? [];

            return getRequiredPluginsNotActiveSsrMessage($requiredPlugins, $elementName);
        }

        $element_class_file_path = $reflection->getFileName();
        $ssr_file_path = $element_class_file_path
            ? rtrim(dirname($element_class_file_path), '\\/') . DIRECTORY_SEPARATOR . 'ssr.php'
            : null;

        if ($ssr_file_path) {
            // Include the file if it exists without blowing up
            /**
             * @psalm-suppress UnresolvableInclude
             */
            if(!@include $ssr_file_path) {
                ob_end_flush();
                return getSsrErrorMessage("This element doesn't have a ssr.php file");
            }
            /*
            NOTE - if we don't use @ here, we'll get errors during SSR such as:
            https://github.com/soflyy/breakdance/issues/6099 - $post isn't set, so a 3rd party plugin
            trying to read $post->ID explodes. We can address this
            by not triggering SSR when generating the CSS cache.
            */

            return ob_get_clean();
        } else {
            ob_end_flush();
            return getSsrErrorMessage("Unable to determine element class file location");
        }
    }

    /**
     * @return false|mixed
     */
    public static function addPanelRules()
    {
        return false;
    }

    public static function requiredPlugins()
    {
    }

    /**
     * @return boolean
     */
    public static function experimental()
    {
        return false;
    }

    /**
     * @return int
     */
    public static function order()
    {
        return 0;
    }

    /**
     * @return array{name:string,template:string}[]|false
     */
    public static function additionalClasses()
    {
        return false;
    }

    /**
     * @return mixed|false
     */
    public static function projectManagement()
    {
        return false;
    }

    /**
     * @return string[]|false
     */
    public static function propertyPathsToWhitelistInFlatProps()
    {
        return false;
    }

    /**
     * @return string[]|false
     */
    public static function propertyPathsToSsrElementWhenValueChanges()
    {
        return false;
    }
}
