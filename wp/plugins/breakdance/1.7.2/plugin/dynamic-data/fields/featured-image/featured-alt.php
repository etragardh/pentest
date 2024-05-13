<?php

namespace Breakdance\DynamicData;

class FeaturedAlt extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Featured Alt';
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
        return 'featured_alt';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $data = new StringData();
        $attachment_id = get_post_thumbnail_id(get_the_ID());
        $data->value = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        return $data;
    }
}
