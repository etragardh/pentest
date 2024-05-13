<?php

namespace Breakdance\DynamicData;

class FeaturedTitle extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Featured Title';
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
        return 'featured_title';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $data = new StringData;
        $attachment_id = get_post_thumbnail_id(get_the_ID());
        $data->value = get_the_title($attachment_id);

        return $data;
    }
}
