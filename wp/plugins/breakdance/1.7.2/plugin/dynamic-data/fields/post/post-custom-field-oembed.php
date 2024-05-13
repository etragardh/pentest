<?php

namespace Breakdance\DynamicData;

class PostCustomFieldOembed extends OembedField
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
        return 'Post';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'post_custom_field_oembed';
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

        $url = get_post_meta(get_the_ID(), $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
