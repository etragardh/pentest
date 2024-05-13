<?php

namespace Breakdance\DynamicData;

class FeaturedCustomFieldOembed extends OembedField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field (Oembed URL)';
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
        return 'featured_custom_oembed';
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
    public function handler($attributes): OembedData
    {
        if (empty($attributes['key'])) {
            return OembedData::emptyOembed();
        }
        $attachmentId = get_post_thumbnail_id(get_the_ID());

        $url = get_post_meta($attachmentId, $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
