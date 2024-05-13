<?php

namespace Breakdance\DynamicData;

class MetaboxGalleryField extends GalleryField
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
        return $this->field['name'];
    }

    /**
     * @inheritDoc
     */
    public function category()
    {
        return 'Metabox';
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
        $isSettingsPage = $this->field['is_settings_page'] ?? false;
        if ($isSettingsPage) {
            return 'metabox_gallery_' . $this->field['settings_page'] . '_' . $this->field['id'];
        }
        return 'metabox_gallery_' . $this->field['id'];
    }

    /**
     * @param string $postType
     * @return bool
     */
    public function availableForPostType($postType)
    {
        return MetaboxField::isFieldAvailableForPostType($this->field, $postType);
    }

    /**
     * @inheritDoc
     */
    public function handler($attributes): GalleryData
    {
        $gallery = new GalleryData();
        $images = MetaboxField::getValue($this->field);

        if (empty($images)) {
            return $gallery;
        }

        $imageIds = array_keys($images);
        $gallery->images = array_map(static function ($imageId) {
            return ImageData::fromAttachmentId($imageId);
        }, $imageIds);

        return $gallery;
    }
}
