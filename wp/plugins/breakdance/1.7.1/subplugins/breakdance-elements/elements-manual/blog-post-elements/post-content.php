<?php

namespace EssentialElements;

use function Breakdance\DynamicData\breakdanceDoShortcode;

class PostContent extends \EssentialElements\RichText
{
    static function name()
    {
        return 'Post Content';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        return [];
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
        echo breakdanceDoShortcode("[breakdance_dynamic field='post_content']");
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
        return 1200;
    }


    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['none'];
    }

}
