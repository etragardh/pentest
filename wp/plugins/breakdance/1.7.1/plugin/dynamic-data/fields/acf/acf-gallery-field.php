<?php

namespace Breakdance\DynamicData;

class AcfGalleryField extends GalleryField
{

    public array $field;

    public function __construct($field)
    {
        $this->field = $field;
    }

    /**
     * @inheritDoc
     */
    public function label()
    {
        return $this->field['label'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'ACF';
    }

    /**
     * @inheritDoc
     */
    public function subcategory()
    {
        return $this->field['group'];
    }

    /**
     * @inheritDoc
     */
    public function slug()
    {
        return 'acf_gallery_field_' . $this->field['key'];
    }

    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return AcfField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): GalleryData
    {
        $imageIds = AcfField::getValue($this->field, false);
        if (empty($imageIds)) {
            return new GalleryData();
        }
        $gallery = new GalleryData();
        $gallery->images = array_map(static function ($imageId) {
            return ImageData::fromAttachmentId($imageId);
        }, $imageIds);

        return $gallery;
    }
}
