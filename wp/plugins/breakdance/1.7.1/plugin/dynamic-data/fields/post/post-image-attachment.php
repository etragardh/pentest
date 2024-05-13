<?php

namespace Breakdance\DynamicData;

class PostImageAttachment extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Post Image Attachment';
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
        return 'post_image_attachment';
    }

    /**
     * @inheritDoc
     */
    public function controls() {
        return [
            \Breakdance\Elements\control(
                'image_index',
                'Image Index',
                [
                    'type' => 'number',
                    'layout' => 'vertical',
                ]
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $attachedImages = array_values(get_attached_media('image', get_the_ID()));
        if (empty($attachedImages)) {
            ImageData::emptyImage();
        }
        $imageIndex = $attributes['image_index'] ?? 0;
        if (isset($attachedImages[$imageIndex])) {
            return ImageData::fromAttachmentId($attachedImages[$imageIndex]->ID);
        }

        return ImageData::emptyImage();
    }
}
