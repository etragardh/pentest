<?php

namespace Breakdance\DynamicData;

class PostImageAttachments extends GalleryField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Image Attachments';
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
        return 'post_image_attachments';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): GalleryData
    {
        $attachedImages = get_attached_media('image', get_the_ID());
        $gallery = new GalleryData();
        $gallery->images = array_map(static function($attachment) {
            return ImageData::fromAttachmentId($attachment->ID);
        }, $attachedImages);

        return $gallery;
    }
}
