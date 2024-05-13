<?php

namespace EssentialElements;

use Breakdance\DynamicData\ImageData;
use function Breakdance\DynamicData\breakdanceDoShortcode;
use function Breakdance\Elements\c;
use function Breakdance\Render\renderDynamicDataInProps;

class PostFeaturedImage extends \EssentialElements\Image
{
    static function name()
    {
        return 'Post Featured Image';
    }

    static function slug()
    {
       return get_class();
    }

    static function contentControls()
    {
        $controls = parent::contentControls();

        $controls[0]['children'][0] = c(
            "fallback_image",
            "Fallback Image",
            [],
            ['type' => 'wpmedia', 'layout' => 'vertical'],
            false,
            false,
            [],
        );

        $controls[0]['children'][1] =  c(
            "size",
            "Image Size",
            [],
            ['type' => 'media_size_dropdown', 'layout' => 'vertical', 'mediaSizeOptions' => ['imagePropertyPath' => 'content.content.fallback_image']],
            false,
            false,
            [],
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

        $element = "\EssentialElements\\Image";

        $featuredImage = breakdanceDoShortcode("[breakdance_dynamic field='post_featured_image']");

        $fallbackImage = $propertiesData['content']['content']['fallback_image'] ?? ImageData::emptyImage();

        $postHasFeaturedImage = array_key_exists('url', $featuredImage) && !empty($featuredImage['url']);

        $p = renderDynamicDataInProps(
            array_merge_recursive(
                $propertiesData,
                [
                    'content' => [
                        'content' => [
                            'image' => $postHasFeaturedImage ? $featuredImage : $fallbackImage
                        ]
                    ]
                ]
            ),
            $element::dynamicPropertyPaths()
        );

        echo \Breakdance\Render\getInnerHtml(
            $element,
            $p['propsWithDynamicDataRendered'],
            "",
            []
        );

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
        return 1300;
    }


    static function propertyPathsToSsrElementWhenValueChanges()
    {
        return ['content'];
    }
}
