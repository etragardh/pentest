<?php

namespace Breakdance\DynamicData;

class WoocommerceCategoryImage extends ImageField {

    /**
     * @inheritDoc
     */
    public function label()
    {
        return 'Category Image';
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Archive';
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'woocommerce_category_image';
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): ImageData
    {
        global $post;
        $termId = $post->term_id ?? null;
        if (!$termId) {
            return ImageData::emptyImage();
        }

        $imageId = get_term_meta($termId, 'thumbnail_id', true);

        if (!$imageId) {
            return ImageData::emptyImage();
        }

        return ImageData::fromAttachmentId($imageId);
    }
}

