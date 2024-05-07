<?php

namespace Breakdance\DynamicData;

class AcfPostFeaturedImage extends AcfImageField
{
    /**
     * @inheritDoc
     */
    public function label()
    {
        return $this->field['label'] . ' Featured Image';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'acf_post_image_' . $this->field['key'];
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        $postId = AcfField::getValue($this->field);
        if (!$postId) {
            return ImageData::emptyImage();
        }
        $attachmentId = get_post_thumbnail_id($postId);

        return ImageData::fromAttachmentId($attachmentId);
    }
}
