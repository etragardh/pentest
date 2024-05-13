<?php

namespace Breakdance\DynamicData;

class FeaturedCustomFieldImage extends ImageField
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
        return 'Featured Image';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'featured_custom_image';
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
        $attachmentId = get_post_thumbnail_id(get_the_ID());

        $url = get_post_meta($attachmentId, $attributes['key'], true);
        return ImageData::fromUrl($url);
    }
}
