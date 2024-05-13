<?php

namespace Breakdance\DynamicData;

class CurrentUserCustomFieldOembed extends OembedField
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
        return 'Current User';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'user_custom_field_oembed';
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
        $userId = get_current_user_id();
        if (empty($attributes['key']) || $userId === 0) {
            return OembedData::emptyOembed();
        }


        $url = get_user_meta($userId, $attributes['key'], true);
        return OembedData::fromOembedUrl($url);
    }
}
