<?php

namespace Breakdance\DynamicData;

class FeaturedCaption extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Featured Caption';
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
        return 'featured_caption';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $data = new StringData();
        $attachment_id = get_post_thumbnail_id(get_the_ID());
        if (!$attachment_id) {
            $data->value = '';
            return $data;
        }
        $attachment = get_post($attachment_id);
        $data->value = $attachment->post_excerpt;
        return $data;
    }
}
