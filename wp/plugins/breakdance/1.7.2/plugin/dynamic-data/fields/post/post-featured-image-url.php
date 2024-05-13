<?php


namespace Breakdance\DynamicData;

class PostFeaturedImageURL extends StringField
{

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Featured Image (URL)';
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
        return 'post_featured_image_url';
    }

    /**
     * @inheritDoc
     */
    public function returnTypes()
    {
        return ['url'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): StringData
    {
        $attachment_id = get_post_thumbnail_id(get_the_ID());
        $attachment_url =  wp_get_attachment_image_url($attachment_id, 'full') ?? '';

        return StringData::fromString($attachment_url);
    }
}
