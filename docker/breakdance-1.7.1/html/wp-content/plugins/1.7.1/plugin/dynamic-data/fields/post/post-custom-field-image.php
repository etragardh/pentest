<?php

namespace Breakdance\DynamicData;

class PostCustomFieldImage extends ImageField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field (Image URL)';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Post';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_custom_field_image';
    }

    /**
     * @inheritDoc
     */
    public function controls()
    {
        return [
            \Breakdance\Elements\control('key', 'Key', [
                'type' => 'text',
                'layout' => 'vertical',
            ]),
        ];
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return [
            'image_url'
        ];
    }
    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        if (empty($attributes['key'])) {
            return ImageData::emptyImage();
        }

        $url = get_post_meta(get_the_ID(), $attributes['key'], true);
        return ImageData::fromUrl($url);
    }
}
