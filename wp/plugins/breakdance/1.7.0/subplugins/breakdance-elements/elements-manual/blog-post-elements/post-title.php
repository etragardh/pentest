<?php

namespace EssentialElements;

use function Breakdance\DynamicData\breakdanceDoShortcode;

class PostTitle extends \EssentialElements\Heading
{
    static function name()
    {
        return 'Post Title';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        $controls = parent::contentControls();

        array_splice($controls[0]['children'], 0, 1);

        return $controls;
    }

    static function template()
    {
        return '%%SSR%%';
    }

    /**
     * @param mixed $propertiesData
     * @param mixed $parentPropertiesData
     * @param bool $isBuilder
     * @param int $repeaterItemNodeId
     * @return string
     */
    static function ssr($propertiesData, $parentPropertiesData = [], $isBuilder = false, $repeaterItemNodeId = null)
    {
        ob_start();
        echo breakdanceDoShortcode("[breakdance_dynamic field='post_title']");
        return ob_get_clean();
    }

        
    static function attributes()
    {
        return [];
    }

    static function category()
    {
        return "dynamic";
    }

    static function order()
    {
        return 1000;
    }

    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['none'];
    }
}
