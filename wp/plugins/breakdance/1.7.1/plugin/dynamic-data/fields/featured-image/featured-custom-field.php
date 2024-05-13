<?php

namespace Breakdance\DynamicData;

class FeaturedCustomField extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Custom Field';
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
        return 'featured_custom';
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
        return ['string', 'url', 'google_map'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        if (empty($attributes['key'])) {
            return StringData::emptyString();
        }
        $attachmentId = get_post_thumbnail_id(get_the_ID());
        $customFieldValue = get_post_meta($attachmentId, $attributes['key'], true);

        return StringData::fromString($customFieldValue);
    }
}
