<?php

namespace EssentialElements;

use function Breakdance\DynamicData\breakdanceDoShortcode;
use function Breakdance\Elements\control;

class ArchiveTitle extends \EssentialElements\Heading
{
    static function name()
    {
        return 'Archive Title';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        $controls = parent::contentControls();

        array_splice($controls[0]['children'], 0, 1);

        $controls[0]['children'][] = control(
            "disable_prefix",
            "Disable Prefix",
            [
                'type' => 'toggle'
            ]
        );

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

        if ($propertiesData['content']['content']['disable_prefix'] ?? false) {
            add_filter('get_the_archive_title_prefix', '__return_false');
        }

        echo breakdanceDoShortcode("[breakdance_dynamic field='archive_title']");

        if ($propertiesData['content']['content']['disable_prefix'] ?? false) {
            remove_filter('get_the_archive_title_prefix', '__return_false');
        }

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
        return 2000;
    }


    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content.content.disable_prefix'];
    }

}
